<?php
declare(strict_types=1);

namespace Google\Generator\Tests\Collections;

use PHPUnit\Framework\TestCase;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Collections\Vector;

final class PhpDocTest extends TestCase
{
    public function testPreFormatted(): void
    {
        $doc = PhpDoc::block(
            PhpDoc::preformattedText(Vector::new([
                'Line 1',
                'Line 2',
                'and finally, line 3'
            ]))
        )->toCode();
        $this->assertEquals("/**\n * Line 1\n * Line 2\n * and finally, line 3\n */\n", $doc);
    }

    public function testText(): void
    {
        $doc = PhpDoc::block(
            PhpDoc::text(
                'Some',
                'text',
                'which will be formatted to a fixed line length of 80 characters, with auto line wrapping at that point :)'
            )
        )->toCode();
        $this->assertEquals(
            "/**\n * Some text which will be formatted to a fixed line length of 80 characters, with\n * auto line wrapping at that point :)\n */\n",
            $doc);
    }

    public function testNewLine(): void
    {
        $doc = PhpDoc::block(
            PhpDoc::text(
                'one',
                PhpDoc::newLine(),
                'two'
            ),
        )->toCode();
        $this->assertEquals("/**\n * one\n * two\n */\n", $doc);
    }

    public function testExperimental(): void
    {
        $doc = PhpDoc::block(
            PhpDoc::experimental()
        )->toCode();
        $this->assertEquals("/** @experimental */\n", $doc);
    }
}
