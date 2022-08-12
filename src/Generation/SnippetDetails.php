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

use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\Variable;
use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\Type;
use Google\LongRunning\Operation;
use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\GPBType;

// TODO: improve readability
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

    /** @var Set The use statements associated with the given snippet. */
    public Set $useStatements;

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
        $this->useStatements = Set::new();
        $this->serviceClientVar = AST::var($serviceDetails->clientVarName);

        $this->initialize();
    }

    private function initialize(): void
    {
        $responseFullName = $this->methodDetails->responseType->getFullName(true);
        $this->useStatements = $this->useStatements->add(ApiException::class);
        $this->useStatements = $this->useStatements
            ->add($this->serviceDetails->emptyClientType->getFullname(true));

        // TODO: handle cases where the OperationResponse wrapper is not used
        if ($responseFullName === Operation::class) {
            $this->useStatements = $this->useStatements->add(OperationResponse::class);
        } else if ($responseFullName !== GPBEmpty::class) {
            $this->useStatements = $this->useStatements->add($responseFullName);
        }

        foreach ($this->methodDetails->requiredFields as $field) {
            if ($field->isOneOf || $field->isFirstFieldInOneof()) {
                continue;
            }

            // Handle resource based format fields
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
        if (!$field->isRepeated) {
            $this->useStatements = $this->useStatements->add($field->typeSingular->getFullname(true));
        }
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

            if (!$subField->isRequired) {
                continue;
            }

            $nestedVar = AST::var(
                Helpers::toCamelCase($field->typeSingular->name . '_' . $subField->camelName)
            );
            $requiredFields[$subField->setter->getName()] = $nestedVar;
            // TODO: refactor to have example value for strings match format of [VALUE]
            $exampleValue = $subField->exampleValue($this->context);

            // if type a message or enum, nothing more to do
            if (in_array($subField->desc->getType(), [GPBType::MESSAGE, GPBType::ENUM])) {
                continue;
            }

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

        // TODO: ensure params are updated 
        if ($field->isRepeated) {
            $value = AST::array([$value]);
        }

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
