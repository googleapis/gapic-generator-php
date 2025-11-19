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
use PhpCsFixer\Fixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer as SymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer;
use Symplify\CodingStandard\TokenRunner;

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
     * @param int $lineLength A line length to adhere the formatted code to.
     *
     * @return string The same code as passed in, but formatted.
     */
    public static function format(string $code, ?int $lineLength = null): string
    {
        $psr2SingleClassElementPerStatementFixer =
          new \PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer();
        $psr2SingleClassElementPerStatementFixer->configure(['elements' => ['property']]);
        $psr2MethodArgumentSpaceFixer =
          new \PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer();
        $psr2MethodArgumentSpaceFixer->configure(['on_multiline' => 'ensure_fully_multiline']);
        $visibilityFixer = new Fixer\ClassNotation\VisibilityRequiredFixer();
        $visibilityFixer->configure(['elements' => ['property', 'method']]);
        // Same rules as in PSR2.
        $fixers = [
              new Fixer\Basic\EncodingFixer(), // 100, PSR2
              new Fixer\PhpTag\FullOpeningTagFixer(), // 98, PSR2

              // No priority provided.
              new Fixer\Casing\ConstantCaseFixer(),
              new Fixer\FunctionNotation\FunctionDeclarationFixer(),
              new Fixer\Casing\LowercaseKeywordsFixer(),
              new Fixer\PhpTag\NoClosingTagFixer(),
              new Fixer\ControlStructure\SwitchCaseSpaceFixer(),
              $visibilityFixer,
              // Ordered.
              $psr2SingleClassElementPerStatementFixer,  // 56, PSR2
              new Fixer\ClassNotation\ClassAttributesSeparationFixer(), // 55
              new Fixer\Whitespace\IndentationTypeFixer(), // 50, PSR2
        ];

        if ($lineLength) {
            $fixers[] = self::buildLineLengthFixer($lineLength);
        }

        $fixers += [
            new Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer(), // 2, PSR2
            new Fixer\Comment\NoEmptyCommentFixer(), // 2
            new Fixer\Whitespace\NoSpacesInsideParenthesisFixer(), // 2, PSR2
            new Fixer\PhpTag\BlankLineAfterOpeningTagFixer(), // 1, Critical to preserving sample code.
            new Fixer\Import\SingleImportPerStatementFixer(), // 1, PSR2
            new Fixer\Phpdoc\PhpdocLineSpanFixer(), // 0, Multiline comment.
            new Fixer\ControlStructure\ElseifFixer(), // 0, PSR2
            new Fixer\Whitespace\LineEndingFixer(), // 0, PSR2
            new Fixer\Whitespace\NoTrailingWhitespaceFixer(), // 0, PSR2,
            new Fixer\Comment\NoTrailingWhitespaceInCommentFixer(), // 0, PSR2
            new Fixer\ControlStructure\NoBreakCommentFixer(), // 0, PSR2
            new Fixer\PhpTag\LinebreakAfterOpeningTagFixer(), // 0
            new Fixer\ClassNotation\ClassDefinitionFixer(), // 0
            new Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer(), // 0, PSR2
            new Fixer\Import\NoUnusedImportsFixer(), // -10
            new Fixer\Import\SingleLineAfterImportsFixer(), // -11, PSR2
            new Fixer\Comment\SingleLineCommentStyleFixer(), // -19
            new Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer(), // -20, PSR2
            new Fixer\Whitespace\NoExtraBlankLinesFixer(), // -20
            new Fixer\Basic\BracesFixer(), // -25, PSR2
            $psr2MethodArgumentSpaceFixer, // -30, PSR2
            new Fixer\Import\OrderedImportsFixer(), // -30
            new Fixer\Whitespace\ArrayIndentationFixer(), // -31
            new Fixer\Whitespace\MethodChainingIndentationFixer(), // -34
            new Fixer\Whitespace\SingleBlankLineAtEofFixer(), // -50, PSR2 (must run last)
        ];

        // Fixer temporarily removed:
        // new Fixer\Whitespace\BlankLineBeforeStatementFixer(), // -21
        // TODO: Understand why this fixer causes too many blank line insertions in some cases.

        try {
            $tokens = Tokens::fromCode($code);

            // All the fixers we'll use don't reference the file passed in, so use a dummy file.
            $fakeFile = new \SplFileInfo('');
            foreach ($fixers as $fixer) {
                $fixer->fix($fakeFile, $tokens);
            }
            // This must run last, otherwise it collapses comments immediately succeeding blocks that may have semicolons.
            $semicolonFixer = new Fixer\Semicolon\NoEmptyStatementFixer();
            $semicolonFixer->fix($fakeFile, $tokens);

            $code = $tokens->generateCode();
            // TODO(vNext): Remove this call.
            $code = static::orderUse($code);

            return $code;
        } catch (\Throwable $ex) {
            $codeWithLineNumbers = Vector::new(explode("\n", $code))->map(fn ($x, $i) => "{$i}: {$x}")->join("\n");
            print("\nFailed to format code: {$ex->getMessage()}\n{$codeWithLineNumbers}\n");
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

    // TODO: Investigate if there are more succinct ways to build out this fixer
    private static function buildLineLengthFixer(int $lineLength)
    {
        $blockFinder = new TokenRunner\Analyzer\FixerAnalyzer\BlockFinder();
        $tokenSkipper = new TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper(
            $blockFinder
        );
        $callAnalyzer = new TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer();
        $whitespacesFixerConfig = new WhitespacesFixerConfig();

        $fixer = new SymplifyFixer\LineLength\LineLengthFixer(
            new TokenRunner\Transformer\FixerTransformer\LineLengthTransformer(
                new TokenRunner\Transformer\FixerTransformer\LineLengthResolver(),
                new TokenRunner\Transformer\FixerTransformer\TokensInliner(
                    $tokenSkipper
                ),
                new TokenRunner\Transformer\FixerTransformer\FirstLineLengthResolver(
                    new TokenRunner\ValueObjectFactory\LineLengthAndPositionFactory()
                ),
                new TokenRunner\Transformer\FixerTransformer\TokensNewliner(
                    new TokenRunner\Transformer\FixerTransformer\LineLengthCloserTransformer(
                        $callAnalyzer,
                        new TokenRunner\TokenFinder()
                    ),
                    $tokenSkipper,
                    new TokenRunner\Transformer\FixerTransformer\LineLengthOpenerTransformer(
                        $callAnalyzer
                    ),
                    $whitespacesFixerConfig,
                    new TokenRunner\Whitespace\IndentResolver(
                        new TokenRunner\Analyzer\FixerAnalyzer\IndentDetector(
                            $whitespacesFixerConfig
                        ),
                        $whitespacesFixerConfig
                    )
                )
            ),
            $blockFinder,
            new TokenAnalyzer\FunctionCallNameMatcher()
        );

        $fixer->configure([
            'line_length' => $lineLength
        ]);

        return $fixer;
    }
}
