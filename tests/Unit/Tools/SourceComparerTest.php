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

namespace Google\Generator\Tests\Unit\Tools;

use PHPUnit\Framework\TestCase;
use Google\Generator\Tests\Tools\SourceComparer;

final class SourceComparerTest extends TestCase
{
    public function testCompareSuccessCases(): void
    {
        // Both values are empty.
        $this->assertTrue(SourceComparer::compare('', ''));

        // Identical strings.
        $stringOne = 'The quick brown fox
        jumped over
        the lazy
        dog';
        $this->assertTrue(SourceComparer::compare($stringOne, $stringOne));

        // Whitespace diffs.
        $stringTwo = 'The quick brown fox jumped over the lazy dog';
        $this->assertTrue(SourceComparer::compare($stringOne, $stringTwo));
        $this->assertTrue(SourceComparer::compare($stringTwo, $stringOne));

        $stringTwo = '    The quick
        brown fox  jumped
        over       the     lazy dog ';
        $this->assertTrue(SourceComparer::compare($stringOne, $stringTwo));
        $this->assertTrue(SourceComparer::compare($stringTwo, $stringOne));
    }

    public function testCompareFailureCases(): void
    {
        // One of the strings is empty.
        $stringOne = 'The quick brown fox jumped over the lazy dog';
        $this->assertFalse(SourceComparer::compare($stringOne, '', False));
        $this->assertFalse(SourceComparer::compare('', $stringOne, False));

        // Different values.
        $stringTwo = 'The Quick brown fox jumped over the lazy dog';
        $this->assertFalse(SourceComparer::compare($stringOne, $stringTwo, False));
        $this->assertFalse(SourceComparer::compare($stringTwo, $stringOne, False));

        // Substring.
        $this->assertFalse(SourceComparer::compare($stringOne, 'The', False));
        $this->assertFalse(SourceComparer::compare('The', $stringOne, False));
    }
}
