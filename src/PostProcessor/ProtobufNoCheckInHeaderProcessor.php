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

namespace Google\PostProcessor;

class ProtobufNoCheckInHeaderProcessor implements ProcessorInterface
{
    public static function run(string $inputDir): void
    {
        $protoDir = new \RecursiveDirectoryIterator($inputDir . '/proto/src');
        $protoDirItr = new \RecursiveIteratorIterator($protoDir);
        $protoItr = new \RegexIterator($protoDirItr, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach ($protoItr as $finding) {
            self::inject($finding[0]);
        }
    }

    private static function inject(string $classFile): void
    {
        // The class to insert the fragment into.
        $classContent = file_get_contents($classFile);
        $processor = new self($classContent);

        $processor->removeNoCheckInHeader();

        // Write the new contents to the class file.
        file_put_contents($classFile, $processor->getContents());
        print("No Check-In Header removed in $classFile\n");
    }

    public function __construct(private string $contents)
    {
    }

    public function removeNoCheckInHeader(): void
    {
        $this->contents = str_replace(
            "# NO CHECKED-IN PROTOBUF GENCODE\n",
            '',
            $this->contents,
        );
    }

    public function getContents(): string
    {
        return $this->contents;
    }
}
