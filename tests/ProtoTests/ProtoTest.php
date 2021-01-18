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

namespace Google\Generator\Tests\ProtoTests;

use PHPUnit\Framework\TestCase;
use Google\Generator\CodeGenerator;
use Google\Generator\Collections\Vector;
use Google\Generator\Tests\ProtoTrait;

final class ProtoTest extends TestCase
{
    use ProtoTrait;

    private function runProtoTest(string $protoPath, ?string $package = null): void
    {
        // Conventions:
        // * The proto package is 'testing.<proto-name>'.
        // * The expected file contents are based in the same directory as the proto file.
        // * An optional grpc-service-config.json file may be in the same directory as the proto file.
        $descBytes = $this->loadDescriptorBytes("ProtoTests/{$protoPath}");
        $baseName = basename($protoPath, '.proto');
        $package = $package ?? str_replace('-', '', "testing.{$baseName}");
        $protoDirName = dirname("ProtoTests/{$protoPath}");
        $grpcServiceConfigJsonPath = "tests/{$protoDirName}/grpc-service-config.json";
        $grpcServiceConfigJson = file_exists($grpcServiceConfigJsonPath) ? file_get_contents($grpcServiceConfigJsonPath) : null;
        $gapicYamlPath = "tests/{$protoDirName}/{$baseName}_gapic.yaml";
        $gapicYaml = file_exists($gapicYamlPath) ? file_get_contents($gapicYamlPath) : null;
        $serviceYamlPath = "tests/{$protoDirName}/{$baseName}_service.yaml";
        $serviceYaml = file_exists($serviceYamlPath) ? file_get_contents($serviceYamlPath) : null;
        // Use the fixed year 2020 for test generation, so tests won't fail in the future.
        $codeIterator = CodeGenerator::GenerateFromDescriptor($descBytes, $package, 2020, $grpcServiceConfigJson, $gapicYaml, $serviceYaml);

        foreach ($codeIterator as [$relativeFilename, $code]) {
            $filename = __DIR__ . '/' . dirname($protoPath) . '/out/' . $relativeFilename;
            // Check "expected-code" file exists, then compare generated code against expected code.
            // TODO: Add ability to check partial files.
            $this->assertTrue(file_exists($filename), "Expected code file missing: '{$filename}'");
            $expectedCode = file_get_contents($filename);
            if (trim($expectedCode) !== 'IGNORE' && trim($expectedCode) !== '<?php // IGNORE') {
                $this->assertEquals($expectedCode, $code);
            }
        }
        // TODO: Check that all expected files are actually generated!
    }

    public function testBasic0(): void
    {
        $this->runProtoTest('Basic/basic.proto');
    }

    public function testBasicLro(): void
    {
        $this->runProtoTest('BasicLro/basic-lro.proto');
    }

    public function testBasicPaginated(): void
    {
        $this->runProtoTest('BasicPaginated/basic-paginated.proto');
    }

    public function testBidiStreaming(): void
    {
        $this->runProtoTest('BasicBidiStreaming/basic-bidi-streaming.proto');
    }

    public function testServerStreaming(): void
    {
        $this->runProtoTest('BasicServerStreaming/basic-server-streaming.proto');
    }

    public function testClientStreaming(): void
    {
        $this->runProtoTest('BasicClientStreaming/basic-client-streaming.proto');
    }

    public function testGrpcServiceConfig(): void
    {
        $this->runProtoTest('GrpcServiceConfig/grpc-service-config1.proto', 'testing.grpcserviceconfig');
    }
}
