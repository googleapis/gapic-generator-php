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
use Google\Generator\Utils\MigrationMode;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ProtoTest extends TestCase
{
    private function runProtoTest(
        string $protoPath,
        ?string $package = null,
        ?string $transport = null,
        bool $generateSnippets = true,
        string $migrationMode = MigrationMode::PRE_MIGRATION_SURFACE_ONLY
    ): void {
        $codeIterator = GeneratorUtils::generateFromProto(
            $protoPath,
            $package,
            $transport,
            $generateSnippets,
            $migrationMode
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
                $this->assertEquals($expectedCode, $code);
            }
            unset($files[$filename]);
        }

        $this->assertEmpty(
            $files,
            'The following expected files were not generated: ' .
            print_r(array_keys($files), true)
        );
    }

    public function testBasic0WithNewSurface(): void
    {
        // test generating the client with only the new surface (no v1 client, v2 samples)
        $this->runProtoTest('Basic/basic.proto', migrationMode: MigrationMode::NEW_SURFACE_ONLY);
    }

    public function testBasicLro(): void
    {
        $this->runProtoTest('BasicLro/basic-lro.proto');
    }

    public function testBasicOneof(): void
    {
        $this->runProtoTest('BasicOneof/basic-oneof.proto');
    }

    public function testBasicOneofNew(): void
    {
        $this->runProtoTest('BasicOneofNew/basic-oneof-new.proto', migrationMode: MigrationMode::NEW_SURFACE_ONLY);
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

    public function testRoutingHeadersWithMigrationSurface(): void
    {
        // test generating the client in migration mode (both v1 and v2 clients, but with v2 samples)
        $this->runProtoTest('RoutingHeaders/routing-headers.proto', migrationMode: MigrationMode::MIGRATING);
    }

    public function testDeprecatedService(): void
    {
        $this->runProtoTest('DeprecatedService/deprecated_service.proto');
    }

    public function testBasicDiregapicWithPreMigrationSurface(): void
    {
        $this->runProtoTest('BasicDiregapic/library_rest.proto', 'google.example.library.v1', 'rest', migrationMode: MigrationMode::PRE_MIGRATION_SURFACE_ONLY);
    }

    public function testResourceNamesWithMigrationModeUnspecified(): void
    {
        $this->runProtoTest('ResourceNames/resource-names.proto', migrationMode: MigrationMode::MIGRATION_MODE_UNSPECIFIED);
    }

    public function testCustomLro(): void
    {
        $this->runProtoTest('CustomLro/custom_lro.proto', 'testing.customlro', 'rest');
    }

    public function testCustomLroNew(): void
    {
        $this->runProtoTest('CustomLroNew/custom_lro_new.proto', 'testing.customlronew', 'rest', migrationMode: MigrationMode::NEW_SURFACE_ONLY);
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

    public function testBasicAutoPopulation(): void
    {
        $this->runProtoTest('BasicAutoPopulation/basic-auto-population.proto', 'testing.basicautopopulation');
    }
}
