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
    private const SERVICE = 6;
    private const SERVICE_METHOD = 2;

    /**
     * Recursively augment proto descriptors.
     * This is required because PHP doesn't currently reflect over all proto data.
     *
     * @param Vector $fileDescs Vector of FileDescriptorProto; Top-level file descriptors.
     */
    public static function Augment(Vector $fileDescs): void
    {
        foreach ($fileDescs as $fileDesc) {
            static::AugmentFile($fileDesc);
        }
    }

    private static function AugmentFile(FileDescriptorProto $fileDesc)
    {
        $fnLeadingComments = fn($x) => $x->getLeadingComments();

        $sci = $fileDesc->GetSourceCodeInfo();
        $locsByPath = Vector::New($sci->getLocation())
            ->groupBy(fn($x) => Vector::New($x->getPath()));

        // Services
        foreach ($fileDesc->getService() as $serviceIndex => $service) {
            $servicePath = Vector::New([static::SERVICE, $serviceIndex]);
            $serviceLocations = $locsByPath[$servicePath];
            $service->leadingComments = static::getComments($serviceLocations, $fnLeadingComments);
            foreach ($service->getMethod() as $methodIndex => $method) {
                $methodPath = $servicePath->concat(Vector::New([static::SERVICE_METHOD, $methodIndex]));
                $methodLocations = $locsByPath[$methodPath];
                $method->leadingComments = static::getComments($methodLocations, $fnLeadingComments);
            }
        }

        // Messages
        $mergeMessage = null;
        $mergeMessage = function(Vector $messagePath, DescriptorProto $message, Vector $outerMsgs)
                use(&$mergeMessage, $locsByPath, $fnLeadingComments, $fileDesc) {
            // Create higher-level message descriptor, and link in both directions.
            $message->desc = Descriptor::buildFromProto($message, $fileDesc, $outerMsgs->join('.'));
            $message->desc->underlyingProto = $message;
            // Link proto comments.
            $messageLocations = $locsByPath[$messagePath];
            $message->leadingComments = static::getComments($messageLocations, $fnLeadingComments);
            // Recurse into fields and nested msgs.
            foreach ($message->getField() as $fieldIndex => $field) {
                // Create higher-level field descriptor, and link in both directions.
                $field->desc = FieldDescriptor::buildFromProto($field);
                $field->desc->underlyingProto = $field;
                // Link proto coments.
                $fieldPath = $messagePath->concat(Vector::New([static::MESSAGE_FIELD, $fieldIndex]));
                $fieldLocations = $locsByPath[$fieldPath];
                $field->leadingComments = static::getComments($fieldLocations, $fnLeadingComments);
            }
            foreach ($message->getNestedType() as $nestedMessageIndex => $nestedMessage) {
                $nestedMessagePath = $messagePath->concat(Vector::New([static::MESSAGE_MESSAGE, $nestedMessageIndex]));
                $mergeMessage($nestedMessagePath, $nestedMessage, $outerMsgs->append($message->getName()));
            }
            // TODO: enums
        };
        foreach ($fileDesc->getMessageType() as $messageIndex => $message) {
            $mergeMessage(Vector::New([static::MESSAGE, $messageIndex]), $message, Vector::new());
        }

        // TODO: enums
    }

    private static function getComments(Vector $locations, Callable $fn): Vector
    {
        return $locations
            ->flatMap(fn($x) => Vector::New(explode("\n", $fn($x))))
            ->map(fn($x) => trim($x))
            ->skipLast(1); // Last line is always empty due to trailing \n
    }
}
