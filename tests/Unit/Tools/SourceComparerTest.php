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

namespace Google\Generator\Tests\Unit\Tools;

use PHPUnit\Framework\TestCase;
use Google\Generator\Tests\Tools\SourceComparer;

final class SourceComparerTest extends TestCase
{
    public function testCompareJsonBasicSuccessCases(): void
    {
        // Both values are empty.
        $this->assertTrue(SourceComparer::compareJson('', ''));

        // Sane order.
        $monoJson = '{"a" : 1, "b" : 2, "c" : 3}';
        $microJson = '{"a" : 1, "b" : 2, "c" : 3}';
        $this->assertTrue(SourceComparer::compareJson($monoJson, $microJson));

        // Different order.
        $microJson = '{"c" : 3, "a" : 1, "b" : 2}';
        $this->assertTrue(SourceComparer::compareJson($monoJson, $microJson));
        $this->assertTrue(SourceComparer::compareJson($microJson, $monoJson));
    }

    public function testCompareJsonBasicFailureCases(): void
    {
        // One of the JSON strings is empty.
        $jsonString = '{"a" : 1, "b" : 2, "c" : 3}';
        $this->assertFalse(SourceComparer::compareJson($jsonString, '', False));
        $this->assertFalse(SourceComparer::compareJson('', $jsonString, False));

        // One different value.
        $jsonOne = '{"a" : 1, "b" : 2, "c" : 3}';
        $jsonTwo = '{1 : "a", "b" : 2, "c" : 3}';
        $this->assertFalse(SourceComparer::compareJson($jsonOne, $jsonTwo, False));
        $this->assertFalse(SourceComparer::compareJson($jsonTwo, $jsonOne, False));

        // One extra value.
        $jsonTwo = '{1 : "a", "a" : 1, "b" : 2, "c" : 3}';
        $this->assertFalse(SourceComparer::compareJson($jsonOne, $jsonTwo, False));
        $this->assertFalse(SourceComparer::compareJson($jsonTwo, $jsonOne, False));
    }
}