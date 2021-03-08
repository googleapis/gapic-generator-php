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
use Google\Generator\Collections\Set;
use Google\Generator\Collections\Equality;

final class SetTest extends TestCase
{
    public function testNew(): void
    {
        $s = Set::new([1, 2]);
        $this->assertCount(2, $s);
    }

    public function testForeach(): void
    {
        $s = Set::new([1]);
        $found = false;
        foreach ($s as $x) {
            $this->assertEquals(1, $x);
            $found = true;
        }
        $this->assertTrue($found);
    }

    public function testSet(): void
    {
        $s = Set::new([1, 2]);
        $this->assertCount(2, $s);
        $this->assertTrue($s[1]);
        $this->assertTrue($s[2]);
        $this->assertFalse($s[3]);
        $this->assertFalse($s[0]);
        $this->assertFalse($s['1']);
        $this->assertFalse($s['2']);
    }

    public function testAdd(): void
    {
        $s = Set::new();
        $s = $s->add(1);
        $this->assertCount(1, $s);
        $s = $s->add(1);
        $this->assertCount(1, $s);
        $s = $s->add(2);
        $this->assertCount(2, $s);
        $s = $s->add(1);
        $s = $s->add(2);
        $this->assertCount(2, $s);
    }

    public function testToVector(): void
    {
        $s = Set::new([1, 2]);
        $v = $s->toVector();
        $this->assertCount(2, $v);
        $this->assertContains(1, $v);
        $this->assertContains(2, $v);
    }
}