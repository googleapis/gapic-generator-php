<?php
declare(strict_types=1);

namespace Google\Generator\Tests\Generation;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Generation\Type;

final class TypeTest extends TestCase
{
    public function testFromName(): void
    {
        $type = Type::fromName('\\A\\B\\MyType');
        $this->assertTrue($type->isClass());
        $this->assertEquals('A\\B', $type->getNamespace());
        $this->assertEquals('MyType', $type->name);
        $this->assertEquals('\\A\\B\\MyType', $type->getFullname());
    }
}
