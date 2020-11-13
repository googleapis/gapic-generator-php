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

namespace Google\Generator\Tests\Collections;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;

final class VectorTest extends TestCase
{
    public function testNew(): void
    {
        $v = Vector::new(['one', 'two']);
        $this->assertEquals(['one', 'two'], $v->toArray());
    }

    public function testZip(): void
    {
        $v = Vector::zip(
            Vector::new([1, 2, 3]),
            Vector::new(['a', 'b']),
        );
        $this->assertEquals([[1, 'a'], [2, 'b']], $v->toArray());

        $v = Vector::zip(
            Vector::new([1, 2]),
            Vector::new(['a', 'b', 'c']),
            fn($a, $b) => "{$a}:{$b}"
        );
        $this->assertEquals(['1:a', '2:b'], $v->toArray());
    }

    public function testZipIndex(): void
    {
        $v = Vector::zip(
            Vector::new([1, 2]),
            Vector::new(['a', 'b', 'c']),
            fn($a, $b, $index) => "{$index}:{$a}:{$b}"
        );
        $this->assertEquals(['0:1:a', '1:2:b'], $v->toArray());
    }

    public function testRange(): void
    {
        $this->assertEquals([3, 4, 5], Vector::range(3, 5)->toArray());
    }

    public function testEquality(): void
    {
        $this->assertTrue(Vector::new()->isEqualTo(Vector::new()));
        $this->assertFalse(Vector::new([1])->isEqualTo(Vector::new()));
        $this->assertFalse(Vector::new()->isEqualTo(Vector::new([1])));
        $this->assertTrue(Vector::new([1])->isEqualTo(Vector::new([1])));
        $this->assertTrue(Vector::new([1, 'a'])->isEqualTo(Vector::new([1, 'a'])));
        $this->assertFalse(Vector::new(['a', 1])->isEqualTo(Vector::new([1, 'a'])));
        $this->assertTrue(Vector::new([1, Vector::new([1])])->isEqualTo(Vector::new([1, Vector::new([1])])));
        $this->assertFalse(Vector::new([1, Vector::new([1])])->isEqualTo(Vector::new([1, Vector::new(['1'])])));
    }

    public function testPrepend(): void
    {
        $v0 = Vector::new([]);
        $v1 = $v0->prepend(2);
        $v2 = $v1->prepend(1);
        $this->assertEquals([], $v0->toArray());
        $this->assertEquals([2], $v1->toArray());
        $this->assertEquals([1, 2], $v2->toArray());
    }

    public function testAppend(): void
    {
        $v0 = Vector::new([]);
        $v1 = $v0->append(1);
        $v2 = $v1->append(2);
        $this->assertEquals([], $v0->toArray());
        $this->assertEquals([1], $v1->toArray());
        $this->assertEquals([1, 2], $v2->toArray());
    }

    public function testConcat(): void
    {
        $this->assertEquals([1, 2], Vector::new()->concat(Vector::new([1, 2]))->toArray());
        $this->assertEquals([1, 2], Vector::new([1])->concat(Vector::new([2]))->toArray());
        $this->assertEquals([1, 2], Vector::new([1, 2])->concat(Vector::new())->toArray());
    }

    public function testMap(): void
    {
        $v = Vector::new(['one', 'two']);
        $v = $v->map(fn($x) => "{$x}{$x}");
        $this->assertEquals(['oneone', 'twotwo'], $v->toArray());
    }

    public function testMapIndex(): void
    {
        $v = Vector::new(['one', 'two']);
        $v = $v->map(fn($x, $index) => "{$index}{$x}");
        $this->assertEquals(['0one', '1two'], $v->toArray());
    }

    public function testFlatMap(): void
    {
        $v = Vector::new([1, 2]);
        $v = $v->flatMap(fn($x) => Vector::new([$x, $x]));
        $this->assertEquals([1, 1, 2, 2], $v->toArray());
    }

    public function testFlatMapIndex(): void
    {
        $v = Vector::new([1, 2]);
        $v = $v->flatMap(fn($x, $index) => Vector::new([$index, $x]));
        $this->assertEquals([0, 1, 1, 2], $v->toArray());
    }

    public function testFlatten(): void
    {
        $v1 = Vector::new([1, 2]);
        $v2 = Vector::new([3, Vector::new([4, 5])]);
        $this->assertEquals([1, 2, 3, 4, 5], Vector::new([$v1, $v2])->flatten()->toArray());
        $this->assertEquals(['a', 'b', 'c', 'd'],
            Vector::new([Vector::new(['a', 'b']), Vector::new(['c', 'd'])])->flatten()->toArray());
    }

    public function testGroupBy(): void
    {
        $v = Vector::new(['1:a', '2:b', '2:c', '3:d', '3:e', '3:f']);
        $g = $v->groupBy(fn($x) => intval(explode(':', $x)[0]), fn($x) => explode(':', $x)[1]);
        $this->assertEquals(3, count($g));
        $this->assertEquals(['a'], $g[1]->toArray());
        $this->assertEquals(['b', 'c'], $g[2]->toArray());
        $this->assertEquals(['d', 'e', 'f'], $g[3]->toArray());
    }

