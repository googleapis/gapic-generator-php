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

namespace Google\Generator\Tests\Unit\ProtoTests;

use PHPUnit\Framework\TestCase;
use Google\Generator\Tests\Tools\GeneratorUtils;

final class ProtoTest extends TestCase
{
    private function runProtoTest(string $protoPath, ?string $package = null): void
    {
        $codeIterator = GeneratorUtils::generateFromProto($protoPath, $package);
        $expectedGeneratedFilenameEndings  =  array(
          'Client.php',
          'ClientTest.php',
          '_descriptor_config.php',
          '_rest_client_config.php',
          '_client_config.json'
        );
        foreach ($codeIterator as [$relativeFilename, $code]) {
            $filename = __DIR__ . '/' . dirname($protoPath) . '/out/' . $relativeFilename;
            // Check "expected-code" file exists, then compare generated code against expected code.
            // TODO: Add ability to check partial files.
            $this->assertTrue(file_exists($filename), "Expected code file missing: '{$filename}'");
            $expectedCode = file_get_contents($filename);
            if (trim($expectedCode) !== 'IGNORE' && trim($expectedCode) !== '<?php // IGNORE') {
                $this->assertEquals($expectedCode, $code);
            }
            $actualFileEnding = '';
            foreach ($expectedGeneratedFilenameEndings as $fileEnding) {
                if (substr($relativeFilename, -strlen($fileEnding)) === $fileEnding) {
                    $actualFileEnding = $fileEnding;
                }
            }
            if (($key = array_search($actualFileEnding, $expectedGeneratedFilenameEndings)) !== false) {
                unset($expectedGeneratedFilenameEndings[$key]);
            }
        }

        // Ensure all files ahve been generated.
        $this->assertTrue(
            empty($expectedGeneratedFilenameEndings),
            "Expected files not generated for files ending in " . implode(",", $expectedGeneratedFilenameEndings)
        );
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
