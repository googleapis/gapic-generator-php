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

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

// Initial integration test.
// Compare output of monolith and micro generators.
// They should match except for whitespace and trailing commas.

// Initially run for just the "basic" proto as defined in the basic unit test.

$ok = true;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/Basic/basic.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicLro/basic-lro.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicPaginated/basic-paginated.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicBidiStreaming/basic-bidi-streaming.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicServerStreaming/basic-server-streaming.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicClientStreaming/basic-client-streaming.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/ResourceNames/resource-names.proto')) ? $ok : false;

if (!$ok) {
    print("\nFail\n");
    exit(1);
} else {
    print("\nPass\n");
    exit(0);
}

function processDiff($result)
{
    $mono = $result['mono'];
    $micro = $result['micro'];

    $ok = true;

    // Find missing files.
    $missing = array_diff(array_keys($mono), array_keys($micro));
    $ok = count($missing) === 0 ? $ok : false;
    foreach ($missing as $missingPath) {
        print("File missing from micro-generator: '{$missingPath}'\n");
        print($mono[$missingPath]);
        print("\n");
    }

    // Find excessive files.
    $excess = array_diff(array_keys($micro), array_keys($mono));
    $ok = count($excess) === 0 ? $ok : false;
    foreach ($excess as $excessPath) {
        print("File mistakenly generated from micro-generator: '{$excessPath}'\n");
    }

    // Find incorrectly generated files.
    foreach (array_intersect(array_keys($mono), array_keys($micro)) as $path) {
        print("Comparing: '{$path}':\n");
        $sameContent = SourceComparer::compare($mono[$path], $micro[$path]);
        $ok = $sameContent ? $ok : false;
    }

    return $ok;
}
