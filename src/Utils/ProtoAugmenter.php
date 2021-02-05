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

namespace Google\Generator\Utils;

use Google\Protobuf\Internal\EnumDescriptor;
use Google\Protobuf\Internal\FileDescriptor;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\Descriptor;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FieldDescriptor;
use Google\Generator\Collections\Vector;

class ProtoAugmenter
{
    // Constants taken from:
    // https://github.com/protocolbuffers/protobuf/blob/master/src/google/protobuf/descriptor.proto
    private const MESSAGE = 4;
    private const MESSAGE_FIELD = 2;
    private const MESSAGE_MESSAGE = 3;
    private const MESSAGE_ENUM = 4;
    private const ENUM = 5;
    private const SERVICE = 6;
    private const SERVICE_METHOD = 2;

    /**
     * Recursively augment proto descriptors.
     * This is required because PHP doesn't currently reflect over all proto data.
     *
     * @param Vector $fileDescs Vector of FileDescriptorProto; Top-level file descriptors.
     */
    public static function augment(Vector $fileDescs): void
    {
        foreach ($fileDescs as $fileDesc) {
            static::augmentFile($fileDesc);
        }
    }

    private static function augmentFile(FileDescriptorProto $fileProto)
    {
        $fnLeadingComments = fn($x) => $x->getLeadingComments();
        $fnTrailingComments = fn($x) => $x->getTrailingComments();

        $sci = $fileProto->getSourceCodeInfo();
        $locsByPath = Vector::new($sci->getLocation())
            ->groupBy(fn($x) => Vector::new($x->getPath()));

        // Handle top-level services:
        foreach ($fileProto->getService() as $serviceIndex => $service) {
            $servicePath = Vector::new([static::SERVICE, $serviceIndex]);
            $serviceLocations = $locsByPath->get($servicePath, null);
            $service->leadingComments = static::getComments($serviceLocations, $fnLeadingComments);
            foreach ($service->getMethod() as $methodIndex => $method) {
                $methodPath = $servicePath->concat(Vector::new([static::SERVICE_METHOD, $methodIndex]));
                $methodLocations = $locsByPath->get($methodPath, null);
                $method->leadingComments = static::getComments($methodLocations, $fnLeadingComments);
            }
        }

        // A FileDescriptor only includes msgs and enums, hence services are handled separately above.
        $fileDesc = FileDescriptor::buildFromProto($fileProto);

        $fnMergeEnums = function(Vector $path, int $pathId, $proto, $desc) use($locsByPath, $fnLeadingComments) {
            $enums = Vector::zip(Vector::new($proto->getEnumType()), Vector::new($desc->getEnumType()));
            foreach ($enums as $enumIndex => [$enumProto, $enumDesc]) {
                // Link proto and desc in both directions.
                $enumProto->desc = $enumDesc;
                $enumDesc->underlyingProto = $enumProto;
                // Link proto comments.
                $enumPath = $path->concat(Vector::new([$pathId, $enumIndex]));
                $enumLocations = $locsByPath->get($enumPath, null);
                $enumProto->leadingComments = static::getComments($enumLocations, $fnLeadingComments);
            }
        };

        $fnMergeMsgs = null;
        $fnMergeMsgs = function(Vector $msgPath, DescriptorProto $msgProto, Descriptor $msgDesc)
                use(&$fnMergeMsgs, $locsByPath, $fnLeadingComments, $fnTrailingComments, $fnMergeEnums) {
            // Link proto and desc in both directions.
            $msgProto->desc = $msgDesc;
            $msgDesc->underlyingProto = $msgProto;
            // Link proto comments.
            $msgLocations = $locsByPath->get($msgPath, null);
            $msgProto->leadingComments = static::getComments($msgLocations, $fnLeadingComments);
            // Recurse into nested messages:
            $nestedMsgs = Vector::zip(Vector::new($msgProto->getNestedType()), Vector::new($msgDesc->getNestedType()));
            foreach ($nestedMsgs as $nestedMsgIndex => [$nestedMsgProto, $nestedMsgDesc]) {
                $nestedMsgPath = $msgPath->concat(Vector::new([static::MESSAGE_MESSAGE, $nestedMsgIndex]));
                $fnMergeMsgs($nestedMsgPath, $nestedMsgProto, $nestedMsgDesc);
            }
            // Handle fields:
            $fields = Vector::zip(Vector::new($msgProto->getField()), Vector::new($msgDesc->getField()));
            foreach ($fields as $fieldIndex => [$fieldProto, $fieldDesc]) {
                // Link proto and desc in both directions.
                $fieldProto->desc = $fieldDesc;
                $fieldDesc->underlyingProto = $fieldProto;
                // Link proto comments.
                $fieldPath = $msgPath->concat(Vector::new([static::MESSAGE_FIELD, $fieldIndex]));
                $fieldLocations = $locsByPath->get($fieldPath, null);
                $fieldProto->leadingComments = static::getComments($fieldLocations, $fnLeadingComments);
                $fieldProto->trailingComments = static::getComments($fieldLocations, $fnTrailingComments);
            }
            // Handle enums:
            $fnMergeEnums($msgPath, static::MESSAGE_ENUM, $msgProto, $msgDesc);
        };

        // Handle top-level messages:
        $msgs = Vector::zip(Vector::new($fileProto->getMessageType()), Vector::new($fileDesc->getMessageType()));
        foreach ($msgs as $msgIndex => [$msgProto, $msgDesc]) {
            $fnMergeMsgs(Vector::new([static::MESSAGE, $msgIndex]), $msgProto, $msgDesc);
        }
        // Handle top-level enums:
        $fnMergeEnums(Vector::new([]), static::ENUM, $fileProto, $fileDesc);
    }

    private static function getComments(?Vector $locations, Callable $fn): Vector
    {
        return is_null($locations) ? Vector::new([]) : $locations
            ->flatMap(fn($x) => Vector::new(explode("\n", $fn($x))))
            ->map(fn($x) => trim($x))
            ->skipLast(1); // Last line is always empty due to trailing \n
    }
}
