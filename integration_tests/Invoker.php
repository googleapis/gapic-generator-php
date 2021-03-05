<?php
/*
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
declare(strict_types=1);

namespace Google\Generator\IntegrationTests;

use Google\Generator\Collections\Vector;

class Invoker
{
    public static function invoke(
        string $protoPath,
        ?string $package = null,
        ?string $gapicYaml = null,
        ?string $serviceYaml = null,
        ?string $grpcServiceConfig = null
    ) {
        $rootDir = realpath(__DIR__ . '/..');

        // Build the proto descriptor.
        $protoc = "{$rootDir}/tools/protoc";
        $descRes = tmpfile();
        $descFilename = stream_get_meta_data($descRes)['uri'];
        $protobuf = "{$rootDir}/protobuf/src/";
        $googleapis = "{$rootDir}/googleapis/";
        $input = Vector::new(explode(' ', $protoPath)) // Assumes paths don't contain spaces
            ->map(fn($path) => "{$rootDir}/{$path}")
            ->join(' ');
        $protocCmdLinePrefix = "{$protoc} --include_imports --include_source_info --experimental_allow_proto3_optional " .
            "-o {$descFilename}  -I {$googleapis} -I {$protobuf} -I {$rootDir}";
        static::execCmd($protocCmdLinePrefix . " {$input} 2>&1", 'protoc');

        $rootOutDir = sys_get_temp_dir() . '/php-gapic-' . mt_rand(0, (int)1e8);
        mkdir($rootOutDir);
        try {
            $package = $package ?? str_replace('-', '', 'testing.' . basename($protoPath, '.proto'));
            $monoConfigBase = strpos($protoPath, '*') !== false ? null : $rootDir . '/' . dirname($protoPath) . '/' . basename($protoPath, '.proto');

            // Determine locations of config files.
            $gapicYamlArg = is_null($gapicYaml) ? $monoConfigBase . '_gapic.yaml' : $rootDir . '/' . $gapicYaml;
            $serviceConfigArg = is_null($serviceYaml) ? $monoConfigBase . '_service.yaml' : $rootDir . '/' . $serviceYaml;
            $grpcServiceConfigArg = is_null($grpcServiceConfig) ?
                $rootDir . '/' . dirname($protoPath) . 'grpc-service-config.json' :
                $rootDir . '/' . $grpcServiceConfig;

            // Run the monolithic generator.
            $monoDir = "{$rootDir}/gapic-generator";
            $monoBuildDir = "{$monoDir}/build";
            if (!file_exists($monoBuildDir)) {
                static::execCmd("cd {$monoDir}; ./gradlew fatJar", 'mono-gradle');
            }
            $monoOutDir = "{$rootOutDir}/mono";
            $monoCmdLine = "java " .
                '-cp build/libs/gapic-generator-2.7.0-fatjar.jar:build/libs/gapic-generator-latest-fatjar.jar ' .
                'com.google.api.codegen.GeneratorMain ' .
                'GAPIC_CODE ' .
                "--descriptor_set {$descFilename} " .
                "--package {$package} " .
                '--language php ' .
                "-o {$monoOutDir}";
            if (file_exists($gapicYamlArg)) {
                $monoCmdLine .= " --gapic_yaml {$gapicYamlArg}";
            }
            if (file_exists($serviceConfigArg)) {
                $monoCmdLine .= " --service_yaml {$serviceConfigArg}";
            }
            if (file_exists($grpcServiceConfigArg)) {
                $monoCmdLine .= " --grpc_service_config {$grpcServiceConfigArg}";
            }
            static::execCmd("cd {$monoDir}; {$monoCmdLine} 2>&1", 'mono');

            // Run the micro-generator via protoc.
            $microProtocOutDir = "{$rootOutDir}/micro_protoc";
            mkdir($microProtocOutDir); // protoc requires this directory to already exist.
            $protocOpts = [];
            if (file_exists($gapicYamlArg)) {
                $protocOpts[] = "gapic_yaml={$gapicYamlArg}";
            }
            if (file_exists($serviceConfigArg)) {
                $protocOpts[] = "service_yaml={$serviceConfigArg}";
            }
            if (file_exists($grpcServiceConfigArg)) {
                $protocOpts[] = "grpc_service_config={$grpcServiceConfigArg}";
            }
            // This matches how the bazel invocation of protoc provides options to the plugin.
            $protocOpts = count($protocOpts) > 0 ? implode(',', $protocOpts) . ':' : '';
            $protocMicroCmdLine = $protocCmdLinePrefix .
                " --plugin=protoc-gen-gapic={$rootDir}/integration_tests/run_protoc_plugin.sh --gapic_out={$protocOpts}{$microProtocOutDir}";
            static::execCmd($protocMicroCmdLine . " {$input} 2>&1", 'protoc micro plugin');

            // Run the micro-generator standalone.
            $microMain = "{$rootDir}/src/Main.php";
            $microOutDir = "{$rootOutDir}/micro";
            $microCmdLine = "php {$microMain} --descriptor {$descFilename} --package {$package} --output {$microOutDir}";
            if (file_exists($gapicYamlArg)) {
                $microCmdLine .= " --gapic_yaml {$gapicYamlArg}";
            }
            if (file_exists($serviceConfigArg)) {
                $microCmdLine .= " --service_yaml {$serviceConfigArg}";
            }
            if (file_exists($grpcServiceConfigArg)) {
                $microCmdLine .= " --grpc_service_config {$grpcServiceConfigArg}";
            }
            static::execCmd($microCmdLine . ' 2>&1', 'micro');

            // Read all files in output dirs.
            $mono = static::readFiles($monoOutDir);
            $micro = static::readFiles($microOutDir);
            $microProtoc = static::readFiles($microProtocOutDir);

            return [
                'mono' => $mono,
                'micro' => $micro,
                'micro_protoc' => $microProtoc,
            ];
        } finally {
            // Delete temp directory
            static::delTree($rootOutDir);
        }
    }

    private static function execCmd(string $cmd, string $errorPrefix)
    {
        $output = [];
        $result = -1;
        exec($cmd, $output, $result);
        if ($result !== 0) {
            print("{$errorPrefix} error:\n" . implode("\n", $output) . "\n");
            exit(1);
        }
    }

    private static function readFiles(string $fullDirName, $prefix = ''): array
    {
        $result = [];
        foreach (scandir($fullDirName) as $fileName) {
            if ($fileName === '.' || $fileName === '..') {
                continue;
            }
            $fullFileName = $fullDirName . '/' . $fileName;
            if (is_dir($fullFileName)) {
                $result += static::readFiles($fullFileName, "{$prefix}/{$fileName}");
            } elseif (is_file($fullFileName)) {
                $result["{$prefix}/{$fileName}"] = file_get_contents($fullFileName);
            }
        }
        return $result;
    }

    private static function delTree(string $dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            is_dir($path) ? static::delTree($path) : unlink($path);
        }
        return rmdir($dir);
    }

}
