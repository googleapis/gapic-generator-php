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

use Google\Generator\Utils\MigrationMode;

require __DIR__ . '../../../../vendor/autoload.php';
error_reporting(E_ALL);

const UNIT_TESTS = [
  1 => [
    'name' => 'Basic',
    'protoPath' => 'Basic/basic.proto',
    'migrationMode' => MigrationMode::NEW_SURFACE_ONLY
  ],
  2 => [
    'name' => 'BasicLro',
    'protoPath' => 'BasicLro/basic-lro.proto'
  ],
  3 => [
    'name' => 'BasicOneof',
    'protoPath' => 'BasicOneof/basic-oneof.proto'
  ],
  4 => [
    'name' => 'BasicPaginated',
    'protoPath' => 'BasicPaginated/basic-paginated.proto'
  ],
  5 => [
    'name' => 'BasicBidiStreaming',
    'protoPath' => 'BasicBidiStreaming/basic-bidi-streaming.proto'
  ],
  6 => [
    'name' => 'BasicServerStreaming',
    'protoPath' => 'BasicServerStreaming/basic-server-streaming.proto'
  ],
  7 => [
    'name' => 'BasicClientStreaming',
    'protoPath' => 'BasicClientStreaming/basic-client-streaming.proto'
  ],
  8 => [
    'name' => 'GrpcServiceConfig',
    'protoPath' => 'GrpcServiceConfig/grpc-service-config1.proto',
    'package' => 'testing.grpcserviceconfig'
  ],
  9 => [
    'name' => 'RoutingHeaders',
    'protoPath' => 'RoutingHeaders/routing-headers.proto',
    'migrationMode' => MigrationMode::MIGRATING
  ],
  10 => [
    'name' => 'DeprecatedService',
    'protoPath' => 'DeprecatedService/deprecated_service.proto'
  ],
  11 => [
    'name' => 'BasicDiregapic',
    'protoPath' => 'BasicDiregapic/library_rest.proto',
    'package' => 'google.example.library.v1',
    'transport' => 'rest',
    'migrationMode' => 'PRE_MIGRATION_SURFACE_ONLY'
  ],
  12 => [
    'name' => 'ResourceNames',
    'protoPath' => 'ResourceNames/resource-names.proto'
  ],
  13 => [
    'name' => 'CustomLro',
    'protoPath' => 'CustomLro/custom_lro.proto',
    'package' => 'testing.customlro',
    'transport' => 'rest'
  ],
  14 => [
    'name' => 'DisableSnippets',
    'protoPath' => 'DisableSnippets/disable_snippets.proto',
    'package' => 'testing.disablesnippets',
    'generateSnippets' => false
  ],
];

$optionString = implode("\n", array_map(
    function ($v, $k) {
        return sprintf("%s: '%s'", $k, $v['name']);
    },
    UNIT_TESTS,
    array_keys(UNIT_TESTS)
));

$selection = -1;
while ($selection < 0 || $selection > sizeof(array_keys(UNIT_TESTS))) {
    print("============ Unit tests ==========\n$optionString\n\nSelect golden to update (0 for all): ");
    $nextLine = fscanf(STDIN, "%d\n", $selection);
}

if ($selection !== 0) {
    updateGolden($selection);
} else {
    foreach (array_keys(UNIT_TESTS) as $testIndex) {
        updateGolden($testIndex);
        print("\n");
    }
}
exit(0);

function updateGolden(int $testIndex)
{
    $goldenUpdater = new UnitGoldenUpdater;
    $testData = UNIT_TESTS[$testIndex];
    print("Updating goldens for " . $testData['name'] . "\n");
    $goldenUpdater->update(
        $testData['protoPath'],
        array_key_exists('package', $testData) ? $testData['package'] : null,
        array_key_exists('transport', $testData) ? $testData['transport'] : null,
        array_key_exists('generateSnippets', $testData) ? $testData['generateSnippets'] : true,
        array_key_exists('migrationMode', $testData) ? $testData['migrationMode'] : MigrationMode::MIGRATION_MODE_UNSPECIFIED
    );
    print("\n");
}
