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

/** A method within a class. */
final class PhpMethod extends PhpClassMember
{
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->params = Vector::new();
    }

    /** @var string *Readonly* The name of this method. */
    public string $name;

    /**
     * Create a method with the specified parameters.
     *
     * @param array $params Array of AST::vars(); The parameters of the method.
     *
     * @return PhpMethod
     */
    public function withParams(...$params): PhpMethod
    {
        return $this->clone(fn($clone) =>
            $clone->params = Vector::new($params)->flatten()->filter(fn($x) => !is_null($x)));
    }

    /**
     * Create a method with the specified body.
     *
     * @param AST $body The body of the method.
     *
     * @return PhpMethod
     */
    public function withBody(AST $body): PhpMethod
    {
        return $this->clone(fn($clone) => $clone->body = $body);
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
            "function {$this->name}({$this->params->map(fn($x) => static::toPhp($x))->join(', ')})" .
            "{\n" .
            static::toPhp($this->body) .
            "}\n";
    }
}
