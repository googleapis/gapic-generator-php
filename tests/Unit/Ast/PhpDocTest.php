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

namespace Google\Generator\Tests\Unit\Collections;

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
            $doc
        );
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
