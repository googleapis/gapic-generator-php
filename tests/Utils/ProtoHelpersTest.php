<?php declare(strict_types=1);

namespace Google\Generator\Tests\Collections;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ProtoHelpers;
use Google\Protobuf\Internal\FileDescriptorSet;

final class ProtoHelpersTest extends TestCase
{
    public function testCustomOptions(): void
    {
        // TODO: Extract this code to invoke protoc and load descriptors, when more tests require it.

        // Set up required file locations and create tmp output file for protoc invocation.
        $cwd = getcwd();
        $protoc = "{$cwd}/tools/protoc";
        $protobuf = "{$cwd}/protobuf/src/";
        $descRes = tmpfile();
        $descFilename = stream_get_meta_data($descRes)['uri'];
        $input = "{$cwd}/tests/Utils/CustomOptions.proto";
        // Invoke protoc to build the descriptor of the test proto.
        $protocCmdLine = "{$protoc} --include_imports --include_source_info -o {$descFilename} -I {$protobuf} -I {$cwd} {$input} 2>&1";
        $output = [];
        $result = -1;
        exec($protocCmdLine, $output, $result);
        // Fail with helpful error message if protoc failed.
        $this->assertEquals(0, $result, implode("\n", $output));

        // Load the proto descriptors.
        $descBytes = stream_get_contents($descRes);
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        // Select the correct file from the descriptor-set.
        $file = Vector::new($descSet->getFile())->filter(fn($x) => $x->getName() === 'tests/Utils/CustomOptions.proto')[0];

        // Check custom options are loaded successfully.
        $this->assertEquals(42, ProtoHelpers::getCustomOption($file, 2000));
        $this->assertEquals('stringy', ProtoHelpers::getCustomOption($file, 2001));
        $this->assertEquals([8, 9, 10], ProtoHelpers::getCustomOptionRepeated($file, 2002)->toArray());
        $this->assertEquals(['s1', 's2'], ProtoHelpers::getCustomOptionRepeated($file, 2003)->toArray());
    }
}
