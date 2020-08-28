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
        $this->assertEquals("Line 1\nLine 2\nand finally, line 3", $doc);
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
            "Some text which will be formatted to a fixed line length of 80 characters, with\nauto line wrapping at that point :)",
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
        $this->assertEquals("one\ntwo", $doc);
    }

    public function testExperimental(): void
    {
        $doc = PhpDoc::block(
            PhpDoc::experimental()
        )->toCode();
        $this->assertEquals('@experimental', $doc);
    }
}
