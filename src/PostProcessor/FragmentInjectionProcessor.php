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
    private const METHOD_PREFIX_TOKENS = [
        T_PUBLIC,
        T_PROTECTED,
        T_PRIVATE,
        T_STATIC,
        T_FINAL,
        T_ABSTRACT,
        T_DOC_COMMENT,
        T_COMMENT,
    ];

    private string $contents;
    /** @var PhpToken[] */
    private array $tokens;

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
     * @return PhpToken[]
     */
    private static function fromCode(string $contents): array
    {
        try {
            $tokens = PhpToken::tokenize($contents, TOKEN_PARSE);
        } catch (ParseError $e) {
            throw new ParseError('Provided contents contains a PHP syntax error', 0, $e);
        }

        foreach ($tokens as $token) {
            if ($token->is(T_CLASS)) {
                return $tokens;
            }
        }

        throw new LogicException('Provided contents does not contain a PHP class');
    }

    public function __construct(string $contents)
    {
        $this->tokens = self::fromCode($contents);
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
        $this->tokens = self::fromCode($contents);
        $this->contents = $contents;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    private function getInsertLineBeforeFirstMethod(): int
    {
        if (null !== $methodIndex = $this->findMethodTokenIndex()) {
            return $this->getMethodStartLine($methodIndex) - 1;
        }

        // if there are no methods in the file, insert fragment before the end of the class
        return $this->getClassEndLine() - 1;
    }

    private function getInsertLineBeforeMethod(string $insertBeforeMethod): int
    {
        if (null !== $methodIndex = $this->findMethodTokenIndex($insertBeforeMethod)) {
            return $this->getMethodStartLine($methodIndex) - 1;
        }

        throw new LogicException(
            'Provided contents does not contain method ' . $insertBeforeMethod
        );
    }

    private function findMethodTokenIndex(?string $methodName = null): ?int
    {
        foreach ($this->tokens as $i => $token) {
            if (!$token->is(T_FUNCTION)) {
                continue;
            }
            $nameToken = $this->tokens[$i + 2] ?? null;
            if ($nameToken && $nameToken->is(T_STRING)) {
                if ($methodName === null || $nameToken->text === $methodName) {
                    return $i;
                }
            }
        }

        return null;
    }

    private function getMethodStartLine(int $functionTokenIndex): int
    {
        $startLine = $this->tokens[$functionTokenIndex]->line;
        for ($i = $functionTokenIndex - 1; $i >= 0; $i--) {
            $token = $this->tokens[$i];
            if ($token->is(T_WHITESPACE)) {
                continue;
            }
            if ($token->is(self::METHOD_PREFIX_TOKENS)) {
                $startLine = $token->line;
                continue;
            }
            break;
        }

        return $startLine;
    }

    private function getClassEndLine(): int
    {
        for ($i = count($this->tokens) - 1; $i >= 0; $i--) {
            if ($this->tokens[$i]->text === '}') {
                return $this->tokens[$i]->line;
            }
        }

        throw new LogicException('Provided contents does not contain a PHP class');
    }
}
