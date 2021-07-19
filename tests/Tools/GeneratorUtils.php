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

class GeneratorUtils
{
    /**
     *  Runs the generator and returns the produced sources.
     *
     *  @param string protoPath path to the proto under ProtoTests.
     *  @param ?string package
     *  @param ?string transport
     *
     * @return string[] maps the relative file path to the string contents of the gtenerated code.
     */
    public static function generateFromProto(string $protoPath, ?string $package = null, ?string $transport = null)
    {
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

        $licenseYear = 2020; // Avoid updating tests all the time.
        $generateGapicMetadata = true;
        $codeIterator = CodeGenerator::generateFromDescriptor(
            $descBytes,
            $package,
            $transport,
            $generateGapicMetadata,
            $grpcServiceConfigJson,
            $gapicYaml,
            $serviceYaml,
            $licenseYear
        );
        return $codeIterator;
    }
}
