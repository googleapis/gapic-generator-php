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

use Google\Api\HttpRule;
use Google\ApiCore\BidiStream;
use Google\ApiCore\ClientStream;
use Google\ApiCore\OperationResponse;
use Google\ApiCore\PagedListResponse;
use Google\ApiCore\ServerStream;
use Google\Cloud\OperationResponseMapping;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\MethodDescriptorProto;
use Google\Protobuf\Internal\ServiceDescriptorProto;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\CustomOptions;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\Transport;
use Google\Generator\Utils\Type;
use Google\LongRunning\OperationInfo;

abstract class MethodDetails
{
    public const NORMAL = 'normal';
    public const LRO = 'lro';
    public const PAGINATED = 'paginated';
    public const BIDI_STREAMING = 'bidi_streaming';
    public const SERVER_STREAMING = 'server_streaming';
    public const CLIENT_STREAMING = 'client_streaming';
    public const CUSTOM_OP = 'custom_op';

    public const DEPRECATED_MSG = "This method will be removed in the next major version update.";

    public static function create(ServiceDetails $svc, MethodDescriptorProto $desc): MethodDetails
    {
        // TODO: Handle further method types; e.g. streaming, paginated, ...
        return
            static::maybeCreatePaginated($svc, $desc) ??
            static::maybeCreateCustomOperation($svc, $desc) ??
            static::maybeCreateLro($svc, $desc) ??
            static::maybeCreateBidiStreaming($svc, $desc) ??
            static::maybeCreateServerStreaming($svc, $desc) ??
            static::maybeCreateClientStreaming($svc, $desc) ??
            static::createNormal($svc, $desc);
    }

    public static function createMixin(ServiceDetails $svc, MethodDescriptorProto $desc, string $mixinHostServiceFullname): MethodDetails
    {
        $methodDetails = static::create($svc, $desc);
        $methodDetails->mixinServiceFullname = $mixinHostServiceFullname;
        return $methodDetails;
    }

    private static function maybeCreateClientStreaming(ServiceDetails $svc, MethodDescriptorProto $desc): ?MethodDetails
    {
        if (!$desc->getClientStreaming()) {
            return null;
        } else {
            return new class($svc, $desc) extends MethodDetails {
                public function __construct($svc, $desc)
                {
                    parent::__construct($svc, $desc);
                    $this->methodType = MethodDetails::CLIENT_STREAMING;
                    $this->methodReturnType = Type::fromName(ClientStream::class);
                }
            };
        }
    }

    private static function maybeCreateServerStreaming(ServiceDetails $svc, MethodDescriptorProto $desc): ?MethodDetails
    {
        if (!$desc->getServerStreaming()) {
            return null;
        } else {
            return new class($svc, $desc) extends MethodDetails {
                public function __construct($svc, $desc)
                {
                    parent::__construct($svc, $desc);
                    $this->methodType = MethodDetails::SERVER_STREAMING;
                    $this->methodReturnType = Type::fromName(ServerStream::class);
                }
            };
        }
    }

    private static function maybeCreateBidiStreaming(ServiceDetails $svc, MethodDescriptorProto $desc): ?MethodDetails
    {
        if (!$desc->getClientStreaming() || !$desc->getServerStreaming()) {
            return null;
        } else {
            return new class($svc, $desc) extends MethodDetails {
                public function __construct($svc, $desc)
                {
                    parent::__construct($svc, $desc);
                    $this->methodType = MethodDetails::BIDI_STREAMING;
                    $this->methodReturnType = Type::fromName(BidiStream::class);
                }
            };
        }
    }

