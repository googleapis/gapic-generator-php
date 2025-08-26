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

class FirestoreRequestParamProcessor implements ProcessorInterface
{
    private ClassDeclaration $classNode;

    public static function run(string $inputDir): void
    {
        $firestoreClientFile = $inputDir . '/src/V1/Client/FirestoreClient.php';
        self::inject($firestoreClientFile);
    }

    private static function inject(string $classFile): void
    {
        // The class to update
        $classContent = file_get_contents($classFile);
        $processor = new FirestoreRequestParamProcessor($classContent);

        $processor->addDatabaseRequestParamToListenMethod();

        // Write the new contents to the class file.
        file_put_contents($classFile, $processor->getContents());
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
    public function addDatabaseRequestParamToListenMethod(): void
    {
        $listenMethod = $this->getMethodDeclaration('listen');

        // update PHPDoc
        $lineToReplace = '     *     @type int $timeoutMillis';
        $newLines = [
            '     *     @type string $datbase',
            '     *           Set the database of the call, to be added as a routing header',
            $lineToReplace,
        ];

        $phpdoc = $listenMethod->getDocCommentText();
        $newPhpdoc = str_replace($lineToReplace, implode(PHP_EOL, $newLines), $phpdoc);
        $newContents = str_replace($phpdoc, $newPhpdoc, $this->classNode->getFileContents());

        // update listen method
        $lineToReplace = '        return $this->startApiCall(\'Listen\', null, $callOptions);';
        $newLines = [
            '        $requestParamHeaders = [];',
            '        if (isset($callOptions[\'database\'])) {',
            '            $requestParamHeaders[\'database\'] = $callOptions[\'database\'];',
            '        }',
            '        $requestParams = new \Google\ApiCore\RequestParamsHeaderDescriptor($requestParamHeaders);',
            '        $callOptions[\'headers\'] = isset($optionalArgs[\'headers\']) ? array_merge($requestParams->getHeader(), $callOptions[\'headers\']) : $requestParams->getHeader();',
            $lineToReplace,
        ];
        $methodText = $listenMethod->compoundStatementOrSemicolon->getText();
        $newMethodText = str_replace($lineToReplace, implode(PHP_EOL, $newLines), $methodText);
        $newContents = str_replace($methodText, $newMethodText, $newContents);

        $this->classNode = self::fromCode($newContents);
    }

    public function getContents(): string
    {
        return $this->classNode->getFileContents();
    }

    private function getMethodDeclaration(string $insertBeforeMethod): MethodDeclaration
    {
        foreach ($this->classNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                if ($childNode->getName() === $insertBeforeMethod) {
                    return $childNode;
                }
            }
        }

        throw new LogicException(
            'Provided contents does not contain method ' . $insertBeforeMethod
        );
    }
}
