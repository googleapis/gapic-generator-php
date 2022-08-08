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
use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\GPBType;

// TODO: improve readability
class SnippetDetails
{
    /** @var Vector The main sample's parameters. */
    private Vector $sampleParams;

    /** @var Vector The main sample's php doc parameters. */
    private Vector $phpDocParams;

    /** @var Vector The main sample's assignments. */
    private Vector $sampleAssignments;

    /** @var Vector The arguments for the RPC call within the main sample. */
    private Vector $rpcArguments;

    /** @var Vector The assignments within the call sample, used to invoke the main sample. */
    private Vector $callSampleAssignments;

    /** @var Vector The arguments for the main sample, used within the call sample. */
    private Vector $sampleArguments;

    /** @var MethodDetails The method details associated with the given snippet. */
    private MethodDetails $methodDetails;

    /** @var ServiceDetails The service details. */
    private ServiceDetails $serviceDetails;

    /** @var SourceFileContext A context assigned to the file associated with the snippet. */
    private SourceFileContext $context;

    /** @var MethodDetails The use statements associated with the given snippet. */
    private Set $useStatements;

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

        $this->initialize();
    }

    public function getPhpDocParams(): Vector
    {
        return $this->phpDocParams;
    }

    public function getSampleParams(): Vector
    {
        return $this->sampleParams;
    }

    public function getSampleAssignments(): Vector
    {
        return $this->sampleAssignments;
    }

    public function getRpcArguments(): Vector
    {
        return $this->rpcArguments;
    }

    public function getCallSampleAssignments(): Vector
    {
        return $this->callSampleAssignments;
    }

    public function getSampleArguments(): Vector
    {
        return $this->sampleArguments;
    }

    public function getContext(): SourceFileContext
    {
        return $this->context;
    }

    private function initialize(): void
    {
        foreach ($this->methodDetails->allFields as $field) {
            if ($field->isOneOf || $field->isFirstFieldInOneof() || !$field->isRequired) {
                continue;
            }

            if ($field->useResourceTestValue) {
                $this->handleFormattedResource($field);
                continue;
            }

            $this->rpcArguments = $this->rpcArguments->append(
                AST::var($field->camelName)
            );

            if ($field->isMessage) {
                $this->handleMessage($field);
                continue;
            }

            $param = AST::param(
                $this->context->type(
                    Type::fromField(
                        $this->serviceDetails->catalog,
                        $field->desc->desc
                    )
                ),
                AST::var($field->camelName)
            );
            $this->callSampleAssignments = $this->callSampleAssignments->append(
                AST::assign(
                    AST::var($field->camelName),
                    $field->exampleValue($this->context) // TODO: make sure this formats strings as expected (e.g, [VALUE])
                )
            );
            $this->sampleParams = $this->sampleParams->append($param);
            // TODO: filter docLines to make proto references more readable/usable
            $this->phpDocParams = $this->phpDocParams->append(
                PhpDoc::param(
                    $param,
                    PhpDoc::preFormattedText($field->docLines)
                )
            );
            $this->sampleArguments = $this->sampleArguments->append(AST::var($field->camelName));
        }
    }

    private function handleMessage(FieldDetails $field): void
    {
        $messageDescriptor = $this->serviceDetails
            ->catalog
            ->msgsByFullname[$field->fullname];

        // TODO: make the map's value configurable
        if ($field->isMap) {
            $keyName = Helpers::toCamelCase($field->camelName . '_key');
            $valueVar = AST::var(
                Helpers::toCamelCase($field->camelName . '_value')
            );
            
            $this->sampleAssignments = $this->sampleAssignments->append(
                AST::assign(
                    AST::var($field->camelName),
                    AST::array([$keyName => $valueVar])
                )
            );

            return;
        }

        $requiredFields = $this->handleRequiredFieldsOnMessage($messageDescriptor, $field);
        $value = AST::new(
            $this->context->type($field->typeSingular)
        )();

        if ($requiredFields) {
            foreach ($requiredFields as $setter => $v) {
                $value = $value->$setter($v);
            }
        }

        $value = $field->isRepeated
            ? AST::array([$value])
            : $value;

        $this->sampleAssignments = $this->sampleAssignments->append(
            AST::assign(
                AST::var($field->camelName),
                $value
            ) 
        );
    }

    private function handleRequiredFieldsOnMessage(DescriptorProto $messageDescriptor, FieldDetails $field): array
    {
        $requiredFields = [];

        // loop through each field on the message - if it is required set an example value
        // TODO: if nested field is a message, ensure the type is added to the imports
        // TODO: determine how many message layers deep we should go, currently only 1 layer
        foreach ($messageDescriptor->getField() as $protoField) {
            $subField = new FieldDetails($this->serviceDetails->catalog, $messageDescriptor, $protoField);

            if ($subField->isRequired) {
                $nestedVar = AST::var(
                    Helpers::toCamelCase($field->typeSingular->name . '_' . $subField->camelName)
                );
                // TODO: refactor to have example value for strings match format of [VALUE]
                $exampleValue = $subField->exampleValue($this->context);

                // if type is neither message or enum
                if (!in_array($subField->desc->getType(), [GPBType::MESSAGE, GPBType::ENUM])) {
                    $param = AST::param(
                        $this->context->type(
                            Type::fromField(
                                $this->serviceDetails->catalog,
                                $protoField->desc
                            )
                        ),
                        $nestedVar
                    );
                    $this->sampleArguments = $this->sampleArguments->append($nestedVar);
                    $this->callSampleAssignments = $this->callSampleAssignments->append(
                        AST::assign($nestedVar, $exampleValue)
                    );
                    $this->sampleParams = $this->sampleParams->append($param);
                    $this->phpDocParams = $this->phpDocParams->append(
                        PhpDoc::param(
                            $param,
                            PhpDoc::preFormattedText($field->docLines)
                        )
                    );
                }

                $requiredFields[$subField->setter->getName()] = $nestedVar;
            }
        }

        return $requiredFields;
    }

    private function handleFormattedResource(FieldDetails $field): void
    {
        $formatMethodArgs = $field->resourceDetails
            ->getParams()
            ->map(function (array $paramDetails) {
                return strtoupper("[$paramDetails[0]]");
            });
        $value = AST::staticCall(
            $this->context->type($this->serviceDetails->emptyClientType),
            $field->resourceDetails->formatMethod
        )($formatMethodArgs);
        $var = AST::var(Helpers::toCamelCase("formatted_{$field->name}"));
        $param = AST::param(
            $this->context->type(Type::string()),
            $var
        );

        $this->rpcArguments = $this->rpcArguments->append($var);
        $this->sampleArguments = $this->sampleArguments->append($var);
        $this->callSampleAssignments = $this->callSampleAssignments->append(
            AST::assign($var, $value)
        );
        $this->sampleParams = $this->sampleParams->append($param);
        $this->phpDocParams = $this->phpDocParams->append(
            PhpDoc::param(
                $param,
                PhpDoc::preFormattedText($field->docLines)
            )
        );
    }
}