    private static function maybeCreatePaginated(ServiceDetails $svc, MethodDescriptorProto $desc): ?MethodDetails
    {
        $catalog = $svc->catalog;
        $inputMsg = $catalog->msgsByFullname[$desc->getInputType()];
        $outputMsg = $catalog->msgsByFullname[$desc->getOutputType()];
        $isRestOnly = $svc->transportType === Transport::REST;
        $pageSize = $inputMsg->desc->getFieldByName('page_size');
        if ($isRestOnly && is_null($pageSize)) {
            $pageSize = $inputMsg->desc->getFieldByName('max_results');
        }

        $pageToken = $inputMsg->desc->getFieldByName('page_token');
        $nextPageToken = $outputMsg->desc->getFieldByName('next_page_token');
        // Find the resoures field. Although AIP states that the resource field should be field number 1,
        // this isn't always the case.
        $rawFields = $outputMsg->desc->getField(); // array of field-number -> field descriptor
        $resourceCandidates = Vector::new(array_keys($rawFields))
            ->map(fn ($k) => [$k, $rawFields[$k]])
            ->filter(fn ($x) => $x[1]->isRepeated());
        $resourceByNumber = $resourceCandidates->orderBy(fn ($x) => $x[0])->firstOrNull();
        if ($isRestOnly) {
            $resourceListCandidates = $resourceCandidates->filter(fn ($x) => !ProtoHelpers::isMap($catalog, $x[1]));
            $resourceMapCandidates = $resourceCandidates->filter(fn ($x) => ProtoHelpers::isMap($catalog, $x[1]));
            // If there are more than one of either, do not generate a paginated method.
            if (count($resourceListCandidates) > 1 || count($resourceMapCandidates) > 1) {
                return null;
            }
            // A map field takes precedence over a repeated (i.e. list) field.
            $resourceByNumber = $resourceMapCandidates->orderBy(fn ($x) => $x[0])->firstOrNull();
            if (is_null($resourceByNumber)) {
                $resourceByNumber = $resourceListCandidates->orderBy(fn ($x) => $x[0])->firstOrNull();
            }
        }
        $resourceByPosition = $resourceCandidates->firstOrNull();
        $resources = is_null($resourceByNumber) ? null : $resourceByNumber[1];
        // Valid if it's the first field by position and number, and is not a map.
        $resourceFieldValid = !is_null($resources);
        // Leverage short-circuting.
        if ($resourceFieldValid && !$isRestOnly) {
            $resourceFieldValid &= !ProtoHelpers::isMap($catalog, $resources)
                && $resourceByNumber[0] === $resourceByPosition[0];
        }

        if (is_null($pageSize) || is_null($pageToken) || is_null($nextPageToken) || is_null($resources)) {
            return null;
        }

        $isValidPageSize = !$pageSize->isRepeated();
        if ($isRestOnly) {
            $isValidPageSize = $pageSize->getType() === GPBType::UINT32 || $pageSize->getType() === GPBType::INT32;
        } else {
            $isValidPageSize = $pageSize->getType() === GPBType::INT32;
        }
        if (!$isValidPageSize) {
            throw new \Exception("page_size field must be of type " . ($isRestOnly ? "uint32 or int32" : "int32") . ".");
        }
        if ($pageToken->isRepeated() || $pageToken->getType() !== GPBType::STRING) {
            throw new \Exception("page_token field must be of type string.");
        }
        if ($nextPageToken->isRepeated() || $nextPageToken->getType() !== GPBType::STRING) {
            throw new \Exception("next_page_token field must be of type string.");
        }
        if (!$resourceFieldValid) {
            if ($isRestOnly) {
                throw new \Exception("Item resources field must a map or repeated field.");
            }
            throw new \Exception("Item resources field must be the first repeated field by number and position.");
        }
        return new class($svc, $desc, $outputMsg, $pageSize, $pageToken, $nextPageToken, $resources, $inputMsg) extends MethodDetails {
            public function __construct($svc, $desc, $outputMsg, $pageSize, $pageToken, $nextPageToken, $resources, $inputMsg)
            {
                parent::__construct($svc, $desc);
                $this->methodType = MethodDetails::PAGINATED;
                $this->requestPageSizeGetter = AST::method($pageSize->getGetter());
                $this->requestPageSizeSetter = AST::method($pageSize->getSetter());
                $this->requestPageTokenGetter = AST::method($pageToken->getGetter());
                $this->requestPageTokenSetter = AST::method($pageToken->getSetter());
                $this->responseNextPageTokenGetter = AST::method($nextPageToken->getGetter());
                $this->responseNextPageTokenSetter = AST::method($nextPageToken->getSetter());
                $this->resourcesGetter = AST::method($resources->getGetter());
                $this->resourcesSetter = AST::method($resources->getSetter());
                $this->resourceType = Type::fromField($svc->catalog, $resources, false);
                $this->resourcesFieldName = Helpers::toCamelCase($resources->getName());
                $this->resourcesField = new FieldDetails($svc->catalog, $outputMsg, $resources->underlyingProto);
                $this->methodReturnType = Type::fromName(PagedListResponse::class);
                // Override docs for page_size and page_token fields.
                $this->requiredFields = $this->overrideFieldDocs($svc->catalog, $outputMsg, $this->requiredFields);
                $this->optionalFields = $this->overrideFieldDocs($svc->catalog, $outputMsg, $this->optionalFields);
            }

            private function overrideFieldDocs(ProtoCatalog $catalog, DescriptorProto $outputMsg, Vector $fields): Vector
            {
                return $fields->map(function ($f) use ($catalog, $outputMsg) {
                    switch ($f->name) {
                    case 'page_token':
                        return new FieldDetails($catalog, $outputMsg, $f->desc, Vector::new([
                            'A page token is used to specify a page of values to be returned.',
                            'If no page token is specified (the default), the first page',
                            'of values will be returned. Any page token used here must have',
                            'been generated by a previous call to the API.'
                        ]));
                    case 'page_size':
                        return new FieldDetails($catalog, $outputMsg, $f->desc, Vector::new([
                            'The maximum number of resources contained in the underlying API',
                            'response. The API may return fewer values in a page, even if',
                            'there are additional values to be retrieved.'
                        ]));
                    default:
                        return $f;
                    }
                });
            }

            /** @var PhpMethod *Readonly* The name of the page_size getter method. */
            public PhpMethod $requestPageSizeGetter;

            /** @var PhpMethod *Readonly* The name of the page_size setter method. */
            public PhpMethod $requestPageSizeSetter;

            /** @var PhpMethod *Readonly* The name of the page_token getter method. */
            public PhpMethod $requestPageTokenGetter;

            /** @var PhpMethod *Readonly* The name of the page_token setter method. */
            public PhpMethod $requestPageTokenSetter;

            /** @var PhpMethod *Readonly* The next_page_token getter method. */
            public PhpMethod $responseNextPageTokenGetter;

            /** @var PhpMethod *Readonly* The next_page_token setter method. */
            public PhpMethod $responseNextPageTokenSetter;

            /** @var PhpMethod *Readonly* The name of the resources getter method. */
            public PhpMethod $resourcesGetter;

            /** @var PhpMethod *Readonly* The name of the resources setter method. */
            public PhpMethod $resourcesSetter;

            /** @var Type *Readonly* The type of the resources. */
            public Type $resourceType;

            /** @var string *Readonly* The name of the resources field. */
            public string $resourcesFieldName;

            /** @var FieldDetails *Readonly* The resources field. */
            public FieldDetails $resourcesField;
        };
    }