    public function testDistinct(): void
    {
        $v = Vector::new([1, 1, 1, 2, 3, 3]);
        $v = $v->distinct();
        $this->assertEquals([1, 2, 3], $v->toArray());
    }

    public function testTake(): void
    {
        $v = Vector::new([1, 2, 3]);
        $this->assertEquals([], $v->take(0)->toArray());
        $this->assertEquals([1], $v->take(1)->toArray());
        $this->assertEquals([1, 2, 3], $v->take(3)->toArray());
        $this->assertEquals([1, 2 ,3], $v->take(4)->toArray());
    }

    public function testTakeLast(): void
    {
        $v = Vector::new([1, 2, 3]);
        $this->assertEquals([], $v->takeLast(0)->toArray());
        $this->assertEquals([3], $v->takeLast(1)->toArray());
        $this->assertEquals([1, 2, 3], $v->takeLast(3)->toArray());
        $this->assertEquals([1, 2 ,3], $v->takeLast(4)->toArray());
    }

    public function testTakeWhile(): void
    {
        $v = Vector::new([1, 2, 0]);
        $this->assertEquals([1, 2, 0], $v->takeWhile(fn($x) => $x < 3)->toArray());
        $this->assertEquals([1, 2], $v->takeWhile(fn($x) => $x > 0)->toArray());
        $this->assertEquals([], $v->takeWhile(fn($x) => $x > 3)->toArray());
    }

    public function testSkip(): void
    {
        $v = Vector::new([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $v->skip(0)->toArray());
        $this->assertEquals([2, 3], $v->skip(1)->toArray());
        $this->assertEquals([], $v->skip(3)->toArray());
        $this->assertEquals([], $v->skip(4)->toArray());
    }

    public function testSkipLast(): void
    {
        $v = Vector::new([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $v->skipLast(0)->toArray());
        $this->assertEquals([1, 2], $v->skipLast(1)->toArray());
        $this->assertEquals([], $v->skipLast(3)->toArray());
        $this->assertEquals([], $v->skipLast(4)->toArray());
    }

    public function testSkipWhile(): void
    {
        $v = Vector::new([1, 2, 0]);
        $this->assertEquals([1, 2, 0], $v->skipWhile(fn($x) => $x < 1)->toArray());
        $this->assertEquals([2, 0], $v->skipWhile(fn($x) => $x < 2)->toArray());
        $this->assertEquals([], $v->skipWhile(fn($x) => $x < 3)->toArray());
    }

    public function testSkipLastWhile(): void
    {
        $v = Vector::new([1, 2, 0]);
        $this->assertEquals([1, 2, 0], $v->skipLastWhile(fn($x) => $x < 0)->toArray());
        $this->assertEquals([1, 2], $v->skipLastWhile(fn($x) => $x < 2)->toArray());
        $this->assertEquals([], $v->skipLastWhile(fn($x) => $x < 3)->toArray());
    }

    public function testLast(): void
    {
        $v = Vector::new([1, 2]);
        $this->assertEquals(2, $v->last());
    }

    public function testAny(): void
    {
        $v = Vector::new([1, 2]);
        $this->assertTrue($v->any(fn($x) => $x === 1));
        $this->assertFalse($v->any(fn($x) => $x === 11));
        $this->assertTrue($v->any());
        $this->assertFalse(Vector::new()->any());
    }

    public function testJoin(): void
    {
        $v = Vector::new(['a', 'b', 'c']);
        $this->assertEquals('a:b:c', $v->join(':'));
    }

    public function testContains(): void
    {
        $v = Vector::new([1, 2]);
        $this->assertFalse($v->contains(0));
        $this->assertTrue($v->contains(1));
        $this->assertTrue($v->contains(2));
        $this->assertFalse($v->contains(3));
    }

    public function testToMap(): void
    {
        $v = Vector::new(['1:one', '2:two']);
        $m = $v->toMap(
            fn($x) => intval(explode(':', $x)[0]),
            fn($x) => explode(':', $x)[1]);
        $this->assertCount(2, $m);
        $this->assertEquals('one', $m[1]);
        $this->assertEquals('two', $m[2]);
    }

    public function testToSet(): void
    {
        $v = Vector::new([1, 2, 3]);
        $s = $v->toSet();
        $this->assertCount(3, $s);
        $this->assertFalse($s[0]);
        $this->assertTrue($s[1]);
        $this->assertTrue($s[2]);
        $this->assertTrue($s[3]);
        $this->assertFalse($s[4]);
    }

    public function testMax(): void
    {
        $this->assertEquals(3, Vector::new([1, 2, 3])->max());
        $this->assertEquals(3, Vector::new()->max(3));
    }

    public function testForeach(): void
    {
        $v = Vector::new([1]);
        $found = false;
        foreach ($v as $x) {
            $this->assertEquals(1, $x);
            $found = true;
        }
        $this->assertTrue($found);
    }

    public function testToArrayAssoc(): void
    {
        $v = Vector::new([1, 2]);
        $a = $v->toArray(fn($x) => "key_{$x}");
        $this->assertEquals(['key_1' => 1, 'key_2' => 2], $a);
        $a = $v->toArray(fn($x) => "key_{$x}", fn($x) => $x * 10);
        $this->assertEquals(['key_1' => 10, 'key_2' => 20], $a);
    }
}
