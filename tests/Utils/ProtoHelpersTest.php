<?php declare(strict_types=1);

namespace Google\Generator\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Google\Generator\Tests\ProtoTrait;
use Google\Generator\Utils\ProtoHelpers;

final class ProtoHelpersTest extends TestCase
{
    use ProtoTrait;

    public function testCustomOptions(): void
    {
        $file = $this->loadDescriptor('Utils/custom_options.proto');

        // Check custom options are loaded successfully.
        $this->assertEquals(42, ProtoHelpers::getCustomOption($file, 2000));
        $this->assertEquals('stringy', ProtoHelpers::getCustomOption($file, 2001));
        $this->assertEquals([8, 9, 10], ProtoHelpers::getCustomOptionRepeated($file, 2002)->toArray());
        $this->assertEquals(['s1', 's2'], ProtoHelpers::getCustomOptionRepeated($file, 2003)->toArray());
    }
}
