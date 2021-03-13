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

namespace Google\Generator\Utils;

use Google\Generator\Collections\Vector;

class Formatter
{
    /**
     * Format PHP code.
     *
     * Note that this does not quite perform all required formatting, for example it does
     * not turn long single-line array declarations into multi-line. So a small amount of
     * formatting is done by the code generation in the AST classes.
     *
     * @param string $code Unformatted code, to be formatted.
     *
     * @return string The same code as passed in, but formatted.
     */
    public static function format(string $code): string
    {
        $psr2SingleClassElementPerStatementFixer =
          new \PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
        $psr2SingleClassElementPerStatementFixer->configure(['elements' => ['property']]);
        $psr2MethodArgumentSpaceFixer =
          new \PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
        $psr2MethodArgumentSpaceFixer->configure(['on_multiline' => 'ensure_fully_multiline']);
        // Same rules as in PSR2.
        $fixers = [
          new \PhpCsFixer\Fixer\Basic\EncodingFixer(), // 100, PSR2
          new \PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer(), // 98, PSR2

          // No priority provided.
          new \PhpCsFixer\Fixer\Casing\ConstantCaseFixer(),
          new \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer(),
          new \PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer(),
          new \PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer(),
          new \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer(),
          new \PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer(),

          // Ordered.
          $psr2SingleClassElementPerStatementFixer,  // 56, PSR2
          new \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer(), // 55
          new \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer(), // 50, PSR2
          new \PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer(), // 2, PSR2
          new \PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer(), // 2
          new \PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer(), // 2, PSR2
          new \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer(), // 1, Critical to preserving sample code.
          new \PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer(), // 1, PSR2
          new \PhpCsFixer\Fixer\ControlStructure\ElseifFixer(), // 0, PSR2
          new \PhpCsFixer\Fixer\Whitespace\LineEndingFixer(), // 0, PSR2
          new \PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer(), // 0, PSR2,
          new \PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer(), // 0, PSR2
          new \PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer(), // 0, PSR2
          new \PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer(), // 0
          new \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer(), // 0
          new \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer(), // 0, PSR2
          new \PhpCsFixer\Fixer\Import\NoUnusedImportsFixer(), // -10
          new \PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer(), // -11, PSR2
          new \PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer(), // -19
          new \PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer(), // -20, PSR2
          new \PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer(), // -20
          new \PhpCsFixer\Fixer\Basic\BracesFixer(), // -25, PSR2
          $psr2MethodArgumentSpaceFixer, // -30, PSR2
          new \PhpCsFixer\Fixer\Import\OrderedImportsFixer(), // -30
          new \PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer(), // -31
          new \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer(), // -50, PSR2 (must run last)
        ];

        // Fixer temporarily removed:
        // new \PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer(), // -21
        // TODO: Understand why this fixer causes too many blank line insertions in some cases.

        try {
            $tokens = \PhpCsFixer\Tokenizer\Tokens::fromCode($code);

            // All the fixers we'll use don't reference the file passed in, so use a dummy file.
            $fakeFile = new \SplFileInfo('');
            foreach ($fixers as $fixer) {
                $fixer->fix($fakeFile, $tokens);
            }
            // This must run last, otherwise it collapses comments immediately succeeding blocks that may have semicolons.
            $semicolonFixer = new \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer();
            $semicolonFixer->fix($fakeFile, $tokens);


            $code = $tokens->generateCode();
            // TODO(vNext): Remove this call.
            $code = static::orderUse($code);

            return $code;
        } catch (\Throwable $ex) {
            $codeWithLineNumbers = Vector::new(explode("\n", $code))->map(fn ($x, $i) => "{$i}: {$x}")->join("\n");
            print("\nFailed to format code:\n{$codeWithLineNumbers}\n");
            throw $ex;
        }
    }

    // TODO(vNext): Remove this method when no longer required.
    // Monolith orders 'use' statements by ASCII order, whereas they should be ordered case-insensitively.
    private static function orderUse(string $codeStr): string
    {
        $code = Vector::new(explode("\n", $codeStr));
        $pre = $code->takeWhile(fn ($x) => strpos($x, 'use ') !== 0);
        $usings = $code->skip(count($pre))->takeWhile(fn ($x) => strpos($x, 'use ') === 0);
        $post = $code->skip(count($pre) + count($usings));

        $usings = $usings->orderBy(fn ($x) => $x);

        return $pre->concat($usings)->concat($post)->join("\n");
    }

    // TODO(vNext): Remove this method when no longer required.
    public static function moveUseTo(string $codeStr, string $typeName, int $index): string
    {
        $code = Vector::new(explode("\n", $codeStr));
        $pre = $code->takeWhile(fn ($x) => strpos($x, 'use ') !== 0);
        $usings = $code->skip(count($pre))->takeWhile(fn ($x) => strpos($x, 'use ') === 0);
        $post = $code->skip(count($pre) + count($usings));

        $line = "use {$typeName};";
        if (!$usings->any(fn ($x) => $x === $line)) {
            return $codeStr;
        }
        $usings = $usings->filter(fn ($x) => $x !== $line);
        $index = $index >= 0 ? $index : count($usings) + $index + 1;
        $usings = $usings->take($index)->append($line)->concat($usings->skip($index));

        return $pre->concat($usings)->concat($post)->join("\n");
    }
}
