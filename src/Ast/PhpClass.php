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

use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;

/** A class definition. */
final class PhpClass extends AST
{
    use HasPhpDoc;

    public function __construct(Type $type, ?ResolvedType $extends)
    {
        $this->type = $type;
        $this->extends = $extends;
        $this->traits = Set::new();
        $this->members = Vector::new();
    }

    /** @var Type *Readonly* The type of this class. */
    public Type $type;

    /** @var Set *Readonly* Set of ResolvedType; the traits used by this class. */
    public Set $traits;

    /** @var Vector *Readonly* Vector of PhpClassMember; all members of this class. */
    public Vector $members;

    /**
     * Create a class with an additional trait.
     *
     * @param ResolvedType $trait Trait to add. Must be a type which is a trait.
     *
     * @return PhpClass
     */
    public function withTrait(ResolvedType $trait): PhpClass
    {
        if (!$trait->type->isClass()) {
            throw new \Exception('Only classes (traits) may be used as a trait.');
        }
        return $this->clone(fn ($clone) => $clone->traits = $clone->traits->add($trait));
    }

    /**
     * Create a class with an additional member.
     *
     * @param ?PhpClassMember $member The member to add. Ignored if null.
     *
     * @return PhpClass
     */
    public function withMember(?PhpClassMember $member): PhpClass
    {
        return is_null($member) ? $this :
            $this->clone(fn ($clone) => $clone->members = $clone->members->append($member));
    }

    /**
     * Create a class with additional members.
     *
     * @param Vector $members Vector of PhpClassMember; the members to add.
     *
     * @return PhpClass
     */
    public function withMembers(Vector $members): PhpClass
    {
        $members = $members->filter(fn ($x) => !is_null($x));
        return count($members) === 0 ? $this :
            $this->clone(fn ($clone) => $clone->members = $clone->members->concat($members));
    }

    public function toCode(): string
    {
        $extends = is_null($this->extends) ? '' : " extends {$this->extends->toCode()}";
        return
            $this->phpDocToCode() .
            "class {$this->type->name}{$extends}\n" .
            "{\n" .
            $this->traits->toVector()->map(fn ($x) => "use {$x->toCode()};\n")->join() .
            (count($this->traits) >= 1 ? "\n" : '') .
            $this->members->map(fn ($x) => $x->toCode() . "\n")->join() .
            "}\n";
    }
}
