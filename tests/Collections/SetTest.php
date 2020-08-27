<?php
declare(strict_types=1);

namespace Google\Generator\Tests\Collections;

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
}