    private static function maybeCreateLro(ServiceDetails $svc, MethodDescriptorProto $desc): ?MethodDetails
    {
        if ($desc->getOutputType() !== '.google.longrunning.Operation' || $svc->serviceName === 'google.longrunning.Operations') {
            return null;
        } else {
            return new class($svc, $desc) extends MethodDetails {
                public function __construct($svc, $desc)
                {
                    parent::__construct($svc, $desc);
                    $this->methodType = MethodDetails::LRO;
                    $catalog = $svc->catalog;
                    $lroData = ProtoHelpers::getCustomOption($desc, CustomOptions::GOOGLE_LONGRUNNING_OPERATIONINFO, OperationInfo::class);
                    if (is_null($lroData)) {
                        // TODO(miraleung): This currently breaks when building a GAPIC for LRO.
                        throw new \Exception("An LRO method must provide a `google.api.operation` option, missing from {$this->name}.");
                    }
                    $responseMsg = $catalog->msgsByFullname[$svc->packageFullName($lroData->getResponseType())];
                    $metadataMsg = $catalog->msgsByFullname[$svc->packageFullName($lroData->getMetadataType())];
                    $this->lroResponseType = Type::fromMessage($responseMsg->desc);
                    $this->hasEmptyLroResponse = $this->lroResponseType->getFullname() === '\Google\Protobuf\GPBEmpty';
                    $this->lroMetadataType = Type::fromMessage($metadataMsg->desc);
                    $this->methodReturnType = Type::fromName(OperationResponse::class);
                    $this->lroResponseFields = Vector::new($responseMsg->getField())
                        ->map(fn ($x) => new FieldDetails($catalog, $responseMsg, $x));
                }

                /** @var Type *Readonly* The type of the LRO response. */
                public Type $lroResponseType;

                /** @var bool *Readonly* Whether the LRO response type is empty. */
                public bool $hasEmptyLroResponse;

                /** @var Type *Readonly* The type of the LRO metadata. */
                public Type $lroMetadataType;

                /** @var Vector *Readonly* Vector of FieldDetails; all fields of lroResponse type. */
                public Vector $lroResponseFields;
            };
        }
    }

