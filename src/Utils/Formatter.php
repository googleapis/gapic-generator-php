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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

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

    private static function buildLineLengthFixer(int $lineLength): object
    {
        return new class($lineLength) {
            public function __construct(private int $lineLength)
            {
            }

            public function fix(\SplFileInfo $fileInfo, Tokens $tokens): void
            {
                for ($position = count($tokens) - 1; $position >= 0; --$position) {
                    $token = $tokens[$position];

                    if ($token->equals(')')) {
                        $this->processMethodCall($tokens, $position);
                        continue;
                    }

                    if ($token->isGivenKind([T_FUNCTION, CT::T_USE_LAMBDA, T_NEW])) {
                        $openIndex = $tokens->getNextTokenOfKind($position, ['(', ';']);
                        if ($openIndex === null || $tokens[$openIndex]->equals(';')) {
                            continue;
                        }
                        $nextMeaningful = $tokens->getNextMeaningfulToken($openIndex);
                        if ($nextMeaningful !== null && $tokens[$nextMeaningful]->equals(')')) {
                            continue;
                        }
                        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
                        $this->fixBlock($tokens, $openIndex, $closeIndex, false);
                        continue;
                    }

                    if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                        $openIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $position);
                        $this->fixBlock($tokens, $openIndex, $position, true);
                    }
                }
            }

            private function processMethodCall(Tokens $tokens, int $closeIndex): void
            {
                try {
                    $openIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $closeIndex);
                } catch (\Throwable) {
                    return;
                }
                $nameToken = $tokens[$openIndex - 1] ?? null;
                if ($nameToken === null || !$nameToken->isGivenKind(T_STRING)) {
                    return;
                }
                $content = $nameToken->getContent();
                if (!ctype_lower($content[0]) || $tokens[$openIndex + 1]->equals(')')) {
                    return;
                }
                if ($tokens->findGivenKind(T_COMMENT, $openIndex, $closeIndex) !== []) {
                    return;
                }
                $this->transformBlock($tokens, $openIndex, $closeIndex);
            }

            private function fixBlock(Tokens $tokens, int $openIndex, int $closeIndex, bool $isArray): void
            {
                if ($closeIndex - $openIndex <= 1) {
                    return;
                }
                if (array_filter($tokens->findGivenKind([T_START_HEREDOC, T_DOUBLE_ARROW, T_COMMENT], $openIndex, $closeIndex)) !== []) {
                    return;
                }
                $this->transformBlock($tokens, $openIndex, $closeIndex);
            }

            private function transformBlock(Tokens $tokens, int $openIndex, int $closeIndex): void
            {
                if ($this->resolveFirstLineLength($tokens, $openIndex) > $this->lineLength) {
                    $this->breakItems($tokens, $openIndex, $closeIndex);
                    return;
                }
                if ($this->resolveFullLineLength($tokens, $openIndex, $closeIndex) <= $this->lineLength) {
                    $this->inlineItems($tokens, $openIndex, $closeIndex);
                }
            }

            private function resolveFirstLineLength(Tokens $tokens, int $openIndex): int
            {
                $pos = $openIndex;
                $len = 0;
                while ($pos > 0 && !str_starts_with($tokens[$pos]->getContent(), "\n") && !$tokens[$pos]->isGivenKind(T_OPEN_TAG)) {
                    $len += strlen($tokens[$pos]->getContent());
                    --$pos;
                }
                $len += strlen($tokens[$pos]->getContent()) - substr_count($tokens[$pos]->getContent(), "\n");

                for ($pos = $openIndex + 1; isset($tokens[$pos]); ++$pos) {
                    if (str_starts_with($tokens[$pos]->getContent(), "\n") || $tokens[$pos]->isGivenKind(CT::T_USE_LAMBDA)) {
                        break;
                    }
                    $parts = explode("\n", $tokens[$pos]->getContent(), 2);
                    $len += strlen($parts[0]);
                    if (count($parts) > 1) {
                        break;
                    }
                }
                return $len;
            }

            private function resolveFullLineLength(Tokens $tokens, int $openIndex, int $closeIndex): int
            {
                $len = 0;
                $pos = $openIndex;
                while ($pos > 0 && !str_starts_with($tokens[$pos]->getContent(), "\n") && !$tokens[$pos]->isGivenKind(T_OPEN_TAG)) {
                    $len += strlen($tokens[$pos]->getContent());
                    --$pos;
                }
                $len += strlen($tokens[$pos]->getContent());

                for ($pos = $openIndex; $pos < $closeIndex && isset($tokens[$pos]); ++$pos) {
                    $len += $tokens[$pos]->isGivenKind(T_WHITESPACE) ? 1 : strlen($tokens[$pos]->getContent());
                }
                for ($pos = $closeIndex; isset($tokens[$pos]) && !str_starts_with($tokens[$pos]->getContent(), "\n"); ++$pos) {
                    $len += strlen($tokens[$pos]->getContent());
                }
                return $len;
            }

            private function detectIndentLevel(Tokens $tokens, int $startIndex): int
            {
                for ($i = $startIndex; $i > 0; --$i) {
                    $content = $tokens[$i]->getContent();
                    $lastNewlinePos = strrpos($content, "\n");
                    if ($tokens[$i]->isWhitespace() && trim($content, ' ') !== '') {
                        return substr_count($content, '    ', (int) $lastNewlinePos);
                    }
                    if ($lastNewlinePos !== false) {
                        return substr_count($content, '    ', $lastNewlinePos);
                    }
                }
                return 0;
            }

            private function breakItems(Tokens $tokens, int $openIndex, int $closeIndex): void
            {
                $indentLevel = $this->detectIndentLevel($tokens, $openIndex);
                $closingIndent = "\n" . str_repeat('    ', $indentLevel);
                $itemIndent = "\n" . str_repeat('    ', $indentLevel + 1);

                $tokens->ensureWhitespaceAtIndex($closeIndex - 1, 1, $closingIndent);

                for ($i = $closeIndex - 1; $i >= $openIndex; --$i) {
                    $token = $tokens[$i];
                    if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                        $i = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i);
                        continue;
                    }
                    if ($token->equals(')')) {
                        $i = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i);
                        continue;
                    }
                    if ($token->getContent() === ',') {
                        if (str_contains($tokens[$i + 1]->getContent(), "\n")) {
                            continue;
                        }
                        if ($tokens[$i + 2]->isComment()) {
                            continue;
                        }
                        $tokens->ensureWhitespaceAtIndex($i + 1, 0, $itemIndent);
                    }
                }

                if ($tokens[$openIndex + 1]->isGivenKind(T_WHITESPACE)) {
                    $tokens->ensureWhitespaceAtIndex($openIndex + 1, 0, $itemIndent);
                } else {
                    $tokens->ensureWhitespaceAtIndex($openIndex, 1, $itemIndent);
                }
            }

            private function inlineItems(Tokens $tokens, int $openIndex, int $closeIndex): void
            {
                for ($i = $openIndex + 1; $i < $closeIndex; ++$i) {
                    $currentToken = $tokens[$i];
                    if ($currentToken->getContent() === '{') {
                        $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $i);
                    } elseif ($currentToken->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                        $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i);
                    } elseif ($currentToken->isGivenKind(T_ARRAY)) {
                        $next = $tokens->getNextMeaningfulToken($i);
                        if ($next !== null) {
                            $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $next);
                        }
                    }
                    if (!$currentToken->isGivenKind(T_WHITESPACE)) {
                        continue;
                    }
                    $prev = $tokens[$i - 1];
                    $next = $tokens[$i + 1];
                    if ($prev->isGivenKind([T_START_HEREDOC, T_END_HEREDOC])) {
                        continue;
                    }
                    if (in_array($prev->getContent(), ['(', '['], true) || in_array($next->getContent(), [')', ']'], true)) {
                        $tokens->clearAt($i);
                        continue;
                    }
                    $tokens[$i] = new Token([T_WHITESPACE, ' ']);
                }
            }
        };
    }
}
