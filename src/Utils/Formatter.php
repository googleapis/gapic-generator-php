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
        // Fixers must be in priority order; the priority is the number in the comment.
        // More fixers can be added as required to achieve the formatting we desire.
        $fixers = [
            new \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer(), // 55
            new \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer(), // 50
            new \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer(), // 26
            new \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer(), // 1
            new \PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer(), // 0
            new \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer(), // 0
            new \PhpCsFixer\Fixer\Basic\BracesFixer(), // -25
            new \PhpCsFixer\Fixer\Import\OrderedImportsFixer(), // -30
            new \PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer(), // -31
            new \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer(), // -50
        ];
        // Fixer temporarily removed:
        // new \PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer(), // -21
        // TODO: Understand why this fixer causes too many blank line insertions in some cases.

        try
        {
            $tokens = \PhpCsFixer\Tokenizer\Tokens::fromCode($code);

            // All the fixers we'll use don't reference the file passed in, so use a dummy file.
            $fakeFile = new \SplFileInfo('');
            foreach ($fixers as $fixer) {
                $fixer->fix($fakeFile, $tokens);
            }

            $code = $tokens->generateCode();
            return $code;
        } catch (\Throwable $ex) {
            print("\nFailed to format code:\n{$code}\n");
            throw $ex;
        }
    }
}
