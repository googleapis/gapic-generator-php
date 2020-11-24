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

namespace Google\Generator\Generation;

use Google\Api\ResourceDescriptor;
use Google\Generator\Ast\Ast;
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Ast\PhpProperty;
use Google\Generator\Collections\Equality;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;

class ResourceDetails implements ResourcePart
{
    public function __construct(ResourceDescriptor $desc)
    {
        $this->type = $desc->getType();
        $typeParts = explode('/', $this->type);
        if (count($typeParts) !== 2) {
            throw new \Exception("Resource-name type is illformed: '{$this->type}'");
        }
        $this->nameCamelCase = Helpers::toCamelCase($typeParts[1]);
        $this->nameSnakeCase = Helpers::toSnakeCase($typeParts[1]);
        $this->templateProperty = Ast::property($this->nameCamelCase . 'NameTemplate');
        $this->templateGetterMethod = AST::method(Helpers::toCamelCase('get_' . $typeParts[1]) . 'NameTemplate');
        $this->formatMethod = AST::method($this->nameCamelCase . 'Name');
        $this->patterns = Vector::new($desc->getPattern())->map(fn($x) => new ResourcePatternDetails($x));
    }

    /** @var string The type name (unique resource name) of this resource. */
    public string $type;

    /** @var string The underlying name of this resource. */
    public string $nameCamelCase;

    /** @var string The underlying name of this resource. */
    public string $nameSnakeCase;

    /** @var string The PHP property of the resource template. */
    public PhpProperty $templateProperty;

    /** @var string The PHP getter method to get the resource template. */
    public PhpMethod $templateGetterMethod;

    /** @var PhpMethod The PHP method of the public format method for this template. */
    public PhpMethod $formatMethod;

    /** @var Vector Vector of ResourcePatternDetails; the resource-name patterns. */
    public Vector $patterns;

    // ResourcePart implementation.

    public function getNameCamelCase(): string
    {
        return $this->nameCamelCase;
    }

    public function getNameSnakeCase(): string
    {
        return $this->nameSnakeCase;
    }

    public function getTemplateProperty(): PhpProperty
    {
        return $this->templateProperty;
    }

    public function getTemplateGetterMethod(): PhpMethod
    {
        return $this->templateGetterMethod;
    }

    public function getPattern(): string
    {
        return $this->patterns[0]->pattern;
    }

    public function getFormatMethod(): PhpMethod
    {
        return $this->formatMethod;
    }

    public function getParams(): Vector
    {
        return $this->patterns[0]->params;
    }
}
