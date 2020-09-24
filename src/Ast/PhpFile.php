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

final class PhpFile extends AST
{
    public function __construct(PhpClass $class)
    {
        $this->class = $class;
        $this->uses = Set::new();
    }

    private Set $uses;

    public function withUses(Set $uses)
    {
        return $this->clone(fn($clone) => $clone->uses = $uses);
    }

    public function toCode(): string
    {
        return
            "<?php\n" .
            "declare(strict_types=1);\n" .
            "\n" .
            "namespace {$this->class->type->getNamespace()};\n" .
            "\n" .
            $this->uses->toVector()->map(fn($x) => "use {$x->getFullname(true)};\n")->join() .
            (count($this->uses) >= 1 ? "\n" : '') .
            static::toPhp($this->class);
    }
}
