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

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\Descriptor;
use Google\Protobuf\Internal\EnumDescriptor;
use Google\Protobuf\Internal\FieldDescriptor;
use Google\Generator\Collections\Equality;
use Google\Generator\Collections\Vector;

/** A fully-specified PHP type. */
class Type implements Equality
{
    /** The built-in 'array' type. */
    public static function array(): Type
    {
        return new Type(null, 'array');
    }

    /** The built-in 'string' type. */
    public static function string(): Type
    {
        return new Type(null, 'string');
    }

    /** The built-in 'float' type. */
    public static function float(): Type
    {
        return new Type(null, 'float');
    }

    /** The built-in 'int' type. */
    public static function int(): Type
    {
        return new Type(null, 'int');
    }

    /** The built-in 'bool' type. */
    public static function bool(): Type
    {
        return new Type(null, 'bool');
    }

    /** The build int 'false' value */
    public static function false(): Type
    {
        return new Type(null, 'false');
    }

    /** The built-in 'callable' type. */
    public static function callable(): Type
    {
        return new Type(null, 'callable');
    }

    /** The built-in 'mixed' type. */
    public static function mixed(): Type
    {
        return new Type(null, 'mixed');
    }

    /** The built-in 'self' identifier. */
    public static function self(): Type
    {
        return new Type(null, 'self');
    }

    /** The built-in 'void' type. */
    public static function void(): Type
    {
        return new Type(null, 'void');
    }

    /** Null type, for use in union types and generics. */
    public static function null(): Type
    {
        return new Type(null, 'null');
    }

    /** The built-in 'stdClass' type. */
    public static function stdClass(): Type
    {
        return new Type(Vector::new([]), 'stdClass');
    }

    /** The build-in 'union' type for multiple types */
    public static function union(array $types): Type
    {
        return new Type(
            null,
            implode(
                '|',
                array_map(fn (Type $type) => $type->name, $types)
            )
        );
    }

    /** An array of the specified type, for PhpDoc use only. */
    public static function arrayOf(Type $elementType): Type
    {
        return new Type($elementType->namespaceParts, $elementType->name . '[]');
    }

    /**
     * Build a type from a class full-name.
     *
     * @param string $fullname The full name of the class.
     *
     * @return Type
     */
    public static function fromName(string $fullname): Type
    {
        $parts = Vector::new(explode('\\', $fullname))
            ->skipWhile(fn ($x) => $x === ''); // First element will be empty if leading '\' given.
        return new Type($parts->skipLast(1), $parts->last());
    }

    /**
     * Build a type from a proto message descriptor.
     *
     * @param Descriptor $desc The proto message descriptor.
     *
     * @return Type
     */
    public static function fromMessage(Descriptor $desc): Type
    {
        return static::fromName($desc->getClass());
    }

    /**
     * Build a type from a proto enum descriptor.
     *
     * @param EnumDescriptor $desc The proto enum descriptor.
     *
     * @return Type
     */
    public static function fromEnum(EnumDescriptor $desc): Type
    {
        return static::fromName($desc->getClass());
    }

    /**
     * Build a type from a proto message field.
     *
     * @param ProtoCatalog $catalog The proto catalog.
     * @param FieldDescriptor $desc The proto field descriptor.
     * @param ?bool $forceRepeated false to force to not-repeated; true to force to repeated.
     *
     * @return Type
     */
    public static function fromField(ProtoCatalog $catalog, FieldDescriptor $desc, ?bool $forceRepeated = null): Type
    {
        if ($forceRepeated === true || ($desc->isRepeated() && $forceRepeated !== false)) {
            return static::array();
        }
        switch ($desc->getType()) {
            case GPBType::DOUBLE: // 1
            case GPBType::FLOAT: // 2
                return static::float();
            case GPBType::INT64: // 3
            case GPBType::UINT64: // 4
            case GPBType::INT32: // 5
            case GPBType::FIXED64: // 6
            case GPBType::FIXED32: // 7
            case GPBType::UINT32: // 13
            case GPBType::SFIXED32: // 15
            case GPBType::SFIXED64: // 16
            case GPBType::SINT32: // 17
            case GPBType::SINT64: // 18
            return static::int();
            case GPBType::BOOL: // 8
                return static::bool();
            case GPBType::STRING: // 9
                return static::string();
            case GPBType::MESSAGE: // 11
                return static::fromMessage($catalog->msgsByFullname[$desc->getMessageType()]->desc);
            case GPBType::BYTES: // 12
                return static::string();
            case GPBType::ENUM: // 14
                return static::fromEnum($catalog->enumsByFullname[$desc->getEnumType()]->desc);
            default:
                throw new \Exception("Cannot get field type of type: {$desc->getType()}");
        }
    }

    /** Combines multiple types into a single union type */
    public static function generic(string $typeString): Type
    {
        return new Type(
            null,
            $typeString
        );
    }

    private function __construct(?Vector $namespaceParts, string $name)
    {
        $this->namespaceParts = $namespaceParts;
        $this->name = $name;
    }

    /** @var ?Vector *Readonly* Vector of strings; the namespace parts if a class, otherwise null. */
    public ?Vector $namespaceParts;

    /** @var string *Readonly* The name of type class, or inbuilt type. */
    public string $name;

    /**
     * Does this Type represent a class?
     *
     * @return bool
     */
    public function isClass(): bool
    {
        return !is_null($this->namespaceParts);
    }

    /**
     * Get the namespace, if this Type represents a class; otherwise throws an exception.
     *
     * @return string Namespace if a class, otherwise throws an exception.
     */
    public function getNamespace(): string
    {
        if (!$this->isClass()) {
            throw new \Exception('Non-class types do not have a namespace');
        }
        return $this->namespaceParts->join('\\');
    }

    /**
     * Get the full name of this Type.
     *
     * @return string
     */
    public function getFullname($omitLeadingBackslash = false): string
    {
        return $this->isClass() ?
            ($omitLeadingBackslash ? '' : '\\') . $this->namespaceParts->map(fn ($x) => $x . '\\')->join() . $this->name :
            $this->name;
    }

    // Equality methods

    public function getHash(): int
    {
        return crc32($this->getFullname());
    }

    public function isEqualTo($other): bool
    {
        return $other instanceof Type && $this->getFullname() === $other->getFullname();
    }
}
