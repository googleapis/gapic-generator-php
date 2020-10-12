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

namespace Google\Generator\Ast;

/** A property within a class. */
final class PhpProperty extends PhpClassMember
{
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->value = null;
    }

    /**
     * Create a property with the specified value.
     *
     * @param Expression $value The value of the constant.
     *
     * @return PhpConstant
     */
    public function withValue(Expression $value): PhpProperty
    {
        return $this->clone(fn($clone) => $clone->value = $value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toCode(): string
    {
        return
            $this->phpDocToCode() .
            $this->accessToCode() .
            '$' . $this->name .
            (is_null($this->value) ? ';' : ' = ' . static::toPhp($this->value) . ';');
    }
}