    private static function maybeCreateCustomOperation(ServiceDetails $svc, MethodDescriptorProto $desc): ?MethodDetails
    {
        // This is a DIREGAPIC only feature, and the RPC must be annotated as such.
        if ($svc->transportType !== Transport::REST || !ProtoHelpers::isOperationService($desc)) {
            return null;
        } else {
            return new class($svc, $desc) extends MethodDetails {
                public function __construct($svc, $desc)
                {
                    parent::__construct($svc, $desc);
                    $catalog = $svc->catalog;
                    $this->methodType = MethodDetails::CUSTOM_OP;
                    $this->methodReturnType = Type::fromName(OperationResponse::class);
                    $this->operationService = ProtoHelpers::lookupOperationService($catalog, $desc, $svc->package);

                    // Find the RPC annotated as the polling method on the operation_service.
                    $polling = Vector::new($this->operationService->getMethod())
                        ->filter(fn ($x) =>
                            !is_null(ProtoHelpers::operationPollingMethod($x)));
                    if ($polling->count() == 0) {
                        throw new \Exception("Custom operation service {$this->operationService->getName()} does not provide a polling method marked with `google.cloud.operation_polling_method = true` option.");
                    }
                    $pollingMethod = $polling->firstOrNull();
                    $this->operationPollingMethod = MethodDetails::create($svc, $pollingMethod);

                    // Collect the operation_response_field mapping from the polling request message.
                    $customOperation = $catalog->msgsByFullname[$desc->getOutputType()];
                    $copFields = Vector::new($customOperation->getField())->toMap(
                        fn ($x) => $x->getName(),
                        fn ($x) => new FieldDetails($catalog, $customOperation, $x)
                    );
                    $pollingMsg = $catalog->msgsByFullname[$pollingMethod->getInputType()];
                    $this->operationPollingFields = Vector::new($pollingMsg->getField())
                        ->filter(fn ($x) => ProtoHelpers::isOperationResponseField($x))
                        ->toMap(
                            fn ($x) => new FieldDetails($catalog, $pollingMsg, $x),
                            fn ($x) => $copFields[ProtoHelpers::operationResponseField($x)]
                        );

                    // Collect operation_request_field mapping from input message fields.
                    $inputMsg = $catalog->msgsByFullname[$desc->getInputType()];
                    $pollingFields = Vector::new($pollingMsg->getField())->toMap(
                        fn ($x) => $x->getName(),
                        fn ($x) => new FieldDetails($catalog, $pollingMsg, $x)
                    );
                    $opRequestFields = Vector::new($inputMsg->getField())
                        ->filter(fn ($x) => ProtoHelpers::isOperationRequestField($x))
                        ->filter(fn ($x) => ProtoHelpers::isRequired($pollingFields[ProtoHelpers::operationRequestField($x)]->desc))
                        ->toMap(
                            fn ($x) => $pollingFields[ProtoHelpers::operationRequestField($x)],
                            fn ($x) => new FieldDetails($catalog, $inputMsg, $x)
                        );
                    $this->operationRequestFields = $pollingFields->values()
                        ->filter(fn ($f) => !is_null($opRequestFields->get($f, null)))
                        ->toMap(fn ($f) => $f, fn ($f) => $opRequestFields[$f]);

                    // Collect the operation field mappings.
                    $outputMsg = $catalog->msgsByFullname[$desc->getOutputType()];
                    foreach ($outputMsg->getField() as $field) {
                        $opField = ProtoHelpers::operationField($field);
                        if (is_null($opField)) {
                            continue;
                        }
                        switch ($opField) {
                            case OperationResponseMapping::ERROR_CODE:
                                $this->operationErrorCodeField = new FieldDetails($catalog, $outputMsg, $field);
                                break;
                            case OperationResponseMapping::ERROR_MESSAGE:
                                $this->operationErrorMessageField = new FieldDetails($catalog, $outputMsg, $field);
                                break;
                            case OperationResponseMapping::NAME:
                                $this->operationNameField = new FieldDetails($catalog, $outputMsg, $field);
                                break;
                            case OperationResponseMapping::STATUS:
                                $this->operationStatusField = new FieldDetails($catalog, $outputMsg, $field);
                                break;
                        }
                    }
                }

                /** @var ServiceDescriptorProto *Readonly* The custom operation service that manages this method's operations. */
                public ServiceDescriptorProto $operationService;

                /** @var MethodDetails *Readonly* The method marked as the polling method on the custom operaiton service. */
                public MethodDetails $operationPollingMethod;

                /**
                 * @var Map *Readonly* Map<FieldDetails, FieldDetails> mapping of polling request message fields to the
                 * corresponding request message field to source from.
                 */
                public Map $operationPollingFields;

                /** @var Map *Readonly* Map<FieldDetails, FieldDetails> all fields from the request message that map to operation fields. */
                public Map $operationRequestFields;

                /** @var FieldDetails *Readonly* FieldDetails of the field that represents the operation status. */
                public FieldDetails $operationStatusField;

                /** @var FieldDetails *Readonly* FieldDetails of the field that represents the operation error code. */
                public FieldDetails $operationErrorCodeField;

                /** @var FieldDetails *Readonly* FieldDetails of the field that represents the operation error message. */
                public FieldDetails $operationErrorMessageField;

                /** @var FieldDetails *Readonly* FieldDetails of the field that represents the operation name. */
                public FieldDetails $operationNameField;
            };
        }
    }

