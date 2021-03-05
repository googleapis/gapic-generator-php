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

namespace Google\Generator;

use Google\Generator\Collections\Vector;
use Google\Protobuf\Compiler\CodeGeneratorRequest;
use Google\Protobuf\Compiler\CodeGeneratorResponse;
use Google\Protobuf\Compiler\CodeGeneratorResponse\Feature;
use Google\Protobuf\Compiler\CodeGeneratorResponse\File;
use Google\Protobuf\Internal\FileDescriptorProto;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

function showUsageAndExit()
{
    print("Invalid arguments. Expect:\n");
    print("  --descriptor <path> The path to the proto descriptor file.\n");
    print("  --package <string> The proto package to generate.\n");
    print("  --output <path> [Optional] The output directory.\n");
    print("  --grpc_service_config <path> [Optional] The client-side gRPC service config.\n");
    print("  --gapic_yaml <path> [Optional] The gapic yaml.\n");
    print("  --service_yaml <path> [Optional] The service yaml.\n");
    print("\n");
    exit(1);
}

// The monolithic generator appears to be fixed to use 2020, so fixing the micro-generator to 2020
// allows the comparison-based integration tests to run.
// TODO: Use the current year, not fixed at 2020. Make the monolith use current year.
$year = 2020;
// $year = (int)date('Y');

// When running as a protoc plugin, an optional root directory may be passed in to set the
// root location of the side-loaded configuration files:
// - grpc service config json
// - service yaml
// - gapic yaml
// This is required when running under bazel, as the PHP working directory has to be different to the
// bazel working directory; but the side-loaded config files are passed relative to the bazel working
// directory. Passing the bazel working directory as side_loaded_root_dir allows these files to be
// loaded successfully.
// This is because PHP uses the same working directory for its own code, and application-level
// file loading.
$opts = getopt('', ['side_loaded_root_dir:']);
$sideLoadedRootDir = isset($opts['side_loaded_root_dir']) ? rtrim($opts['side_loaded_root_dir'], '/') : null;

// argc <= 3 to allow both "--side_loaded_root_dir=path" and "--side_loaded_root_dir path"
if ($argc === 1 || (!is_null($sideLoadedRootDir) && $argc <= 3)) {
    // No args or just side_loaded_root_dir arg, probably being called from protoc.
    // However, timeout after a couple of seconds of no data in stdin,
    // in case this has been run interactively with zero args.
    $protocRequest = '';
    while (true) {
        $read = [STDIN];
        $write = $except = [];
        if (!stream_select($read, $write, $except, 2)) {
            // Timeout after 2 seconds, probably being run interactively.
            showUsageAndExit();
        }
        $read = fread(STDIN, 32 * 1024);
        if (!$read) {
            // End of stream, reading done.
            break;
        }
        $protocRequest .= $read;
    }
    $genRequest = new CodeGeneratorRequest();
    $genRequest->mergeFromString($protocRequest);
    if ($genRequest->serializeToString() === (new CodeGeneratorRequest())->serializeToString()) {
        // Nothing passed in; probably run from cmd-line and ^D (EOF) sent.
        showUsageAndExit();
    }
    $genResponse = new CodeGeneratorResponse();
    $genResponse->setSupportedFeatures(Feature::FEATURE_PROTO3_OPTIONAL);
    try {
        $fileDescs = Vector::new($genRequest->getProtoFile())
            ->map(function ($bytes) {
                $desc = new FileDescriptorProto();
                $desc->mergeFromString($bytes);
                return $desc;
            });
        $filesToGen = Vector::new($genRequest->getFileToGenerate());
        $opts = Vector::new(explode(',', $genRequest->getParameter()))
            ->filter(fn ($x) => !is_null($x) && $x !== '')
            ->map(fn ($x) => explode('=', $x, 2))
            ->toArray(fn ($x) => $x[0], fn ($x) => $x[1]);
        [$grpcServiceConfig, $gapicYaml, $serviceYaml] = readOptions($opts, $sideLoadedRootDir);
        $files = CodeGenerator::generate($fileDescs, $filesToGen, $year, $grpcServiceConfig, $gapicYaml, $serviceYaml);
        $files = Vector::new($files)->map(function ($fileData) {
            [$relativeFilename, $fileContent] = $fileData;
            $file = new File();
            $file->setName($relativeFilename);
            $file->setContent($fileContent);
            return $file;
        });
        $genResponse->setFile($files->toArray());
    } catch (\Exception $e) {
        $genResponse->setError("Error from PHP gapic generator:\n" . $e->getMessage());
    }
    fwrite(STDOUT, $genResponse->serializeToString());
} else {
    // Read command-line args, being run interactively from cmd-line.
    $opts = getopt('', ['descriptor:', 'package:', 'output:', 'grpc_service_config:', 'gapic_yaml:', 'service_yaml:']);
    if (!isset($opts['descriptor']) || !isset($opts['package'])) {
        showUsageAndExit();
    }
    $descBytes = file_get_contents($opts['descriptor']);
    $package = $opts['package'];
    $outputDir = $opts['output'];
    [$grpcServiceConfig, $gapicYaml, $serviceYaml] = readOptions($opts);

    // Generate PHP code.
    $files = CodeGenerator::generateFromDescriptor($descBytes, $package, $year, $grpcServiceConfig, $gapicYaml, $serviceYaml);

    if (is_null($outputDir)) {
        // TODO: Remove printout; only save files to the specified output path.
        foreach ($files as [$relativeFilename, $fileContent]) {
            print("File: '{$relativeFilename}':\n");
            print($fileContent . "\n");
        }
    } else {
        foreach ($files as [$relativeFilename, $fileContent]) {
            $path = $outputDir . '/' . $relativeFilename;
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $fileContent);
        }
    }
}

function readOptions($opts, $sideLoadedRootDir = null)
{
    $makePath = function ($path) use ($sideLoadedRootDir) {
        if (strlen($path) > 0 && $path[0] === '/') {
            if (!is_null($sideLoadedRootDir)) {
                throw new \Exception("Cannot use --side_loaded_root_dir with absolute config paths");
            }
            return $path;
        } else {
            return is_null($sideLoadedRootDir) ? $path : ($sideLoadedRootDir . '/' . $path);
        }
    };
    if (isset($opts['grpc_service_config'])) {
        $grpcServiceConfigPath = $makePath($opts['grpc_service_config']);
        if (!file_exists($grpcServiceConfigPath)) {
            throw new \Exception("Specified grpc_service_config file does not exist: '{$grpcServiceConfigPath}'");
        }
        $grpcServiceConfig = file_get_contents($grpcServiceConfigPath);
    } else {
        $grpcServiceConfig = null;
    }

    if (isset($opts['gapic_yaml'])) {
        $gapicYamlPath = $makePath($opts['gapic_yaml']);
        if (!file_exists($gapicYamlPath)) {
            throw new \Exception('Specified gapi_yaml file does not exist.');
        }
        $gapicYaml = file_get_contents($gapicYamlPath);
    } else {
        $gapicYaml = null;
    }

    if (isset($opts['service_yaml'])) {
        $serviceYamlPath = $makePath($opts['service_yaml']);
        if (!file_exists($serviceYamlPath)) {
            throw new \Exception('Specified service_yaml file does not exist.');
        }
        $serviceYaml = file_get_contents($serviceYamlPath);
    } else {
        $serviceYaml = null;
    }

    return [$grpcServiceConfig, $gapicYaml, $serviceYaml];
}
