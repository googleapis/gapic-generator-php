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

class Invoker
{
    public static function invoke(string $protoPath)
    {
        $rootDir = __DIR__ . '/..';

        // Build the proto descriptor.
        $protoc = "{$rootDir}/tools/protoc";
        $descRes = tmpfile();
        $descFilename = stream_get_meta_data($descRes)['uri'];
        $descFilename = "/tmp/desc.desc";
        $protobuf = "{$rootDir}/protobuf/src/";
        $googleapis = "{$rootDir}/googleapis/";
        $input = "{$rootDir}/{$protoPath}";
        $protocCmdLine = "{$protoc} --include_imports --include_source_info -o {$descFilename} " .
            "-I {$googleapis} -I {$protobuf} -I {$rootDir} {$input} 2>&1";
        static::execCmd($protocCmdLine, 'protoc');

        $rootOutDir = sys_get_temp_dir() . '/php-gapic-' . mt_rand(0, (int)1e8);
        mkdir($rootOutDir);
        try {
            $package = str_replace('-', '', 'testing.' . basename($protoPath, '.proto'));

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
            $monoConfigBase = $rootDir . '/' . dirname($protoPath) . '/' . basename($protoPath, '.proto');
            $monoGapicYaml = $monoConfigBase . '_gapic.yaml';
            if (file_exists($monoGapicYaml)) {
                $monoCmdLine .= " --gapic_yaml {$monoGapicYaml}";
            }
            $monoServiceConfig = $monoConfigBase . '_service.yaml';
            if (file_exists($monoServiceConfig)) {
                $monoCmdLine .= " --service_yaml {$monoServiceConfig}";
            }
            static::execCmd("cd {$monoDir}; {$monoCmdLine}", 'mono');

            // Run the micro-generator.
            $microMain = "{$rootDir}/src/Main.php";
            $microOutDir = "{$rootOutDir}/micro";
            $microCmdLine = "php {$microMain} --descriptor {$descFilename} --package {$package} --output {$microOutDir} 2>&1";
            static::execCmd($microCmdLine, 'micro');

            // Read all files in output dirs.
            $mono = static::readFiles($monoOutDir);
            $micro = static::readFiles($microOutDir);

            return [
                'mono' => $mono,
                'micro' => $micro,
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
