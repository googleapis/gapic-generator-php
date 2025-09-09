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

use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\Variable;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\Type;

class SnippetDetails
{
    /** @var Vector The main sample's parameters. */
    public Vector $sampleParams;

    /** @var Vector The main sample's php doc parameters. */
    public Vector $phpDocParams;

    /** @var Vector The main sample's assignments. */
    public Vector $sampleAssignments;

    /** @var Vector The arguments for the RPC call within the main sample. */
    public Vector $rpcArguments;

    /** @var Vector The assignments within the call sample, used to invoke the main sample. */
    public Vector $callSampleAssignments;

    /** @var Vector The arguments for the main sample, used within the call sample. */
    public Vector $sampleArguments;

    /** @var MethodDetails The method details associated with the given snippet. */
    public MethodDetails $methodDetails;

    /** @var ServiceDetails The service details. */
    public ServiceDetails $serviceDetails;

    /** @var SourceFileContext A context assigned to the file associated with the snippet. */
    public SourceFileContext $context;

    /** @var Variable An AST node representing the service client associated with this method. */
    public Variable $serviceClientVar;

    public function __construct(MethodDetails $methodDetails, ServiceDetails $serviceDetails)
    {
        $this->methodDetails = $methodDetails;
        $this->serviceDetails = $serviceDetails;
        $this->context = new SourceFileContext('');
        $this->phpDocParams = Vector::new();
        $this->sampleParams = Vector::new();
        $this->sampleAssignments = Vector::new();
        $this->rpcArguments = Vector::new();
        $this->callSampleAssignments = Vector::new();
        $this->sampleArguments = Vector::new();
        $this->serviceClientVar = AST::var($serviceDetails->clientVarName);

        $this->initialize();
    }

    private function initialize(): void
    {
        if ($this->methodDetails->isClientStreaming() || $this->methodDetails->isBidiStreaming()) {
            $this->handleBidiOrClientStreaming();
            return;
        }

        foreach ($this->methodDetails->requiredFields as $field) {
            if ($field->isOneOf && !$field->isFirstFieldInOneof()) {
                continue;
            }
            $this->handleField($field);
        }
    }

    /**
     * @param FieldDetails $field
     * @param string|null $parentFieldName
     */
    private function handleField(FieldDetails $field, ?string $parentFieldName = null): void
    {
        // resource based format fields
        if ($field->useResourceTestValue) {
            $this->handleFormattedResource($field, $parentFieldName);
            return;
        }

        // maps
        if ($field->isMap) {
            $this->handleMap($field, $parentFieldName);
            return;
        }

        // messages
        if ($field->isMessage && !$field->isOneOf) {
            $this->handleMessage($field, $parentFieldName);
            return;
        }

        // oneofs
        if ($field->isOneOf) {
            $this->handleOneof($field, $parentFieldName);
            return;
        }

        // scalar/enum
        $this->handleScalarAndEnum($field, $parentFieldName);
    }

    private function handleBidiOrClientStreaming(): void
    {
        // bidi/client stream RPC initiations accept a request message
        $value = AST::new(
            $this->context->type($this->methodDetails->requestType)
        )();

        foreach ($this->methodDetails->requiredFields as $field) {
            if ($field->isOneOf && !$field->isFirstFieldInOneof()) {
                continue;
            }

            $this->handleField($field, '');
            $prefix = $field->useResourceTestValue
                ? 'formatted_'
                : '';
            $setter = $field->setter->getName();
            $value = $value->$setter(
                AST::var(
                    Helpers::toCamelCase(
                        $prefix . $field->camelName
                    )
                )
            );
        }

        $requestVar = AST::var('request');
        $this->sampleAssignments = $this->sampleAssignments->append(
            AST::assign(
                $requestVar,
                $value
            )
        );
        $this->rpcArguments = $this->rpcArguments->append([$requestVar]);
    }

