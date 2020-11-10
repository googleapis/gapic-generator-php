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

// TODO: Improve and generalise this integration test running, and add more tests.

$rootDir = __DIR__ . '/..';

// Build the proto descriptor.
$protoc = "{$rootDir}/tools/protoc";
$descRes = tmpfile();
$descFilename = stream_get_meta_data($descRes)['uri'];
$descFilename = "/tmp/desc.desc";
$protobuf = "{$rootDir}/protobuf/src/";
$googleapis = "{$rootDir}/googleapis/";
$input = "{$rootDir}/tests/ProtoTests/Basic/basic.proto";
$protocCmdLine = "{$protoc} --include_imports --include_source_info -o {$descFilename} " .
    "-I {$googleapis} -I {$protobuf} -I {$rootDir} {$input} 2>&1";
execCmd($protocCmdLine, 'protoc');

$rootOutDir = sys_get_temp_dir() . '/php-gapic-' . mt_rand(0, (int)1e8);

// Run the micro-generator.
$microMain = "{$rootDir}/src/Main.php";
$microOutDir = "{$rootOutDir}/micro";
$microCmdLine = "php {$microMain} --descriptor {$descFilename} --package testing.basic --output {$microOutDir} 2>&1";
execCmd($microCmdLine, 'micro');

// Run the monolithic generator.
$monoDir = "{$rootDir}/gapic-generator";
$monoBuildDir = "{$monoDir}/build";
if (!file_exists($monoBuildDir)) {
    execCmd("cd {$monoDir}; ./gradlew fatJar", 'mono-gradle');
}
$monoOutDir = "{$rootOutDir}/mono";
$monoCmdLine = "java " .
    '-cp build/libs/gapic-generator-2.7.0-fatjar.jar:build/libs/gapic-generator-latest-fatjar.jar ' .
    'com.google.api.codegen.GeneratorMain ' .
    'GAPIC_CODE ' .
    "--descriptor_set {$descFilename} " .
    '--package testing.basic ' .
    '--language php ' .
    "-o {$monoOutDir}";
execCmd("cd {$monoDir}; {$monoCmdLine}", 'mono');

// Compare a file. Just the gapic client for the moment.
// TODO: Compare all generated files.
$ok = compareFile(
    "{$monoOutDir}/src/Gapic/BasicGapicClient.php",
    "{$microOutDir}/Gapic/BasicGapicClient.php"
);

// Delete tmp directory.
delTree($rootOutDir);

if (!$ok) {
    print("Fail\n");
    exit(1);
} else {
    print("Pass\n");
    exit(0);
}

function execCmd($cmd, $errorPrefix)
{
    $output = [];
    $result = -1;
    exec($cmd, $output, $result);
    if ($result !== 0) {
        print("{$errorPrefix} error:\n" . implode("\n", $output) . "\n");
        exit(1);
    }
}

function compareFile($monoPath, $microPath): bool
{
    // Compare ignoring whitespace, except within strings.
    // Ignore '*' in comments.
    // Ignore trailing commas.
    $mono = file_get_contents($monoPath);
    $micro = file_get_contents($microPath);
    $monoLen = strlen($mono);
    $microLen = strlen($micro);
    $monoPos = 0;
    $microPos = 0;
    $inString = false;
    $inComment = false;
    while ($monoPos < $monoLen && $microPos < $microLen) {
        if ($mono[$monoPos] !== $micro[$microPos]) {
            if (!$inString) {
                while ($monoPos < $monoLen &&
                    (isWhitespace($mono[$monoPos]) ||
                    ($inComment && $mono[$monoPos] === '*') ||
                    substr($mono, $monoPos, 2) === ",\n")) $monoPos++;
                while ($microPos < $microLen &&
                    (isWhitespace($micro[$microPos]) ||
                    ($inComment && $micro[$microPos] === '*') ||
                    substr($micro, $microPos, 2) === ",\n")) $microPos++;
            }
        }
        $c = $mono[$monoPos];
        if ($c !== $micro[$microPos]) {
            $lines = 5;
            for ($monoFrom = $monoPos, $c = 0; $c < $lines && $monoFrom > 0; $monoFrom--) $c += $mono[$monoFrom] === "\n" ? 1 : 0;
            for ($monoTo = $monoPos, $c = 0; $c < $lines && $monoTo < $monoLen; $monoTo++) $c += $mono[$monoTo] === "\n" ? 1 : 0;
            for ($microFrom = $microPos, $c = 0; $c < $lines && $microFrom > 0; $microFrom--) $c += $micro[$microFrom] === "\n" ? 1 : 0;
            for ($microTo = $microPos, $c = 0; $c < $lines && $microTo < $microLen; $microTo++) $c += $micro[$microTo] === "\n" ? 1 : 0;
            print("-----\nmono:\n");
            print(substr($mono, $monoFrom + 2, $monoTo - $monoFrom - 2));
            print("----- '{$mono[$monoPos]}' -> '{$micro[$microPos]}' \nmicro:\n");
            print(substr($micro, $microFrom + 2, $microTo - $microFrom - 2));
            print("-----\n");
            return false;
        }
        if ($c === '"') $inString = $inString === false ? '"' : ($inString === '"' ? false : $inString);
        elseif ($c === "'") $inString = $inString === false ? "'" : ($inString === "'" ? false : $inString);
        else if (!$inString) {
            if (!$inComment && substr($mono, $monoPos - 1, 2) === '/*') $inComment = true;
            elseif ($inComment && substr($mono, $monoPos - 1, 2) === '*/') $inComment = false;
        }
        $monoPos++;
        $microPos++;
    }
    return true;
}

function isWhitespace($c)
{
    return $c === ' ' || $c === "\n";
}

function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        $path = "{$dir}/{$file}";
        is_dir($path) ? delTree($path) : unlink($path);
    }
    return rmdir($dir);
}
