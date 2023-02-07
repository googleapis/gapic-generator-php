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
use FilesystemIterator;
use Google\Generator\Tests\Tools\GeneratorUtils;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ProtoTest extends TestCase
{
    private function runProtoTest(
        string $protoPath,
        ?string $package = null,
        ?string $transport = null,
        bool $generateSnippets = true
    ): void {
        $codeIterator = GeneratorUtils::generateFromProto(
            $protoPath,
            $package,
            $transport,
            $generateSnippets
        );
        $files = iterator_to_array(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    __DIR__ . '/' . dirname($protoPath) . '/out/',
                    FilesystemIterator::SKIP_DOTS
                )
            )
        );

        foreach ($codeIterator as [$relativeFilename, $code]) {
            $filename = __DIR__ . '/' . dirname($protoPath) . '/out/' . $relativeFilename;
            // Check "expected-code" file exists, then compare generated code against expected code.
            // TODO: Add ability to check partial files.
            $this->assertTrue(file_exists($filename), "Expected code file missing: '{$filename}'");
            $expectedCode = file_get_contents($filename);
            if (trim($expectedCode) !== 'IGNORE' && trim($expectedCode) !== '<?php // IGNORE') {
                $this->assertEquals($expectedCode, $code, $filename);
            }
            unset($files[$filename]);
        }

        // Ensure all files have been generated.
        $this->assertTrue(
            empty($expectedGeneratedFilenameEndings),
            "Expected files not generated for files ending in " . implode(",", $expectedGeneratedFilenameEndings)
        );

        $this->assertEmpty(
            $files,
            'The following expected files were not generated: ' .
            print_r(array_keys($files), true)
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

    public function testBasicOneof(): void
    {
        $this->runProtoTest('BasicOneof/basic-oneof.proto');
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

    public function testRoutingHeaders(): void
    {
        $this->runProtoTest('RoutingHeaders/routing-headers.proto');
    }

    public function testDeprecatedService(): void
    {
        $this->runProtoTest('DeprecatedService/deprecated_service.proto');
    }

    public function testBasicDiregapic(): void
    {
        $this->runProtoTest('BasicDiregapic/library_rest.proto', 'google.example.library.v1', 'rest');
    }

    public function testResourceNames(): void
    {
        $this->runProtoTest('ResourceNames/resource-names.proto');
    }

    public function testCustomLro(): void
    {
        $this->runProtoTest('CustomLro/custom_lro.proto', 'testing.customlro', 'rest');
    }

    public function testDisableSnippetGeneration(): void
    {
        $this->runProtoTest(
            'DisableSnippets/disable_snippets.proto',
            'testing.disablesnippets',
            null,
            false
        );
    }
}
