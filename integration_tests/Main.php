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

$result = Invoker::invoke('tests/ProtoTests/Basic/basic.proto');
$mono = $result['mono'];
$micro = $result['micro'];

// Compare a file. Just the generated gapic client for the moment.
// TODO: Compare all generated files.
$ok = SourceComparer::compare(
    $mono['/src/Gapic/BasicGapicClient.php'],
    $micro['/Gapic/BasicGapicClient.php']
);

if (!$ok) {
    print("Fail\n");
    exit(1);
} else {
    print("Pass\n");
    exit(0);
}