    private static function createNormal(ServiceDetails $svc, MethodDescriptorProto $desc): MethodDetails
    {
        return new class($svc, $desc) extends MethodDetails {
            public function __construct($svc, $desc)
            {
                parent::__construct($svc, $desc);
                $this->methodType = MethodDetails::NORMAL;
            }
        };
    }

    /** @var ServiceDetails  The service that contains this method. */
    public ServiceDetails $serviceDetails;

    /** @var ProtoCatalog The proto catalog. */
    public ProtoCatalog $catalog;

    /** @var string *Readonly* The method type - e.g. normal, lro, server-streaming, ... */
    public string $methodType;

    /** @var string *Readonly* The name of the method, as named in the proto. */
    public string $name;

    /** @var string *Readonly* The fully-qualified name of the method, with the proto package and service. */
    public string $fullName;

    /** @var string *Readonly* The name of this method, as required for PHP code. */
    public string $methodName;

    /** @var ?string The full name of this method's original service if it is a mixin, null otherwise. */
    public ?string $mixinServiceFullname;

    /** @var string *Readonly* The name of the test method testing the success case. */
    public string $testSuccessMethodName;

    /** @var string *Readonly* The name of the test method testing the exceptional case. */
    public string $testExceptionMethodName;

    /** @var DescriptorProto *Readonly* The input proto msg descriptor proto of this method. */
    public DescriptorProto $inputMsg;

    /** @var Type *Readonly* The type of the method request message. */
    public Type $requestType;

    /** @var Type *Readonly* The type of the method response message. */
    public Type $responseType;

    /** @var Type *Readonly* The return type of the PHP method. */
    public Type $methodReturnType;

    /** @var bool *Readonly* Whether this RPC returns empty. */
    public bool $hasEmptyResponse;

    /** @var Vector *Readonly* Vector of FieldDetails; all response fields. */
    public Vector $responseFields;

