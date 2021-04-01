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

namespace Google\Generator\Tests\Unit\ProtoTests;

use Google\Generator\Tests\Tools\GeneratorUtils;

class UnitGoldenUpdater
{
    public function update(string $protoPath, ?string $package = null): void
    {
        $codeIterator = GeneratorUtils::generateFromProto($protoPath, $package);
        $outputPath = __DIR__ . '/' . dirname($protoPath) . '/out';
        // Delete everything from the directory.
        if (is_dir($outputPath)) {
            $fileSysObjects = scandir($outputPath);
            foreach ($fileSysObjects as $fileSysObject) {
                if ($fileSysObject === "." || $fileSysObject === "..") {
                    continue;
                }
                $fullPath = "$outputPath/$fileSysObject";
                if (filetype($fullPath) === "dir") {
                    static::deleteFilesInDir($fullPath);
                } else {
                    unlink($fullPath);
                }
            }
            reset($fileSysObjects);
        }
        foreach ($codeIterator as [$relativeFilename, $code]) {
            $filename = "$outputPath/$relativeFilename";
            print("\twriting $relativeFilename\n");
            file_put_contents($filename, $code);
        }
    }

    private static function deleteFilesInDir($directoryPath)
    {
        if (!is_dir($directoryPath)) {
            throw new InvalidArgumentException("$directoryPath must be a directory");
        }
        if (substr($directoryPath, strlen($directoryPath) - 1, 1) != '/') {
            $directoryPath .= '/';
        }
        $files = glob($directoryPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                static::deleteFilesInDir($file);
            } else {
                unlink($file);
            }
        }
    }
}
