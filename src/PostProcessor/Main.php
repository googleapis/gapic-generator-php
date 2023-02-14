<?php
/*
 * Copyright 2023 Google LLC
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

namespace Google\PostProcessor;

require __DIR__ . '/../../vendor/autoload.php';
error_reporting(E_ALL);

function showUsageAndExit()
{
    print("Invalid arguments. Expect:\n");
    print("  --input <path> The path to the expanded archive of generated code.\n");
    print("  --base_dir <path> The path of the current working dir to use as a base.\n");
    print("\n");
    exit(1);
}

// Add new options to the second list as "flag_name:".
$opts = getopt('', ['input:', "base_dir:"]);
if (!isset($opts['input'])) {
    showUsageAndExit();
}

PostProcessor::process($opts);
