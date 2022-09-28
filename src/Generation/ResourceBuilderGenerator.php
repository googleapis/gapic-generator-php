<?php
/*
 * Copyright 2021 Google LLC
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

use Google\Generator\Ast\Access;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Type;
use Google\ApiCore\PathTemplate;
use Google\ApiCore\ValidationException;

class ResourceBuilderGenerator {
    public static function generate(SourceFileContext $ctx, ResourceDetails $resourceDetails, string $pkgNamespace): PhpFile
    {
        return (new ResourceBuilderGenerator($ctx, $resourceDetails, $pkgNamespace))->generateImpl();
    }

    const RESERVED_NAMES = ['class'];

    private SourceFileContext $ctx;
    private ResourceDetails $resourceDetails;
    private Vector $resourceParts;
    private Type $builder;

    private function __construct(SourceFileContext $ctx, ResourceDetails $resourceDetails, string $pkgNamespace)
    {
        $this->ctx = $ctx;
        $this->resourceDetails = $resourceDetails;
        $this->builder = $this->resourceDetails->helperClass($pkgNamespace);
        $this->resourceParts = $this->resourceDetails->patterns
            ->distinct(fn ($p) => $p->getNameCamelCase())
            ->orderBy(fn ($p) => $p->getNameCamelCase());
    }

    private function generateImpl(): PhpFile
    {
        // Generate file content
        $file = AST::file($this->generateClass())
            ->withApacheLicense($this->ctx->licenseYear)
            ->withGeneratedCodeWarning();
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function generateClass(): PhpClass
    {
        return AST::class($this->builder)
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text(
                    $this->builder->name . ' contains methods for building and ' .
                    'parsing the ' . $this->resourceDetails->type . ' resource names.'
                )
            ))
            ->withMembers($this->resourceProperties())
            ->withMembers($this->resourceMethods());
    }

    private function resourceProperties(): Vector
    {
        if (count($this->resourceParts) > 0) {
            // Prevent duplicate properties. Vector's toMap currently does not support cloberring keys.
            // Sorts these properties alphabetically as a nice side effect.
            $templateMap = [];
            foreach ($this->resourceParts as $r) {
                $templateMap[$r->nameCamelCase] =
                  $r->getTemplateProperty()->withAccess(Access::PRIVATE, Access::STATIC);
            }
            $templates = Vector::new(array_values($templateMap));
            $pathTemplateMap = AST::property('pathTemplateMap')
                ->withAccess(Access::PRIVATE, Access::STATIC);
            return Vector::new($templates->append($pathTemplateMap));
        } else {
            return Vector::new([]);
        }
    }

    private function resourceMethods(): Vector
    {
        $resourceParts = $this->resourceParts;
        if (count($resourceParts) > 0) {
            $templateGetters = $resourceParts
                ->map(fn ($x) => $x->getTemplateGetterMethod()
                    ->withAccess(Access::PRIVATE, Access::STATIC)
                    ->withBody(AST::block(
                        AST::if(AST::binaryOp(AST::access(AST::SELF, $x->getTemplateProperty()), '==', AST::NULL))->then(
                            AST::assign(
                                AST::access(AST::SELF, $x->getTemplateProperty()),
                                AST::new($this->ctx->type(Type::fromName(PathTemplate::class)))($x->getPattern())
                            )
                        ),
                        AST::return(AST::access(AST::SELF, $x->getTemplateProperty()))
                    )));
            $pathTemplateMap = AST::property('pathTemplateMap');
            $getPathTemplateMap = AST::method('getPathTemplateMap')
                ->withAccess(Access::PRIVATE, Access::STATIC)
                ->withBody(AST::block(
                    AST::if(AST::binaryOp(AST::access(AST::SELF, $pathTemplateMap), '==', AST::NULL))->then(
                        AST::assign(
                            AST::access(AST::SELF, $pathTemplateMap),
                            AST::array($resourceParts
                            ->toArray(fn ($x) => $x->getNameCamelCase(), fn ($x) => AST::call(AST::SELF, $x->getTemplateGetterMethod())()))
                        )
                    ),
                    AST::return(AST::access(AST::SELF, $pathTemplateMap))
                ));
            $formatMethods = $resourceParts
                ->map(fn ($x) => $x->getFormatMethod()
                    ->withAccess(Access::PUBLIC, Access::STATIC)
                    ->withParams($x->getParams()->map(fn ($x) => $x[1]))
                    ->withBody(AST::block(
                        AST::return(AST::call(AST::SELF, $x->getTemplateGetterMethod())()->render(
                            AST::array($x->getParams()->toArray(fn ($x) => $x[0], fn ($x) => $x[1]))
                        ))
                    ))
                    ->withPhpDoc(PhpDoc::block(
                        PhpDoc::text(
                            'Formats a string containing the fully-qualified path to represent a',
                            $this->resourceDetails->nameUpperCamelCase, 'resource using the',
                            $x->idSegments->skipLast(1)->join(', ') . (count($x->idSegments) > 1 ? ', and' : ''),
                            $x->idSegments->last() . '.'
                        ),
                        $x->getParams()->map(fn ($x) => PhpDoc::param($x[1], PhpDoc::text(), $this->ctx->type(Type::string()))),
                        PhpDoc::return($this->ctx->type(Type::string()),
                            PhpDoc::text('The formatted', $this->resourceDetails->nameUpperCamelCase, 'resource name.'))
                    )));
            $formattedName = AST::param(null, AST::var('formattedName'));
            $template = AST::param(null, AST::var('template'), AST::NULL);
            $templateMap = AST::var('templateMap');
            $templateName = AST::var('templateName');
            $pathTemplate = AST::var('pathTemplate');
            $ex = AST::var('ex');
            $parseMethod = AST::method('parseName')
                ->withAccess(Access::PUBLIC, Access::STATIC)
                ->withParams($formattedName, $template)
                ->withBody(AST::block(
                    AST::assign($templateMap, AST::call(AST::SELF, $getPathTemplateMap)()),
                    AST::if($template->var)->then(
                        AST::if(AST::not(AST::call(AST::ISSET)($templateMap[$template->var])))->then(
                            AST::throw(AST::new($this->ctx->type(Type::fromName(ValidationException::class)))(
                                AST::interpolatedString('Template name $template does not exist')
                            ))
                        ),
                        AST::return($templateMap[$template->var]->match($formattedName->var))
                    ),
                    AST::foreach($templateMap, $pathTemplate, $templateName)(
                        AST::try(
                            AST::return($pathTemplate->match($formattedName))
                        )->catch($this->ctx->type(Type::fromName(ValidationException::class)), $ex)(
                            '// Swallow the exception to continue trying other path templates'
                        )
                    ),
                    AST::throw(AST::new($this->ctx->type(Type::fromName(ValidationException::class)))(
                        AST::interpolatedString('Input did not match any known format. Input: $formattedName')
                    ))
                ))
                ->withPhpDoc(PhpDoc::block(
                    PhpDoc::preFormattedText(Vector::new([
                        'Parses a formatted name string and returns an associative array of the components in the name.',
                        'The following name formats are supported:',
                        'Template: Pattern',
                    ])->concat($resourceParts->map(fn ($x) => "- {$x->getNameCamelCase()}: {$x->getPattern()}"))),
                    PhpDoc::text(
                        'The optional $template argument can be supplied to specify a particular pattern, and must',
                        'match one of the templates listed above. If no $template argument is provided, or if the',
                        '$template argument does not match one of the templates listed, then parseName will check',
                        'each of the supported templates, and return the first match.'
                    ),
                    PhpDoc::param($formattedName, PhpDoc::text('The formatted name string'), $this->ctx->type(Type::string())),
                    PhpDoc::param($template, PhpDoc::text('Optional name of template to match'), $this->ctx->type(Type::string())),
                    PhpDoc::return($this->ctx->type(Type::array()), PhpDoc::text('An associative array from name component IDs to component values.')),
                    PhpDoc::throws($this->ctx->type(Type::fromName(ValidationException::class)), PhpDoc::text('If $formattedName could not be matched.'))
                ));
            return $templateGetters->append($getPathTemplateMap)->concat($formatMethods)->append($parseMethod);
        } else {
            return Vector::new([]);
        }
    }
}