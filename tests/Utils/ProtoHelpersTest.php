<?php declare(strict_types=1);

namespace Google\Generator\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Google\Generator\Tests\ProtoTrait;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\SourceCodeInfoHelper;

final class ProtoHelpersTest extends TestCase
{
    use ProtoTrait;

    public function testProtoCustomOptions(): void
    {
        $file = $this->loadDescriptor('Utils/custom_options.proto');

        // Check custom options are loaded successfully.
        $this->assertEquals(42, ProtoHelpers::getCustomOption($file, 2000));
        $this->assertEquals('stringy', ProtoHelpers::getCustomOption($file, 2001));
        $this->assertEquals([8, 9, 10], ProtoHelpers::getCustomOptionRepeated($file, 2002)->toArray());
        $this->assertEquals(['s1', 's2'], ProtoHelpers::getCustomOptionRepeated($file, 2003)->toArray());
    }

    public function testProtoComments(): void
    {
        $file = $this->loadDescriptor('Utils/comments.proto');
        SourceCodeInfoHelper::Merge($file);
        
        // Check comments are merged from all proto structures.
        $svc = $file->getService()[0];
        $this->assertEquals(['Svc 1', 'Svc 2'], $svc->leadingComments->toArray());
        $method = $svc->getMethod()[0];
        $this->assertEquals(['Method 1', 'Method 2'], $method->leadingComments->toArray());
        $msg = $file->getMessageType()[0];
        $this->assertEquals(['Msg 1', 'Msg 2'], $msg->leadingComments->toArray());
        $msgField = $msg->getField()[0];
        $this->assertEquals(['Field 1', 'Field 2'], $msgField->leadingComments->toArray());
        $inner = $msg->getNestedType()[0];
        $this->assertEquals(['Inner 1', 'Inner 2'], $inner->leadingComments->toArray());
        $innerField = $inner->getField()[0];
        $this->assertEquals(['Inner field 1', 'Inner field 2'], $innerField->leadingComments->toArray());
    }
}
