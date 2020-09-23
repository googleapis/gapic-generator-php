<?php declare(strict_types=1);

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
            new \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer(), // 50
            new \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer(), // 26
            new \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer(), // 2
            new \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer(), // 1
            new \PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer(), // 0
            new \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer(), // 0
            new \PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer(), // -21
            new \PhpCsFixer\Fixer\Basic\BracesFixer(), // -25
            new \PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer(), // -31
            new \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer(), // -50
        ];

        $tokens = \PhpCsFixer\Tokenizer\Tokens::fromCode($code);

        // All the fixers we'll use don't reference the file passed in, so use a dummy file.
        $fakeFile = new \SplFileInfo('');
        foreach ($fixers as $fixer) {
            $fixer->fix($fakeFile, $tokens);
        }

        $code = $tokens->generateCode();
        return $code;
    }
}
