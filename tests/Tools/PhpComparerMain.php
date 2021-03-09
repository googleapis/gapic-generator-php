<?php
/*
 * Copyright 2021 Google LLC
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

namespace Google\Generator\Tools;

use Google\Generator\Tests\Tools\PhpClassComparer;

require __DIR__ . '../../../vendor/autoload.php';
error_reporting(E_ALL);

if (!compareFiles($argv)) {
  print("FAIL\n\n");
  exit(1);
}

print("PASS\n\n");

/**
 * Compares two PHP files. Assumes the LHS arg (nodesOne) is from the monolith, and the RHS one from the microgenerator.
 */
function compareFiles($argv): bool {
    if (count($argv) < 3) {
        print("Insufficient arguments\nUsage example: php MethodComparer.php /path/to/file1.php /path/to/file2.php");
        exit(1);
    }

    $fileContentsOne = parseFileToString($argv[1]);
    $fileContentsTwo = parseFileToString($argv[2]);
    if (is_null($fileContentsOne) || is_null($fileContentsTwo)) {
      exit(1);
    }

    return PhpClassComparer::compare($fileContentsOne, $fileContentsTwo);
}

/**
 * Parses the file to a string.
 * @return ?string the file contens, or null upon a file parsing error.
 */
function parseFileToString(string $filePath): ?string {
    if (!file_exists($filePath)) {
        print("File $filePath does not exist");
        return null;
    }
    if (strcmp(pathinfo($filePath)['extension'], "php") != 0) {
        print("$filePath is not a PHP file");
        return null;
    }
    $phpFileContents = file_get_contents($filePath);
    if (!$phpFileContents) {
        print("File parsing failed.");
        return null;
    }
    return $phpFileContents;
}
