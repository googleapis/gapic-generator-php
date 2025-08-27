<?php
/*
 * Copyright 2025 Google LLC
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
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\DiagnosticsProvider;
use LogicException;
use ParseError;

trait PostProcessorTrait
{
    private ClassDeclaration $classNode;

    public function __construct(string $contents)
    {
        $this->classNode = self::fromCode($contents);
    }

    public function getContents(): string
    {
        return $this->classNode->getFileContents();
    }

    public static abstract function run(string $inputDir): void;

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
}