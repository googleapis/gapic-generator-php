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

namespace Google\Generator\Tests\Tools;

use Google\Generator\Tests\Tools\SourceComparer;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\PrettyPrinter;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

use PhpParser\NodeDumper;

class PhpClassComparer
{

  /**
   * Compares two PHP classes.
   * Assumes the LHS arg (nodesOne) is from the monolith, and the RHS one from the microgenerator.
   * @param phpClassOne the first PHP class as a string literal, such as the contents of a file.
   * @param phpClassTwo the first PHP class as a string literal, such as the contents of a file.
   * @return bool true if the classes are semantically equal, false otherwise.
   */
    public static function compare(string $phpClassOne, string $phpClassTwo, bool $printDiffs = true): bool
    {
        $phpParser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $astOne = static::parseToAst($phpParser, $phpClassOne, "mono", $printDiffs);
        $astTwo = static::parseToAst($phpParser, $phpClassTwo, "micro", $printDiffs);

        if (!$astOne || !$astTwo) {
          if ($printDiffs) {
            print("Failed to parse " . (!$astOne ? "micro" : "mono") . " AST");
          }
          return false;
        }

        // Class names.
        $astNodeFinder = new NodeFinder;
        $classOne = $astNodeFinder->findFirstInstanceOf($astOne, Class_::class);
        $classTwo = $astNodeFinder->findFirstInstanceOf($astTwo, Class_::class);
        $classOneName = is_null($classOne) ? "null" : $classOne->name->name;
        $classTwoName = is_null($classTwo) ? "null" : $classTwo->name->name;
        $diffFindings = [];
        if ($classOneName !== $classTwoName) {
            $diffFindings[] = "mono class name $classOneName != micro class name $classTwoName\n\n";
        }

        // TODO(miraleung): Namespace, properties, consts.

        // Class methods.
        $methodsOne = $astNodeFinder->findInstanceOf($astOne, ClassMethod::class);
        $methodsTwo = $astNodeFinder->findInstanceOf($astTwo, ClassMethod::class);
        $diffFindings =
          array_merge($diffFindings, static::diffAstNodes($methodsOne, $methodsTwo, "method", $printDiffs));

        // Functions.
        $functionsOne = $astNodeFinder->findInstanceOf($astOne, Function_::class);
        $functionsTwo = $astNodeFinder->findInstanceOf($astTwo, Function_::class);
        $diffFindings =
          array_merge($diffFindings, static::diffAstNodes($functionsOne, $functionsTwo, "function", $printDiffs));

        if (!empty($diffFindings)) {
            if ($printDiffs) {
                print(print_r($diffFindings) . "\n\n");
            }
            return false;
        }

        return true;
    }

    /**
     * Parses the given file into an AST.
     * @return string|false the ast or false if parsing failed.
     */
    private static function parseToAst($phpParser, string $phpClass, string $astLabel, bool $printDiffs = true)
    {
        try {
            $ast = $phpParser->parse($phpClass);
        } catch (Error $error) {
          if ($printDiffs) {
            print("Could not parse AST for $astLabel: {$error->getMessage()}\n");
          }
            return null;
        }

        return $ast;
    }

    /**
     * Diffs two lists of AST nodes. Assumes the LHS arg (nodesOne) is from the monolith,
     * and the RHS one from the microgenerator.
     * @return string[] text diffs of the nodes in source form.
     */
    private static function diffAstNodes($nodesOne, $nodesTwo, $nodeTypeName, bool $printDiffs = true): array
    {
        $astNodeCmp = function ($nodeA, $nodeB) {
            return strcmp($nodeA->name->name, $nodeB->name->name);
        };
        usort($nodesOne, $astNodeCmp);
        usort($nodesTwo, $astNodeCmp);

        $nodeMapOne = [];
        foreach ($nodesOne as $node) {
            $nodeMapOne[$node->name->name] = $node;
        }
        $nodeMapTwo = [];
        foreach ($nodesTwo as $node) {
            $nodeMapTwo[$node->name->name] = $node;
        }

        $diffFindings = [];
        $astPrinter = new PrettyPrinter\Standard;
        $allNodeNames = array_unique(array_merge(array_keys($nodeMapOne), array_keys($nodeMapTwo)));
        sort($allNodeNames);
        foreach ($allNodeNames as $nodeName) {
            if (!array_key_exists($nodeName, $nodeMapOne)) {
                $diffFindings[] = "mono missing $nodeTypeName $nodeName, which is in micro\n\n";
                continue;
            }
            if (!array_key_exists($nodeName, $nodeMapTwo)) {
                $diffFindings[] = "mono has $nodeTypeName $nodeName, which is missing from micro\n\n";
                continue;
            }
            $nodeStringOne = $astPrinter->prettyPrint(array($nodeMapOne[$nodeName]));
            $nodeStringTwo = $astPrinter->prettyPrint(array($nodeMapTwo[$nodeName]));
            $sourceIdentical = SourceComparer::compare($nodeStringOne, $nodeStringTwo, $printDiffs);
            if (!$sourceIdentical) {
                $diffFindings[] = "Diff found in $nodeTypeName $nodeName for mono and micro";
            }
        }
        return $diffFindings;
    }
}
