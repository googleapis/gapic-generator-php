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

use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;
use RuntimeException;

/** A class definition. */
final class PhpInterface extends AST
{
    use HasPhpDoc;

    /**
     * @param Type $type *Readonly* The type of this class.
     * @param ?ResolvedType $extends
     */
    public function __construct(
        public Type $type,
        private ?ResolvedType $extends
    ) {
        $this->members = Vector::new();
    }

    /** @var Vector *Readonly* Vector of PhpClassMember; all members of this class. */
    public Vector $members;

    /**
     * Create a class with an additional member.
     *
     * @param ?PhpClassMember $member The member to add. Ignored if null.
     *
     * @return PhpInterface
     */
    public function withMember(?PhpClassMember $member): PhpInterface
    {
        return is_null($member) ? $this :
            $this->clone(fn ($clone) => $clone->members = $clone->members->append($member));
    }

    /**
     * Create a class with additional members.
     *
     * @param Vector $members Vector of PhpClassMember; the members to add.
     *
     * @return PhpInterface
     */
    public function withMembers(Vector $members): PhpInterface
    {
        $members = $members->filter(fn ($x) => !is_null($x));

        return count($members) === 0 ? $this :
            $this->clone(fn ($clone) => $clone->members = $clone->members->concat($members));
    }

    /**
     * Generates PHP code of the class.
     *
     * @throws RuntimeException When $abstract and $final both are set.
     */
    public function toCode(): string
    {
        $extends = is_null($this->extends) ? '' : " extends {$this->extends->toCode()}";

        return
            $this->phpDocToCode() .
            "interface {$this->type->name}{$extends}\n" .
            "{\n" .
            $this->members->map(fn ($x) => $x->toCode() . "\n")->join() .
            "}\n";
    }
}
