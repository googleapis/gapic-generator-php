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

namespace Google\Generator;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

// TODO: Support running as protoc plugin.
// TODO: Provide help/usage if incorrect command-line args provided.
// Read commend-line args.
$opts = getopt('', ['descriptor:', 'package:']);
$descBytes = stream_get_contents(fopen($opts['descriptor'], 'rb'));
$package = $opts['package'];

// Generate PHP code.
// At the moment $files is just the file content.
// TODO: Change this to be file location and content
$files = CodeGenerator::GenerateFromDescriptor($descBytes, $package);
foreach ($files as [$relativeFilename, $fileContent]) {
    // TODO: Later this won't just print out the generated file content.
    print("File: '{$relativeFilename}':\n");
    print($fileContent . "\n");
}
