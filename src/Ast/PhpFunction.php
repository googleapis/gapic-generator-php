<?php
/*
 * Copyright 2022 Google LLC
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

/** A function that can be placed in any block of code. */
final class PhpFunction extends AST implements ShouldNotApplySemicolonInterface
{
    use HasPhpDoc;

    /** @var string The name of this function. */
    private string $name;

    /** @var bool Whether a newline should be appended to the end of the function declaration. */
    private bool $appendNewline = true;

    /** @var Vector The function's parameters. */
    private Vector $params;

    /** @var string The return type of the function. */
    private ?ResolvedType $returnType = null;

    /** @var string The body of the function. */
    private ?AST $body = null;

    /**
     * @param string $name The name of this function.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->params = Vector::new();
    }

    /**
     * Create a function with the specified parameters.
     *
     * @param array $params Array of AST::param(); The parameters of the function.
     *
     * @return PhpFunction
     */
    public function withParams(...$params): PhpFunction
    {
        return $this->clone(fn ($clone) =>
            $clone->params = Vector::new($params)->flatten()->filter(fn ($x) => !is_null($x)));
    }

    /**
     * Create a function with the specified body.
     *
     * @param AST $body The body of the function.
     *
     * @return PhpFunction
     */
    public function withBody(AST $body): PhpFunction
    {
        return $this->clone(fn ($clone) => $clone->body = $body);
    }

    /**
     * Create a function with the specified return type.
     *
     * @param ResolvedType $returnType The return type of the function.
     *
     * @return PhpFunction
     */
    public function withReturnType(ResolvedType $returnType): PhpFunction
    {
        return $this->clone(fn ($clone) => $clone->returnType = $returnType);
    }

    /**
     * Omits a newline after the function declaration.
     *
     * @return PhpFunction
     */
    public function withoutNewlineAfterDeclaration(): PhpFunction
    {
        return $this->clone(fn ($clone) => $clone->appendNewline = false);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toCode(): string
    {
        $fnSignatureDeclaration =
            "function {$this->name}({$this->params->map(fn ($x) => static::toPhp($x))->join(', ')})" .
            ($this->returnType ? ': ' . static::toPhp($this->returnType) : null);

        $code = $this->phpDocToCode() .
                $fnSignatureDeclaration .
                '{' . PHP_EOL .
                static::toPhp($this->body) .
                PHP_EOL . '}';
        if ($this->appendNewline) {
            $code .= PHP_EOL;
        }

        return $code;
    }
}
