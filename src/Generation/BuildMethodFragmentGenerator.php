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

namespace Google\Generator\Generation;

use Google\Generator\Ast\Access;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpClassMember;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpParam;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Formatter;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;

class BuildMethodFragmentGenerator
{
    public static function generate(SourceFileContext $ctx, ServiceDetails $serviceDetails): Map
    {
        return (new BuildMethodFragmentGenerator($ctx, $serviceDetails))->generateImpl();
    }

    public static function format(string $code): string
    {
        // Add a class wrapper so that the formatter works (requires valid PHP)
        $codeWrap = "<?php\n\nnew class {\n%s\n};";

        // Apply the standard Formatter
        $code = Formatter::format(sprintf($codeWrap, $code));

        // Remove the wrapping class
        preg_match(sprintf('/' . $codeWrap . '/s', '(.*)'), $code, $matches);

        return PHP_EOL . $matches[1];
    }

    private function generateImpl(): Map
    {
        $buildMethodSnippets = Map::new([]);
        foreach ($this->serviceDetails->methods as $methodDetails) {
            if ($methodDetails->methodSignature && !$methodDetails->isMixin()) {
                // Do not generate build method fragments for request messages outside this package
                if (0 !== strpos($methodDetails->inputMsg->desc->getFullName(), $this->serviceDetails->package)) {
                    continue;
                }
                $buildMethods = Vector::new();
                foreach ($methodDetails->methodSignature as $i => $methodSignature) {
                    if (empty($methodSignature)) {
                        continue;
                    }
                    $buildMethods = $buildMethods->append(
                        $this->buildMethodSnippet($methodDetails, $methodSignature, 0 === $i)
                    );
                }
                if ($buildMethods->count() > 0) {
                    $buildMethodSnippets = $buildMethodSnippets->set(
                        str_replace('\\', '/', $methodDetails->requestType->getFullname(true)),
                        $buildMethods
                    );
                }
            }
        }

        $this->ctx->finalize(null);

        return $buildMethodSnippets;
    }

    private SourceFileContext $ctx;
    private ServiceDetails $serviceDetails;

    private function __construct(SourceFileContext $ctx, ServiceDetails $serviceDetails)
    {
        $this->ctx = $ctx;
        $this->serviceDetails = $serviceDetails;
    }

    public function buildMethodSnippet(MethodDetails $methodDetails, string $methodSignature, bool $isFirst): PhpClassMember
    {
        $docType = function ($field): ResolvedType {
            if ($field->desc->desc->isRepeated()) {
                if ($field->isEnum) {
                    return $this->ctx->type(Type::arrayOf(Type::int()), false, true);
                } elseif ($field->isMap) {
                    return $this->ctx->type(Type::array());
                } else {
                    return $this->ctx->type(Type::arrayOf(
                        Type::fromField($this->serviceDetails->catalog, $field->desc->desc, false)
                    ), true, true);
                }
            } else {
                // Affects type hinting for required oneofs.
                // TODO(vNext) Handle optional oneofs here.
                if ($field->isEnum) {
                    return $this->ctx->type(Type::int());
                } else {
                    return $this->ctx->type($field->type, true);
                }
            }
        };
        $docExtra = function ($field): Vector {
            if ($field->isEnum) {
                // TODO(vNext): Don't use a fully-qualified name here; and import correctly.
                $enumType = $field->typeSingular->getFullname();
                return Vector::new([
                    "For allowed values, use constants defined on {@see {$enumType}}"
                ]);
            } else {
                return Vector::new([]);
            }
        };

        $methodSignatureArguments = array_map('trim', array_filter(explode(',', $methodSignature)));
        $requiredFields = $methodDetails->allFields
            ->filter(fn ($f) => in_array($f->name, $methodSignatureArguments))
            ->orderBy(fn ($f) => array_search($f->name, $methodSignatureArguments));

        if (count($methodSignatureArguments) !== $requiredFields->count()) {
            throw new \LogicException(sprintf(
                'missing method signature arguments (Expected "%s", found %s)',
                $methodSignature,
                implode(',', $requiredFields->map(fn ($f) => $f->name)->toArray()) ?: 'none'
            ));
        }

        $requiredParams = $requiredFields
            ->map(fn ($f) => $this->toParam($f));

        $newSelf = AST::new($this->ctx->type(Type::self()))();

        foreach ($requiredFields as $requiredField) {
            $callingParam = AST::param(null, AST::var($requiredField->camelName));
            $newSelf = AST::call($newSelf, $requiredField->setter)($callingParam);
        }

        $methodName = $isFirst
            ? 'build'
            : 'buildFrom' . Helpers::toUpperCamelCase(str_replace(',', '_', $methodSignature));

        return AST::method($methodName)
            ->withAccess(Access::PUBLIC, Access::STATIC)
            ->withParams($requiredParams)
            ->withReturnType($this->ctx->type(Type::self()))
            ->withBody(AST::block(AST::return($newSelf)))
            ->withPhpDoc(PhpDoc::block(
                Vector::zip(
                    $requiredFields,
                    fn ($field, $param) =>
                    PhpDoc::param(
                        $this->toParam($field),
                        PhpDoc::preFormattedText(
                            $field->docLines->concat($docExtra($field))
                        ),
                        $docType($field)
                    )
                ),
                PhpDoc::return($this->ctx->type($methodDetails->requestType, true)),
                // TODO(#594): Remove the following line when stable.
                PhpDoc::experimental(),
            ));
    }

    /**
     * Turns a field into a parameter for RPC methods.
     */
    private function toParam(FieldDetails $field): PhpParam
    {
        $resolveType = function ($field): ResolvedType {
            if ($field->desc->desc->isRepeated()) {
                return $this->ctx->type(Type::array());
            }
            // Affects type hinting for required oneofs.
            // TODO(vNext) Handle optional oneofs here.
            if ($field->isEnum) {
                return $this->ctx->type(Type::int());
            } else {
                return $this->ctx->type($field->typeSingular, true);
            }
        };
        return AST::param(
            $resolveType($field),
            AST::var($field->camelName)
        );
    }
}