    /**
     * TODO: make map key/value configurable to showcase valid values
     *
     * @param FieldDetails $field
     * @param string|null $parentFieldName
     */
    private function handleMap(FieldDetails $field, ?string $parentFieldName = null): void
    {
        $fieldVar = $this->buildFieldVar($field->camelName, $parentFieldName);
        if ($parentFieldName === null) {
            $this->rpcArguments = $this->rpcArguments->append($fieldVar);
        }
        $this->sampleAssignments = $this->sampleAssignments->append(
            AST::assign(
                $fieldVar,
                AST::array([]),
            )
        );
    }

    /**
     * @param FieldDetails $field
     * @param string|null $parentFieldName
     */
    private function handleMessage(FieldDetails $field, ?string $parentFieldName = null): void
    {
        $fieldVar = $this->buildFieldVar($field->camelName, $parentFieldName);
        if ($parentFieldName === null) {
            $this->rpcArguments = $this->rpcArguments->append($fieldVar);
        }
        $value = AST::new(
            $this->context->type($field->typeSingular)
        )();

        foreach ($field->requiredSubFields as $subField) {
            if ($subField->isOneOf && !$subField->isFirstFieldInOneof()) {
                continue;
            }

            $this->handleField($subField, $fieldVar->name);
            $setter = $subField->setter->getName();
            $prefix = $subField->useResourceTestValue
                ? 'formatted_'
                : '';
            $value = $value->$setter(
                AST::var(
                    Helpers::toCamelCase(
                        $prefix . $fieldVar->name . '_' . $subField->camelName
                    )
                )
            );
        }

        if ($field->isRepeated) {
            if (count($field->requiredSubFields) > 0) {
                $repeatedItemVar = AST::var(Helpers::toCamelCase($field->typeSingular->name));
                $this->sampleAssignments = $this->sampleAssignments->append(
                    AST::assign($repeatedItemVar, $value)
                );
                $value = $repeatedItemVar;
            }
            $value = [$value];
        }

        $this->sampleAssignments = $this->sampleAssignments->append(
            AST::assign($fieldVar, $value)
        );
    }

    /**
     * @param FieldDetails $field
     * @param string|null $parentFieldName
     */
    private function handleScalarAndEnum(FieldDetails $field, ?string $parentFieldName = null): void
    {
        $fieldVar = $this->buildFieldVar($field->camelName, $parentFieldName);
        $arrayElementVar = null;

        if ($parentFieldName === null) {
            $this->rpcArguments = $this->rpcArguments->append($fieldVar);
        }

        if ($field->isRepeated) {
            $arrayElementVar = AST::var($fieldVar->name . 'Element');
            $this->sampleAssignments = $this->sampleAssignments->append(
                AST::assign(
                    $fieldVar,
                    AST::array([$arrayElementVar])
                )
            );
        }

        $this->handleSampleParams($field, $arrayElementVar ?: $fieldVar);
    }

    /**
     * TODO: handle oneofs that use a comment to indicate required instead of an annotation
     *
     * @param FieldDetails $field
     * @param string|null $parentFieldName
     */
    private function handleOneof(FieldDetails $field, ?string $parentFieldName = null): void
    {
        $oneOfName = $field->getOneofDesc()->getName();
        $wrapperVar = null;

        if ($parentFieldName === null) {
            $wrapperVar = AST::var(Helpers::toCamelCase($oneOfName));
            $this->rpcArguments = $this->rpcArguments->append($wrapperVar);
        }

        if ($field->isMessage) {
            $this->handleMessage($field, $parentFieldName ?: '');
        } else {
            $this->handleScalarAndEnum($field, $parentFieldName ?: $oneOfName);
        }

        if ($wrapperVar) {
            $wrapperArgVar = $field->isMessage
                ? AST::var($field->camelName)
                : AST::var(Helpers::toCamelCase($oneOfName . '_' . $field->camelName));
            $this->sampleAssignments = $this->sampleAssignments->append(
                AST::assign(
                    $wrapperVar,
                    AST::call(
                        AST::new(
                            $this->context->type(
                                $field->toOneofWrapperType(
                                    $this->serviceDetails->namespace
                                )
                            )
                        )(),
                        AST::method($field->setter->name)
                    )($wrapperArgVar)
                )
            );
        }
    }

