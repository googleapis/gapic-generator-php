<?php
/*
 * Copyright 2026 Google LLC
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

namespace Google\Generator\Tests\Unit\Utils;

use PHPUnit\Framework\TestCase;
use Google\Generator\Utils\Formatter;

final class FormatterTest extends TestCase
{
    /**
     * One case per fixer that appears in the second half of Formatter::format()'s fixer
     * list. Appending that half with the array union operator instead of a merge dropped
     * as many entries as the first half contains, so none of these fixers ran. The
     * dropped count depended on whether the optional line-length fixer was added, so
     * every case is exercised on both call paths.
     *
     * @return array
     */
    public function droppedFixerProvider(): array
    {
        return [
            'NoSpacesAfterFunctionNameFixer' => [
                "<?php\nfoo ();\n",
                'foo();',
            ],
            'NoSpacesInsideParenthesisFixer' => [
                "<?php\nfoo( 1 );\n",
                'foo(1);',
            ],
            'NoEmptyCommentFixer' => [
                "<?php\n//\n\$a = 1;\n",
                '$a = 1;',
            ],
            'SingleImportPerStatementFixer' => [
                "<?php\nuse Foo\\A, Foo\\B;\n\$a = new A();\n\$b = new B();\n",
                "use Foo\\A;\nuse Foo\\B;",
            ],
            'PhpdocLineSpanFixer' => [
                "<?php\nclass C\n{\n    /** @var int */\n    private \$a = 1;\n}\n",
                "/**\n     * @var int\n     */",
            ],
            'ElseifFixer' => [
                "<?php\nif (\$a) {\n    \$b = 1;\n} else if (\$c) {\n    \$b = 2;\n}\n",
                '} elseif',
            ],
            'NoTrailingWhitespaceFixer' => [
                "<?php\n\$a = 1;   \n\$b = 2;\n",
                "\$a = 1;\n\$b = 2;",
            ],
            'NoTrailingWhitespaceInCommentFixer' => [
                "<?php\n// a comment   \n\$a = 1;\n",
                "// a comment\n",
            ],
        ];
    }

    /**
     * @dataProvider droppedFixerProvider
     */
    public function testDroppedFixerRuns(string $code, string $expectedSubstring): void
    {
        $this->assertStringContainsString($expectedSubstring, Formatter::format($code));
        // The snippet call path passes a line length, which changes the length of the
        // first half of the list and so used to drop a different number of fixers.
        $this->assertStringContainsString($expectedSubstring, Formatter::format($code, 100));
    }

    public function testBlankLineAfterOpeningTag(): void
    {
        // Annotated in Formatter as "Critical to preserving sample code".
        $formatted = Formatter::format("<?php\n\$a = 1;\n");
        $this->assertStringStartsWith("<?php\n\n\$a = 1;", $formatted);
        $formatted = Formatter::format("<?php\n\$a = 1;\n", 100);
        $this->assertStringStartsWith("<?php\n\n\$a = 1;", $formatted);
    }

    public function testLinebreakAfterOpeningTag(): void
    {
        // This one was only dropped on the line-length (snippet) call path.
        $this->assertStringStartsWith("<?php\n", Formatter::format("<?php \$a = 1;\n"));
        $this->assertStringStartsWith("<?php\n", Formatter::format("<?php \$a = 1;\n", 100));
    }

    public function testLineEndingFixer(): void
    {
        $formatted = Formatter::format("<?php\r\n\$a = 1;\r\n");
        $this->assertStringNotContainsString("\r", $formatted);
        $formatted = Formatter::format("<?php\r\n\$a = 1;\r\n", 100);
        $this->assertStringNotContainsString("\r", $formatted);
    }

    public function testLineLengthFixerStillRuns(): void
    {
        // The line-length fixer sits between the two halves of the list; check the merge
        // did not displace it.
        $code = "<?php\nfoo('" . str_repeat('a', 200) . "', 'b');\n";
        $this->assertStringNotContainsString("\n", trim(substr(Formatter::format($code), 6)));
        $this->assertStringContainsString("\n    '" . str_repeat('a', 200), Formatter::format($code, 100));
    }
}
