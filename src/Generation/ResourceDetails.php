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
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\Type;

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
        $this->nameUpperCamelCase = Helpers::toUpperCamelCase($typeParts[1]);
        $this->nameSnakeCase = Helpers::toSnakeCase($typeParts[1]);
        $this->templateProperty = Ast::property($this->nameCamelCase . 'NameTemplate');
        $this->templateGetterMethod = AST::method(Helpers::toCamelCase('get_' . $typeParts[1]) . 'NameTemplate');
        $this->patterns = Vector::new($desc->getPattern())
            ->filter(fn ($x) => $x !== '*')
            ->map(fn ($x) => new ResourcePatternDetails($x));
        // Just use the first pattern for the format method in examples.
        $this->formatMethod = $this->patterns->count() > 0 ?
            $this->patterns->firstOrNull()->getFormatMethod() :
            new PhpMethod($this->nameCamelCase);
    }

    /** @var string The type name (unique resource name) of this resource. */
    public string $type;

    /** @var string The underlying name of this resource. */
    public string $nameCamelCase;

    /** @var string The underlying name of this resource in UpperCamelCase. */
    public string $nameUpperCamelCase;

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

    /** 
     * Given a package-level namespace, construct the helper class type for this resource.
     * 
     * @param string $namespace The package-level namespace for the resource.
     * 
     * @return Type type of the generated helper class for the resource.
     */
    public function helperClass(string $namespace): Type
    {
        return Type::fromName($namespace . '\\ResourceNames\\' . $this->nameUpperCamelCase);
    }

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
