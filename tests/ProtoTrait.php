<?php
declare(strict_types=1);

namespace Google\Generator\Tests;

use Google\Generator\Collections\Vector;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorSet;

trait ProtoTrait
{
    /**
     * Load a descriptor set bytes from the specified proto path.
     * The proto path must be relative to the `tests` directory.
     * 
     * @param string $protoPath The proto path relative to the `tests` directory.
     * 
     * @return string
     */
    function loadDescriptorBytes(string $protoPath): string
    {
        // Set up required file locations and create tmp output file for protoc invocation.
        // Assumes test are executed from within the repo root directory.
        $cwd = getcwd();
        $protoc = "{$cwd}/tools/protoc";
        $descRes = tmpfile();
        $descFilename = stream_get_meta_data($descRes)['uri'];
        $input = "{$cwd}/tests/{$protoPath}";
        // Invoke protoc to build the descriptor of the test proto.
        $protobuf = "{$cwd}/protobuf/src/";
        $googleapis = "{$cwd}/googleapis/";
        $protocCmdLine = "{$protoc} --include_imports --include_source_info -o {$descFilename} " .
            "-I {$googleapis} -I {$protobuf} -I {$cwd} {$input} 2>&1";
        $output = [];
        $result = -1;
        exec($protocCmdLine, $output, $result);
        // Fail with helpful error message if protoc failed.
        $this->assertEquals(0, $result, implode("\n", $output));

        // Load the proto descriptors.
        return stream_get_contents($descRes);
    }

    /**
     * Load a file descriptor from the specified proto path.
     * The proto path must be relative to the `tests` directory.
     * 
     * @param string $protoPath The proto path relative to the `tests` directory.
     * 
     * @return FileDescriptorProto
     */
    function loadDescriptor(string $protoPath): FileDescriptorProto
    {
        // Load descriptor bytes into a DescriptorSet.
        $descBytes = $this->loadDescriptorBytes($protoPath);
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        // Select the correct file from the descriptor-set.
        return Vector::new($descSet->getFile())->filter(fn($x) => $x->getName() === "tests/{$protoPath}")[0];
    }
}
