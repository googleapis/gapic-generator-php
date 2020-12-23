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

use Google\ApiCore\ResourceTemplate\Parser;
use Google\ApiCore\ResourceTemplate\Segment;
use Google\Generator\Ast\Ast;
use Google\Generator\Ast\Access;
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Ast\PhpProperty;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;

class ResourcePatternDetails implements ResourcePart
{
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        // Replace the valid non-"/" separators with "/" characters, just for pattern parsing.
        // This is valid, as all we need to know is the variables within the string, which is
        // not affected by the separators used.
        // Non-"/" seperators are only allowed to be single characters between variables,
        // hence the specific replace code below.
        $patternToParse = $pattern;
        $patternToParse = str_replace('}_{', '}/{', $patternToParse);
        $patternToParse = str_replace('}-{', '}/{', $patternToParse);
        $patternToParse = str_replace('}.{', '}/{', $patternToParse);
        $patternToParse = str_replace('}~{', '}/{', $patternToParse);
        $segments = Vector::new(Parser::parseSegments($patternToParse));
        $varSegments = $segments
            ->filter(fn($x) => $x->getSegmentType() === Segment::VARIABLE_SEGMENT)
            ->map(fn($x) => $x->getKey());
        $this->nameSnakeCase = $varSegments->join('_');
        $this->nameCamelCase = Helpers::toCamelCase($this->nameSnakeCase);
        $this->templateProperty = AST::property($this->nameCamelCase . 'NameTemplate');
        $this->templateGetterMethod = AST::method(Helpers::toCamelCase("get_{$this->nameCamelCase}") . 'NameTemplate');
        $this->formatMethod = AST::method($this->nameCamelCase . 'Name');
        $this->params = $varSegments->map(fn($x) => [$x, AST::param(null, AST::var(Helpers::toCamelCase($x)))]);
    }

    /** @var string The pattern. */
    public string $pattern;

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

    /** @var Vector Vector of [name, PhpParam] for each pattern variable segment. */
    public Vector $params;

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
        return $this->pattern;
    }

    public function getFormatMethod(): PhpMethod
    {
        return $this->formatMethod;
    }

    public function getParams(): Vector
    {
        return $this->params;
    }
}
