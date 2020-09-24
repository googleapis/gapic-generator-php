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
use Google\Generator\Collections\Equality;
use Google\Generator\Collections\Vector;

/** A fully-specified PHP type. */
class Type implements Equality
{
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
            ->skipWhile(fn($x) => $x === ''); // First element will be empty if leading '\' given.
        return new Type($parts->skipLast(1), $parts->last());
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
            ($omitLeadingBackslash ? '' : '\\') . "{$this->getNamespace()}\\{$this->name}" :
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
