<?php
/*
 * Copyright 2021 Google LLC
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

namespace Google\Generator\Tests\Tools;

use Google\Generator\CodeGenerator;
use Google\Generator\Collections\Vector;
use Google\Protobuf\Internal\FileDescriptorSet;

class GeneratorUtils
{
    /**
     *  Runs the generator and returns the produced sources.
     *
     *  @param string $protoPath path to the proto under ProtoTests.
     *  @param ?string $package
     *  @param ?string $transport
     *  @param bool $generateSnippets
     *
     * @return string[] maps the relative file path to the string contents of the gtenerated code.
     */
    public static function generateFromProto(
        string $protoPath,
        ?string $package = null,
        ?string $transport = null,
        bool $generateSnippets = true,
    ) {
        // Conventions:
        // * The proto package is 'testing.<proto-name>'.
        // * The expected file contents are based in the same directory as the proto file.
        // * An optional grpc-service-config.json file may be in the same directory as the proto file.
        $descBytes = ProtoLoader::loadDescriptorBytes("ProtoTests/{$protoPath}");
        $baseName = basename($protoPath, '.proto');
        $package = $package ?? str_replace('-', '', "testing.{$baseName}");
        $protoDirName = dirname("ProtoTests/{$protoPath}");
        $grpcServiceConfigJson = ConfigLoader::loadConfig("{$protoDirName}/grpc-service-config.json");
        $gapicYaml = ConfigLoader::loadConfig("{$protoDirName}/{$baseName}_gapic.yaml");
        $serviceYaml = ConfigLoader::loadConfig("{$protoDirName}/{$baseName}_service.yaml");
        $licenseYear = 2022; // Avoid updating tests all the time.
        $generateGapicMetadata = true;
        $numericEnums = true;
        $codeIterator = CodeGenerator::generateFromDescriptor(
            $descBytes,
            $package,
            $transport,
            $generateGapicMetadata,
            $grpcServiceConfigJson,
            $gapicYaml,
            $serviceYaml,
            $numericEnums,
            $licenseYear,
            $generateSnippets,
        );
        return $codeIterator;
    }

    /**
     * Runs the generator for an integration test configuration and returns the produced sources.
     *
     * @param array $config Integration test configuration.
     *
     * @return array[] [0] (string) is relative path; [1] (string) is file content.
     */
    public static function generateIntegration(array $config): array
    {
        $protos = glob($config['protoDir'] . '/*.proto');
        $extraProtos = [
            'googleapis/google/cloud/common_resources.proto',
            'googleapis/google/cloud/location/locations.proto',
            'googleapis/google/iam/v1/iam_policy.proto',
            'googleapis/google/iam/v1/policy.proto',
            'googleapis/google/iam/v1/options.proto',
        ];
        $allProtos = array_values(array_unique(array_merge($protos, $extraProtos)));
        $filesToGenerate = array_map(fn ($p) => preg_replace('#^googleapis/#', '', $p), $allProtos);
        $descBytes = ProtoLoader::loadDescriptorBytesFromPaths($allProtos);
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $fileDescs = Vector::new($descSet->getFile());

        return CodeGenerator::generate(
            $fileDescs,
            Vector::new($filesToGenerate),
            $config['transport'] ?? 'grpc+rest',
            true,
            isset($config['grpcServiceConfig']) ? file_get_contents($config['grpcServiceConfig']) : null,
            isset($config['gapicYaml']) ? file_get_contents($config['gapicYaml']) : null,
            isset($config['serviceYaml']) ? file_get_contents($config['serviceYaml']) : null,
            $config['numericEnums'] ?? false,
            2026, // Avoid updating tests all the time.
            true,
        );
    }
}
