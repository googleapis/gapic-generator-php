<?php
declare(strict_types=1);

namespace Google\Generator\Tests\Collections;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Equality;

class Obj
{
}

class ObjEq implements Equality
{
    public function __construct($id) {
        $this->id = $id;
    }

    public function getHash(): int
    {
        return 42;
    }

    public function isEqualTo($other): bool
    {
        return $this->id === $other->id;
    }
}

final class MapTest extends TestCase
{
    public function testNew(): void
    {
        $m = Map::new([1 => 'one', 2 => 'two']);
        $this->assertEquals(2, count($m));
        $this->assertEquals('one', $m[1]);
        $this->assertEquals('two', $m[2]);
    }

    public function testObj(): void
    {
        $k1 = new Obj();
        $k2 = new Obj();
        $m = Map::fromPairs([
            [$k1, 1],
            [$k2, 2],
        ]);
        $this->assertEquals(2, count($m));
        $this->assertEquals(1, $m[$k1]);
        $this->assertEquals(2, $m[$k2]);
    }

    public function testIdenticalHash(): void
    {
        $m = Map::fromPairs([
            [new ObjEq(1), 1],
            [new ObjEq(2), 2],
            [42, 3],
            ["42", 4],
        ]);
        $this->assertEquals(4, count($m));
        $this->assertEquals(1, $m[new ObjEq(1)]);
        $this->assertEquals(2, $m[new ObjEq(2)]);
        $this->assertEquals(3, $m[42]);
        $this->assertEquals(4, $m["42"]);
    }

    public function testSet(): void
    {
        $m = Map::new();
        $m = $m->set(1, 'one');
        $this->assertEquals(1, count($m));
        $this->assertEquals('one', $m[1]);
        $m = $m->set(2, 'two');
        $this->assertEquals(2, count($m));
        $this->assertEquals('one', $m[1]);
        $this->assertEquals('two', $m[2]);
        $m = $m->set(2, 'TWO');
        $this->assertEquals(2, count($m));
        $this->assertEquals('one', $m[1]);
        $this->assertEquals('TWO', $m[2]);
        $m = $m->set(1, 'ONE');
        $this->assertEquals(2, count($m));
        $this->assertEquals('ONE', $m[1]);
        $this->assertEquals('TWO', $m[2]);
    }

    public function testForeach(): void
    {
        $m = Map::new()->set(1, 'one');
        $found = false;
        foreach ($m as [$k, $v]) {
            $this->assertEquals(1, $k);
            $this->assertEquals('one', $v);
            $found = true;
        }
        $this->assertTrue($found);
    }

    public function testFilter(): void
    {
        $m = Map::new()->set(1, 'one')->set(2, 'two');
        $m = $m->filter(fn($k, $v) => $k === 2);
        $this->assertCount(1, $m);
        $this->assertEquals('two', $m[2]);
    }

    public function testMapValues(): void
    {
        $m = Map::new()->set(1, 'one')->set(2, 'two');
        $m = $m->mapValues(fn($k, $v) => "{$k}:{$v}");
        $this->assertCount(2, $m);
        $this->assertEquals('1:one', $m[1]);
        $this->assertEquals('2:two', $m[2]);
    }

    public function testKeys(): void
    {
        $m = Map::new()->set(1, 'one')->set(2, 'two');
        $v = $m->keys();
        $this->assertCount(2, $v);
        $this->assertContains(1, $v);
        $this->assertContains(2, $v);
    }

    public function testValues(): void
    {
        $m = Map::new()->set(1, 'one')->set(2, 'two');
        $v = $m->values();
        $this->assertCount(2, $v);
        $this->assertContains('one', $v);
        $this->assertContains('two', $v);
    }
}