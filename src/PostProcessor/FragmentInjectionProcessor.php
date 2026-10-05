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

use LogicException;
use ParseError;
use PhpToken;

class FragmentInjectionProcessor implements ProcessorInterface
{
    private string $contents;
    /** @var array<string, int> */
    private array $methods;
    private int $classEndLine;

    public static function run(string $inputDir): void
    {
        $fragDir = new \RecursiveDirectoryIterator($inputDir);
        $fragDirItr = new \RecursiveIteratorIterator($fragDir);
        $fragmentItr = new \RegexIterator($fragDirItr, '/^.+\.build\.txt$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach ($fragmentItr as $finding) {
            $fragmentPath = $finding[0];
            $protoPath = str_replace(['fragments', '.build.txt'], ['proto/src', '.php'], $fragmentPath);

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

    /**
     * @return array{array<string, int>, int}
     */
    private static function parseClass(string $contents): array
    {
        try {
            $tokens = PhpToken::tokenize($contents, TOKEN_PARSE);
        } catch (ParseError $e) {
            throw new ParseError('Provided contents contains a PHP syntax error', 0, $e);
        }

        $count = count($tokens);
        $inClass = false;
        $depth = 0;
        $memberStartLine = null;
        $methods = [];
        $classEndLine = null;

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (!$inClass) {
                if ($token->id === T_CLASS) {
                    $prev = $i - 1;
                    while ($prev >= 0 && $tokens[$prev]->id === T_WHITESPACE) {
                        $prev--;
                    }
                    if ($prev < 0 || ($tokens[$prev]->id !== T_DOUBLE_COLON && $tokens[$prev]->id !== T_NEW)) {
                        $inClass = true;
                    }
                }
                continue;
            }

            if ($token->text === '{' || $token->id === T_CURLY_OPEN || $token->id === T_DOLLAR_OPEN_CURLY_BRACES) {
                $depth++;
                if ($depth === 1) {
                    $memberStartLine = null;
                }
                continue;
            }

            if ($token->text === '}') {
                $depth--;
                if ($depth === 1) {
                    $memberStartLine = null;
                } elseif ($depth === 0) {
                    $classEndLine = $token->line;
                    break;
                }
                continue;
            }

            if ($depth === 1) {
                if ($token->text === ';') {
                    $memberStartLine = null;
                    continue;
                }
                if ($memberStartLine === null && $token->id !== T_WHITESPACE) {
                    $memberStartLine = $token->line;
                }
                if ($token->id === T_FUNCTION) {
                    $j = $i + 1;
                    while ($j < $count && ($tokens[$j]->id === T_WHITESPACE || $tokens[$j]->text === '&')) {
                        $j++;
                    }
                    if ($j < $count && $tokens[$j]->id === T_STRING) {
                        $methods[$tokens[$j]->text] = $memberStartLine ?? $token->line;
                    }
                }
            }
        }

        if (!$inClass || $classEndLine === null) {
            throw new LogicException('Provided contents does not contain a PHP class');
        }

        return [$methods, $classEndLine];
    }

    public function __construct(string $contents)
    {
        [$this->methods, $this->classEndLine] = self::parseClass($contents);
        $this->contents = $contents;
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

        $lines = explode(PHP_EOL, $this->contents);
        array_splice($lines, $insertLine, 0, $newContent);
        $contents = implode(PHP_EOL, $lines);
        [$this->methods, $this->classEndLine] = self::parseClass($contents);
        $this->contents = $contents;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    private function getInsertLineBeforeFirstMethod(): int
    {
        if (!empty($this->methods)) {
            return reset($this->methods) - 1;
        }
        // if there are no methods in the file, insert fragment before the end of the class
        return $this->classEndLine - 1;
    }

    private function getInsertLineBeforeMethod(string $insertBeforeMethod): int
    {
        if (isset($this->methods[$insertBeforeMethod])) {
            return $this->methods[$insertBeforeMethod] - 1;
        }

        throw new LogicException(
            'Provided contents does not contain method ' . $insertBeforeMethod
        );
    }
}
