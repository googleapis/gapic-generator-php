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

use Microsoft\PhpParser\Node\MethodDeclaration;
use LogicException;
use ParseError;

class FirestoreRequestParamProcessor implements ProcessorInterface
{
    use PostProcessorTrait;

    // Line to insert the PHP doc param above
    private const DATABASE_PHPDOC_INSERT_AT =
        '     *     @type int $timeoutMillis';

    // PHPdoc param to insert
    private const DATABASE_PHPDOC_PARAM = <<<'EOL'
     *     @type string $database
     *           Set the database of the call, to be added as a routing header
EOL;

    // Line to insert the request param code above
    private const DATABASE_REQUEST_PARAM_INSERT_AT =
        '        return $this->startApiCall(\'Listen\', null, $callOptions);';

    // Request param code to insert
    private const DATABASE_REQUEST_PARAM_CODE = <<<'EOL'
$requestParamHeaders = [];
if (isset($callOptions['database'])) {
    $requestParamHeaders['database'] = $callOptions['database'];
}
$requestParams = new \Google\ApiCore\RequestParamsHeaderDescriptor($requestParamHeaders);
$callOptions['headers'] = isset($callOptions['headers']) ? array_merge($requestParams->getHeader(), $callOptions['headers']) : $requestParams->getHeader();
EOL;

    public static function run(string $inputDir): void
    {
        $firestoreClientFile = $inputDir . '/src/V1/Client/FirestoreClient.php';
        if (file_exists($firestoreClientFile)) {
            self::inject($firestoreClientFile);
        }
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

    /**
     * @throws LogicException
     * @throws ParseError
     */
    public function addDatabaseRequestParamToListenMethod(): void
    {
        $contents = $this->classNode->getFileContents();
        $listenMethod = $this->getMethodDeclaration('listen');

        // update PHPDoc
        $phpdoc = $listenMethod->getDocCommentText();
        if (false === strpos($phpdoc, self::DATABASE_PHPDOC_PARAM)) {
            $newLines = explode(PHP_EOL, self::DATABASE_PHPDOC_PARAM);
            $newLines[] = self::DATABASE_PHPDOC_INSERT_AT;


            $newPhpdoc = str_replace(self::DATABASE_PHPDOC_INSERT_AT, implode(PHP_EOL, $newLines), $phpdoc);
            $contents = str_replace($phpdoc, $newPhpdoc, $contents);
        }

        // Update param code
        $methodText = $listenMethod->compoundStatementOrSemicolon->getText();
        // indent each line 8 spaces
        $indent = str_repeat(' ', 8);
        $newLines = array_map(
            fn ($line) => $indent . $line,
            explode(PHP_EOL, self::DATABASE_REQUEST_PARAM_CODE)
        );
        if (false === strpos($methodText, $newLines[0])) {
            $newLines[] = self::DATABASE_REQUEST_PARAM_INSERT_AT;

            $newMethodText = str_replace(
                self::DATABASE_REQUEST_PARAM_INSERT_AT,
                implode(PHP_EOL, $newLines), $methodText
            );
            $contents = str_replace($methodText, $newMethodText, $contents);
        }

        $this->classNode = self::fromCode($contents);
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