    /** @var Vector *Readonly* Vector of FieldDetails; all request fields. */
    public Vector $allFields;

    /** @var Vector *Readonly* Vector of FieldDetails; all required request fields. */
    public Vector $requiredFields;

    /** @var Vector *Readonly* Vector of FieldDetails; all optional request fields. */
    public Vector $optionalFields;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    /** @var HttpRule *Readonly* HttpRule for method, if given; null otherwise. */
    public ?HttpRule $httpRule;

    /** @var Vector *Readonly* method signature, if specified in google.protobuf.method_signature proto option */
    public ?Vector $methodSignature;

    /** @var ?string *Readonly* REST method, if specified in a 'google.api.http' proto option. */
    public ?string $restMethod;

    public ?array $headerParams;

    /** @var bool *Readonly* Whether the service is deprecated. */
    public bool $isDeprecated = false;

    protected function __construct(ServiceDetails $svc, MethodDescriptorProto $desc)
    {
        $this->serviceDetails = $svc;
        $this->catalog = $svc->catalog;
        $this->inputMsg = $this->catalog->msgsByFullname[$desc->getInputType()];
        $outputMsg = $this->catalog->msgsByFullname[$desc->getOutputType()];
        $this->name = $desc->getName();
        $this->fullName = $svc->serviceName . "." . $desc->getName();
        $this->methodName = Helpers::toCamelCase($this->name);
        $this->mixinServiceFullname = null;
        $this->testSuccessMethodName = $this->methodName . 'Test';
        $this->testExceptionMethodName = $this->methodName . 'ExceptionTest';

        $this->requestType = Type::fromMessage($this->inputMsg->desc);
        $this->responseType = Type::fromMessage($outputMsg->desc);
        $this->hasEmptyResponse = $this->responseType->getFullname() === '\Google\Protobuf\GPBEmpty';
        $this->methodReturnType = $this->responseType;
        $this->responseFields = Vector::new($outputMsg->getField())->map(fn ($x) => new FieldDetails($this->catalog, $outputMsg, $x));
        $this->allFields = Vector::new($this->inputMsg->getField())->map(fn ($x) => new FieldDetails($this->catalog, $this->inputMsg, $x));
        $this->requiredFields = $this->allFields->filter(fn ($x) => $x->isRequired);
        $this->optionalFields = $this->allFields->filter(fn ($x) => !$x->isRequired);
        $this->docLines = $desc->leadingComments;
        $this->httpRule = ProtoHelpers::getCustomOption($desc, CustomOptions::GOOGLE_API_HTTP, HttpRule::class);
        $this->restMethod = is_null($this->httpRule) ? null : $this->httpRule->getPattern();
        $this->headerParams = ProtoHelpers::headerParams($this->catalog, $desc);

        if ($desc->hasOptions() && $desc->getOptions()->hasDeprecated()) {
            $this->isDeprecated = $desc->getOptions()->getDeprecated();
        }

        $this->methodSignature = ProtoHelpers::getCustomOptionRepeated($desc, CustomOptions::GOOGLE_API_METHODSIGNATURE);
    }

    public function isStreaming(): bool
    {
        return $this->methodType === static::BIDI_STREAMING ||
            $this->methodType === static::SERVER_STREAMING ||
            $this->methodType === static::CLIENT_STREAMING;
    }

    public function isServerStreaming(): bool
    {
        return $this->methodType === static::SERVER_STREAMING;
    }

    public function isClientStreaming(): bool
    {
        return $this->methodType === static::CLIENT_STREAMING;
    }

    public function isBidiStreaming(): bool
    {
        return $this->methodType === static::BIDI_STREAMING;
    }

    public function isMixin(): bool
    {
        return $this->mixinServiceFullname !== null;
    }

    // Note: Thes seemingly redundant setter methods exist to faciliate a future
    // refactoring of the public readonly properties to private ones, with public getters.
    public function setDocLines(Vector $newDocLines) : void
    {
        $this->docLines = $newDocLines;
    }

    public function setHttpRule(?HttpRule $newHttpRule) : void
    {
        $this->httpRule = $newHttpRule;
    }
}