    /**
     * @param FieldDetails $field
     * @param string|null $parentFieldName
     */
    private function handleFormattedResource(
        FieldDetails $field,
        ?string $parentFieldName = null
    ): void {
        $fieldName = Helpers::toCamelCase("formatted_{$parentFieldName}_{$field->name}");
        $var = AST::var($fieldName);
        $arrayElementVar = null;
        $formatMethodArgs = $field->resourceDetails
            ->getParams()
            ->map(function (array $paramDetails) {
                return strtoupper("[$paramDetails[0]]");
            });
        $clientCall = AST::staticCall(
            $this->context->type($this->serviceDetails->emptyClientType),
            $field->resourceDetails->formatMethod
        );
        if ($field->isRepeated) {
            $arrayElementVar = AST::var("{$fieldName}Element");
            $this->sampleAssignments = $this->sampleAssignments->append(
                AST::assign($var, [$arrayElementVar])
            );
        }

        // append a message to the param description guiding users where to find the helper.
        $docLineCount = count($field->docLines);
        $formatCall = $this->serviceDetails->emptyClientType->name .
                      '::' . $field->resourceDetails->formatMethod->getName() . '()';
        $formatString = "Please see {@see $formatCall} for help formatting this field.";
        if ($docLineCount > 0) {
            $lastItem = $field->docLines->last();
            $itemLength = strlen($lastItem);
            if ($lastItem[$itemLength - 1] !== '.') {
                $field->docLines = $field->docLines->append($formatString);
            } else {
                $field->docLines = $field->docLines->skipLast(1);
                $lastItem .= ' ';
                $pos = strpos($formatString, '{');
                if (strlen($formatString) + $itemLength > 80) {
                    $field->docLines = $field->docLines
                        ->append($lastItem . substr($formatString, 0, $pos))
                        ->append(substr($formatString, $pos));
                } else {
                    $field->docLines = $field->docLines->append($lastItem . $formatString);
                }
            }
        } else {
            $field->docLines = $field->docLines->append($formatString);
        }

        $this->handleSampleParams(
            $field,
            $arrayElementVar ?: $var,
            $clientCall($formatMethodArgs),
            PhpDoc::preFormattedText(
                $this->filterDocLines($field->docLines)
            )
        );

        // Don't append to rpcArguments if a parent exists.
        if ($parentFieldName !== null) {
            return;
        }

        $this->rpcArguments = $this->rpcArguments->append($var);
    }

    /**
     * @param FieldDetails $field
     * @param Variable $var
     * @param mixed $value A value override.
     * @param PhpDoc $phpDocText A phpdoc param description override.
     */
    private function handleSampleParams(FieldDetails $field, Variable $var, $value = null, ?PhpDoc $phpDocText = null): void
    {
        $paramType = $field->isEnum
            ? Type::int()
            : $field->typeSingular;
        $param = AST::param(
            $this->context->type($paramType),
            $var
        );
        $value = $value ?: $field->exampleValue($this->context, true, true);
        $this->sampleParams = $this->sampleParams->append($param);
        $this->phpDocParams = $this->phpDocParams->append(
            PhpDoc::param(
                $param,
                $phpDocText ?: PhpDoc::preFormattedText(
                    $this->filterDocLines($field->docLines)
                )
            )
        );
        $this->callSampleAssignments = $this->callSampleAssignments->append(
            AST::assign(
                $var,
                $value
            )
        );
        $this->sampleArguments = $this->sampleArguments->append($var);
    }

    /**
     * TODO: parse [Sample][google.service.v1.Sample] into {@see Sample}. Consider migrating this into PhpDoc.
     *
     * @param Vector $docLines
     * @return Vector
     */
    private function filterDocLines(Vector $docLines): Vector
    {
        return $docLines->map(function ($line) {
            if (is_string($line)) {
                return preg_replace('/^Required. /', '', $line);
            }

            return $line;
        });
    }

    /**
     * @param FieldDetails $field
     * @param string|null $parentFieldName
     */
    private function buildFieldVar(string $fieldName, ?string $parentFieldName): Variable
    {
        $prefix = $parentFieldName === null || $parentFieldName === ''
            ? ''
            : $parentFieldName . '_';
        return AST::var(Helpers::toCamelCase($prefix . $fieldName));
    }
}
