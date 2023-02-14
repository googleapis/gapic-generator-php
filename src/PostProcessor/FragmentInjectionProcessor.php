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

use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\PositionUtilities;
use Microsoft\PhpParser\DiagnosticsProvider;
use LogicException;
use ParseError;

class FragmentInjectionProcessor implements Processor
{
    private ClassDeclaration $classNode;

    public static function run(string $inputDir): void
    {
        $fragDir = new \RecursiveDirectoryIterator($inputDir);
        $fragDirItr = new \RecursiveIteratorIterator($fragDir);
        $fragmentItr = new \RegexIterator($fragDirItr, '/^.+\.build\.txt$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach($fragmentItr as $finding) {
            $fragmentPath = $finding[0];
            $protoPath = str_replace('fragments', 'proto/src', $fragmentPath);
            $protoPath = str_replace('.build.txt', '.php', $protoPath);
            
            self::inject($fragmentPath, $protoPath);
        }
    }

    private static function inject(string $fragmentFile, string $classFile): void
    {
        // The fragment to insert into another class.
        $fragmentContent = file_get_contents($fragmentFile);

        // The class to insert the fragment into.
        $classContent = file_get_contents($classFile);
        $addFragmentUtil = new FragmentInjectionProcessor($classContent);

        // Insert the fragment into the class.
        // If no method is provided, the fragment is inserted before the first method
        // ("__construct", for instance), or before the end of the class.
        $addFragmentUtil->insert($fragmentContent);

        // Write the new contents to the class file.
        file_put_contents($classFile, $addFragmentUtil->getContents());
        print("Fragment written to $classFile\n");
    }

    private static function fromCode(string $contents): ClassDeclaration
    {
        $parser = new Parser();
        $astNode = $parser->parseSourceFile($contents);
        if ($errors = DiagnosticsProvider::getDiagnostics($astNode)) {
            throw new ParseError('Provided contents contains a PHP syntax error');
        }

        foreach ($astNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof ClassDeclaration) {
                return $childNode;
            }
        }

        throw new LogicException('Provided contents does not contain a PHP class');
    }

    public function __construct(string $contents)
    {
        $this->classNode = self::fromCode($contents);
    }

    /**
     * @throws LogicException
     * @throws ParseError
     */
    public function insert(string $newContent, ?string $insertBeforeMethod = null): void
    {
        $insertLine = $insertBeforeMethod
            ? $this->getInsertLineBeforeMethod($insertBeforeMethod)
            : $this->getInsertLineBeforeFirstMethod();

        $lines = explode(PHP_EOL, $this->classNode->getFileContents());
        array_splice($lines, $insertLine, 0, $newContent);
        $contents = implode(PHP_EOL, $lines);
        $this->classNode = self::fromCode($contents);
    }

    public function getContents(): string
    {
        return $this->classNode->getFileContents();
    }

    private function getLineNumberFromPosition(int $startPosition): int
    {
        return PositionUtilities::getLineCharacterPositionFromPosition(
            $startPosition,
            $this->classNode->getFileContents()
        )->line;
    }

    private function getInsertLineBeforeFirstMethod(): int
    {
        foreach ($this->classNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                return $this->getLineNumberFromPosition($childNode->getFullStartPosition()) + 1;
            }
        }
        // if there are no methods in the file, insert fragment before the end of the class
        return $this->getLineNumberFromPosition($this->classNode->getEndPosition());
    }

    private function getInsertLineBeforeMethod(string $insertBeforeMethod): int
    {
        foreach ($this->classNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                if ($childNode->getName() === $insertBeforeMethod) {
                    return $this->getLineNumberFromPosition($childNode->getFullStartPosition()) + 1;
                }
            }
        }

        throw new LogicException(
            'Provided contents does not contain method ' . $insertBeforeMethod
        );
    }
}
