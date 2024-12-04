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

use Closure;

/**
 * Represent a resolved type, ready to use in code output.
 * This class is required to allow a resolved type to be differentiated from other plain strings.
 */
class ResolvedType
{
    /**
     * The 'array' built-in type.
     *
     * @return ResolvedType
     */
    public static function array(bool $optional = false): ResolvedType
    {
        return new ResolvedType(Type::array(), fn () => 'array', $optional);
    }

    /**
     * The 'string' built-in type.
     *
     * @return ResolvedType
     */
    public static function string(bool $optional = false): ResolvedType
    {
        return new ResolvedType(Type::string(), fn () => 'string', $optional);
    }

    /**
     * The 'mixed' built-in type.
     *
     * @return ResolvedType
     */
    public static function mixed(): ResolvedType
    {
        return new ResolvedType(Type::mixed(), fn () => 'mixed');
    }

    /**
     * The 'self' built-in identifier.
     *
     * @return ResolvedType
     */
    public static function self(): ResolvedType
    {
        return new ResolvedType(Type::self(), fn () => 'self');
    }

    /**
     * Construct a ResolvedType.
     *
     * @param string $typeName The resolved name of the type.
     */
    public function __construct(
        /** @var Type *Readonly* The type of this resolved-type. */
        public ReadOnly Type $type,
        private Closure $fnToCode,
        private bool $optional = false)
    {
    }

    public function toCode(): string
    {
        return ($this->optional ? '?' : '') . ($this->fnToCode)();
    }
}
