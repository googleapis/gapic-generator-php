<?php
/*
 * Copyright 2026 Google LLC
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

namespace Google\Generator\Tests\Integration;

use FilesystemIterator;
use Google\Generator\Tests\Tools\GeneratorUtils;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class IntegrationTest extends TestCase
{
    public const TESTS = [
        'asset' => [
            'protoDir' => 'googleapis/google/cloud/asset/v1',
            'grpcServiceConfig' => 'googleapis/google/cloud/asset/v1/cloudasset_grpc_service_config.json',
            'numericEnums' => true,
        ],
        'compute_small' => [
            'protoDir' => 'tests/Integration/apis/compute_small/v1',
            'serviceYaml' => 'tests/Integration/apis/compute_small/v1/compute_small_v1.yaml',
            'transport' => 'rest',
        ],
        'container' => [
            'protoDir' => 'googleapis/google/container/v1',
            'grpcServiceConfig' => 'googleapis/google/container/v1/container_grpc_service_config.json',
        ],
        'dataproc' => [
            'protoDir' => 'googleapis/google/cloud/dataproc/v1',
            'gapicYaml' => 'tests/Integration/apis/dataproc/v1/dataproc_gapic.yaml',
            'grpcServiceConfig' => 'googleapis/google/cloud/dataproc/v1/dataproc_grpc_service_config.json',
            'serviceYaml' => 'googleapis/google/cloud/dataproc/v1/dataproc_v1.yaml',
        ],
        'functions' => [
            'protoDir' => 'googleapis/google/cloud/functions/v1',
            'grpcServiceConfig' => 'googleapis/google/cloud/functions/v1/functions_grpc_service_config.json',
        ],
        'kms' => [
            'protoDir' => 'tests/Integration/apis/kms/v1',
            'grpcServiceConfig' => 'tests/Integration/apis/kms/v1/cloudkms_grpc_service_config.json',
            'serviceYaml' => 'tests/Integration/apis/kms/v1/cloudkms_test_mixins_v1.yaml',
        ],
        'iam' => [
            'protoDir' => 'googleapis/google/iam/v1',
            'gapicYaml' => 'tests/Integration/apis/iam/v1/iam_gapic.yaml',
            'serviceYaml' => 'tests/Integration/apis/iam/v1/iam_api.yaml',
        ],
        'logging' => [
            'protoDir' => 'googleapis/google/logging/v2',
            'gapicYaml' => 'googleapis/google/logging/v2/logging_gapic.yaml',
            'grpcServiceConfig' => 'googleapis/google/logging/v2/logging_grpc_service_config.json',
        ],
        'redis' => [
            'protoDir' => 'googleapis/google/cloud/redis/v1',
            'gapicYaml' => 'tests/Integration/apis/redis/v1/redis_gapic.yaml',
            'grpcServiceConfig' => 'googleapis/google/cloud/redis/v1/redis_grpc_service_config.json',
            'serviceYaml' => 'googleapis/google/cloud/redis/v1/redis_v1.yaml',
            'transport' => 'grpc',
        ],
        'retail' => [
            'protoDir' => 'googleapis/google/cloud/retail/v2alpha',
            'grpcServiceConfig' => 'googleapis/google/cloud/retail/v2alpha/retail_grpc_service_config.json',
            'serviceYaml' => 'googleapis/google/cloud/retail/v2alpha/retail_v2alpha.yaml',
        ],
        'spanner' => [
            'protoDir' => 'googleapis/google/spanner/admin/database/v1',
            'serviceYaml' => 'googleapis/google/spanner/admin/database/v1/spanner.yaml',
            'numericEnums' => true,
        ],
        'speech' => [
            'protoDir' => 'googleapis/google/cloud/speech/v1',
            'grpcServiceConfig' => 'googleapis/google/cloud/speech/v1/speech_grpc_service_config.json',
            'serviceYaml' => 'googleapis/google/cloud/speech/v1/speech_v1.yaml',
        ],
        'securitycenter' => [
            'protoDir' => 'googleapis/google/cloud/securitycenter/v1',
            'grpcServiceConfig' => 'googleapis/google/cloud/securitycenter/v1/securitycenter_grpc_service_config.json',
            'serviceYaml' => 'googleapis/google/cloud/securitycenter/v1/securitycenter_v1.yaml',
        ],
        'talent' => [
            'protoDir' => 'googleapis/google/cloud/talent/v4beta1',
            'grpcServiceConfig' => 'googleapis/google/cloud/talent/v4beta1/talent_grpc_service_config.json',
            'serviceYaml' => 'googleapis/google/cloud/talent/v4beta1/jobs_v4beta1.yaml',
        ],
        'videointelligence' => [
            'protoDir' => 'googleapis/google/cloud/videointelligence/v1',
            'gapicYaml' => 'googleapis/google/cloud/videointelligence/v1/videointelligence_gapic.yaml',
            'serviceYaml' => 'tests/Integration/apis/videointelligence/v1/videointelligence_v1.yaml',
        ],
    ];

    public function provideIntegrationTests(): array
    {
        $cases = [];
        foreach (array_keys(self::TESTS) as $name) {
            $cases[$name] = [$name];
        }
        return $cases;
    }

    /**
     * @dataProvider provideIntegrationTests
     */
    public function testIntegration(string $name): void
    {
        $codeIterator = GeneratorUtils::generateIntegration(self::TESTS[$name]);
        $goldenDir = __DIR__ . '/goldens/' . $name . '/';
        $files = iterator_to_array(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $goldenDir,
                    FilesystemIterator::SKIP_DOTS
                )
            )
        );

        foreach ($codeIterator as [$relativeFilename, $code]) {
            $filename = $goldenDir . $relativeFilename;
            $this->assertTrue(file_exists($filename), "Expected code file missing: '{$filename}'");
            $expectedCode = file_get_contents($filename);
            $this->assertEquals($expectedCode, $code, str_replace(__DIR__ . '/', '', $filename));
            unset($files[$filename]);
        }

        $this->assertEmpty(
            $files,
            'The following expected files were not generated: ' .
            print_r(array_keys($files), true)
        );
    }
}
