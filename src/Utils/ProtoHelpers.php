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

use Google\Generator\Collections\Vector;
use Google\Protobuf\Internal\CodedInputStream;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\GPBWire;
use Google\Protobuf\Internal\HasPublicDescriptorTrait;
use Google\Protobuf\Internal\Message;

class ProtoHelpers
{
    /**
     * Get the PHP namespace of the specified file.
     * This is achieved by reading the PHP namespace option if present, otherwise it uses the proto package name.
     *
     * @param FileDescriptorProto $fileDesc The file for this to get the PHP namespace.
     */
    public static function GetNamespace(FileDescriptorProto $fileDesc): string
    {
        if ($fileDesc->hasOptions())
        {
            $opts = $fileDesc->getOptions();
            if ($opts->hasPhpNamespace())
            {
                return $opts->getPhpNamespace();
            }
        }
        // Fallback to munging the proto package.
        return implode('\\', explode('.', $fileDesc->getPackage()));
    }

    /**
     * Add an underlying proto to a descriptor.
     *
     * @param mixed $desc The descriptor to which to add the proto.
     * @param mixed $proto The underlying proto to add.
     *
     * @return mixed The descriptor passed in.
     */
    public static function AddProto($desc, $proto)
    {
        $desc->underlyingProto = $proto;
        return $desc;
    }

    // Return type is dependant on option type. Either string, int, or Vector of string or int,
    // or null if not repeated and value doesn't exist. Repeated returns empty vector if not exists.
    private static function getCustomOptionRaw(Message $message, int $optionId, bool $repeated)
    {
        static $messageUnknown;
        if (!$messageUnknown)
        {
            $ref = new \ReflectionClass('Google\Protobuf\Internal\Message');
            $messageUnknown = $ref->getProperty('unknown');
            $messageUnknown->setAccessible(true);
        }

        $values = [];
        if ($message->hasOptions())
        {
            $opts = $message->getOptions();
            $unknown = $messageUnknown->getValue($opts);
            if ($unknown)
            {
                $unknownStream = new CodedInputStream($unknown);
                // Read through the stream of all custom options, looking for
                // the requested option-id. If it's repeated, then all options
                // must be parsed, otherwise return the first found.
                while (($tag = $unknownStream->readTag()) !== 0)
                {
                    $value = 0;
                    // TODO: Handle extra option types as required.
                    switch (GPBWire::getTagWireType($tag)) {
                        case GPBWire::WIRETYPE_VARINT:
                            $unknownStream->readVarint32($value);
                            break;
                        case GPBWire::WIRETYPE_LENGTH_DELIMITED:
                            $len = 0;
                            $unknownStream->readVarintSizeAsInt($len);
                            $unknownStream->readRaw($len, $value);
                            break;
                        default:
                            throw new \Exception('Cannot read option tag');
                    }
                    if (GPBWire::getTagFieldNumber($tag) === $optionId) {
                        if ($repeated) {
                            $values[] = $value;
                        } else {
                            return $value;
                        }
                    }
                }
            }
        }
        return $repeated ? Vector::new($values) : null;
    }

    private static function conformMessage($message): Message
    {
        if (isset($message->underlyingProto)) {
            $message = $message->underlyingProto;
        }
        if (!($message instanceof Message)) {
            throw new \Exception('Can only get custom option of Message or HasPublicDescriptorTrait');
        }
        return $message;
    }

    /**
     * Get a non-repeated custom option. The option can be of any type, which determines the return type.
     *
     * @param mixed $message The message containing the custom option. Must be of type Message, or
     *     a descriptor with an `underlyingProto` property.
     * @param int $optionId The option-id of the option to get.
     *
     * @return mixed Will be null if the option does not exist.
     */
    public static function getCustomOption($message, int $optionId)
    {
        return static::getCustomOptionRaw(static::conformMessage($message), $optionId, false);
    }

    /**
     * Get a repeated custom option. The option can be of any type, which determines to content of the returned Vector.
     *
     * @param mixed $message The message containing the custom option. Must be of type Message, or
     *     a descriptor with an `underlyingProto` property.
     * @param int $optionId The option-id of the option to get.
     *
     * @return Vector Will be an empty Vector if the option does not exist.
     */
    public static function getCustomOptionRepeated($message, int $optionId): Vector
    {
        return static::getCustomOptionRaw(static::conformMessage($message), $optionId, true);
    }
}
