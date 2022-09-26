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

use Google\Generator\Utils\ResolvedType;

/** A param of a method or function. */
final class PhpParam extends AST
{
    public function __construct(?ResolvedType $type, Variable $var, $default)
    {
        $this->type = $type;
        $this->var = $var;
        $this->default = $default;
    }

    /** @var ?ResolvedType *Readonly* Optional; the type of the parameter. */
    public ?ResolvedType $type;

    /** @var Variable *Readonly* The variable used as the parameter. */
    public Variable $var;

    /** @var mixed *Readonly* Optional; the default value of the parameter. */
    public $default;

    public function toCode(): string
    {
        $type = null;
        if ($this->type) {
            $type = substr($this->type->type->name, -2) === '[]'
                ? 'array '
                : static::toPhp($this->type) . ' ';
        }
        $default = $this->default
            ? ' = ' . static::toPhp($this->default)
            : '';
        return $type . static::toPhp($this->var) . $default;
    }
}
