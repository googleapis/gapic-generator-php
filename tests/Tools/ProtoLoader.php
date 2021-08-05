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

namespace Google\Generator\Tests\Tools;

use Google\Generator\Collections\Vector;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorSet;

class ProtoLoader
{
    /**
     * Load a descriptor set bytes from the specified proto path.
     * The proto path must be relative to the `tests/Unit` directory.
     *
     * @param string $protoPath The proto path relative to the `tests/Unit` directory.
     *
     * @return string
     */
    public static function loadDescriptorBytes(string $protoPath): string
    {
        // Set up required file locations and create tmp output file for protoc invocation.
        // Assumes test are executed from within the repo root directory.
        $cwd = getcwd();
        $protoc = "protoc";
        if (getenv("USE_TOOLS_PROTOC")) {
            $protoc = "{$cwd}/tools/protoc";
        }
        $descRes = tmpfile();
        $descFilename = stream_get_meta_data($descRes)['uri'];
        $input = "{$cwd}/tests/Unit/{$protoPath}";
        // Invoke protoc to build the descriptor of the test proto.
        $protobuf = "{$cwd}/protobuf/src/";
        $googleapis = "{$cwd}/googleapis/";
        $protocCmdLine = "{$protoc} --include_imports --include_source_info -o {$descFilename} " .
            "-I {$googleapis} -I {$protobuf} -I {$cwd} {$input} 2>&1";
        $output = [];
        $result = -1;
        exec($protocCmdLine, $output, $result);
        // Fail with helpful error message if protoc failed.
        if ($result !== 0) {
            print("Protoc failed: " .  implode("\n", $output) . "\n");
            return "";
        }

        // Load the proto descriptors.
        return stream_get_contents($descRes);
    }

    /**
     * Load a file descriptor from the specified proto path.
     * The proto path must be relative to the `tests/Unit` directory.
     *
     * @param string $protoPath The proto path relative to the `tests/Unit` directory.
     *
     * @return FileDescriptorProto
     */
    public static function loadDescriptor(string $protoPath): FileDescriptorProto
    {
        // Load descriptor bytes into a DescriptorSet.
        $descBytes = static::loadDescriptorBytes($protoPath);
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        // Select the correct file from the descriptor-set.
        return Vector::new($descSet->getFile())->filter(fn ($x) => $x->getName() === "tests/Unit/{$protoPath}")[0];
    }
}
