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
use Google\ApiCore\ResourceTemplate\Parser;
use Google\ApiCore\ResourceTemplate\Segment;
use Google\ApiCore\ServerStream;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\MethodDescriptorProto;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\CustomOptions;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
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

    public static function create(ServiceDetails $svc, MethodDescriptorProto $desc): MethodDetails
    {
        // TODO: Handle further method types; e.g. streaming, paginated, ...
        return
            static::maybeCreatePaginated($svc, $desc) ??
            static::maybeCreateLro($svc, $desc) ??
            static::maybeCreateBidiStreaming($svc, $desc) ??
            static::maybeCreateServerStreaming($svc, $desc) ??
            static::maybeCreateClientStreaming($svc, $desc) ??
            static::createNormal($svc, $desc);
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
        $pageSize = $inputMsg->desc->getFieldByName('page_size');
        $pageToken = $inputMsg->desc->getFieldByName('page_token');
        $nextPageToken = $outputMsg->desc->getFieldByName('next_page_token');
        $resources = $outputMsg->desc->getFieldByNumber(1);
        if (is_null($pageSize) || is_null($pageToken) || is_null($nextPageToken) || is_null($resources)) {
            return null;
        } else {
            if ($pageSize->isRepeated() || $pageSize->getType() !== GPBType::INT32) {
                throw new \Exception("page_size field must be of type int32.");
            }
            if ($pageToken->isRepeated() || $pageToken->getType() !== GPBType::STRING) {
                throw new \Exception("page_token field must be of type string.");
            }
            if ($nextPageToken->isRepeated() || $nextPageToken->getType() !== GPBType::STRING) {
                throw new \Exception("next_page_token field must be of type string.");
            }
            // TODO: Currently the proto descriptors are constructed incorrectly, meaning that some of their
            // internal details are not complete. This means ->isMap() cannot currently be called.
            // This will be fixed in a near-future PR.
            //if (!$resources->isRepeated() || $resources->isMap()) {
            if (!$resources->isRepeated()) {
                throw new \Exception("Item resources field must be a repeated field with field-number 1.");
            }
            return new class($svc, $desc, $pageSize, $pageToken, $nextPageToken, $resources) extends MethodDetails {
                public function __construct($svc, $desc, $pageSize, $pageToken, $nextPageToken, $resources)
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
                    $this->resourceType = Type::fromField($svc->catalog, $resources);
                    $this->resourcesFieldName = Helpers::toCamelCase($resources->getName());
                    $this->resourcesField = new FieldDetails($svc->catalog, $resources->underlyingProto);
                    $this->methodReturnType = Type::fromName(PagedListResponse::class);
                    // Override docs for page_size and page_token fields.
                    $this->requiredFields = $this->overrideFieldDocs($svc->catalog, $this->requiredFields);
                    $this->optionalFields = $this->overrideFieldDocs($svc->catalog, $this->optionalFields);
                }

                private function overrideFieldDocs(ProtoCatalog $catalog, Vector $fields): Vector
                {
                    return $fields->map(function($f) use($catalog) {
                        switch ($f->name) {
                            case 'page_token':
                                return new FieldDetails($catalog, $f->desc, Vector::new([
                                    'A page token is used to specify a page of values to be returned.',
                                    'If no page token is specified (the default), the first page',
                                    'of values will be returned. Any page token used here must have',
                                    'been generated by a previous call to the API.'
                                ]));
                            case 'page_size':
                                return new FieldDetails($catalog, $f->desc, Vector::new([
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
    }

    private static function maybeCreateLro(ServiceDetails $svc, MethodDescriptorProto $desc): ?MethodDetails
    {
        if ($desc->getOutputType() !== '.google.longrunning.Operation') {
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
                        throw new \Exception('An LRO method must provide a `google.api.operation` option.');
                    }
                    $responseMsg = $catalog->msgsByFullname[$svc->packageFullName($lroData->getResponseType())];
                    $metadataMsg = $catalog->msgsByFullname[$svc->packageFullName($lroData->getMetadataType())];
                    $this->lroResponseType = Type::fromMessage($responseMsg->desc);
                    $this->lroMetadataType = Type::fromMessage($metadataMsg->desc);
                    $this->methodReturnType = Type::fromName(OperationResponse::class);
                    $this->lroResponseFields = Vector::new($responseMsg->getField())->map(fn($x) => new FieldDetails($catalog, $x));
                }

                /** @var Type *Readonly* The type of the LRO response. */
                public Type $lroResponseType;

                /** @var Type *Readonly* The type of the LRO metadata. */
                public Type $lroMetadataType;

                /** @var Vector *Readonly* Vector of FieldDetails; all fields of lroResponse type. */
                public Vector $lroResponseFields;
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

    /** @var string *Readonly* The method type - e.g. normal, lro, server-streaming, ... */
    public string $methodType;

    /** @var string *Readonly* The name of the method, as named in the proto. */
    public string $name;

    /** @var string *Readonly* The name of this method, as required for PHP code. */
    public string $methodName;

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

    /** @var Vector *Readonly* Vector of FieldDetails; all request fields. */
    public Vector $allFields;

    /** @var Vector *Readonly* Vector of FieldDetails; all required request fields. */
    public Vector $requiredFields;

    /** @var Vector *Readonly* Vector of FieldDetails; all optional request fields. */
    public Vector $optionalFields;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    /** @var ?string *Readonly* REST method, if specified in a 'google.api.http' proto option. */
    public ?string $restMethod;

    /** @var ?string *Readonly* REST URI template, if specified in a 'google.api.http' proto option. */
    public ?string $restUriTemplate;

    /** @var ?string *Readonly* REST body, if specified in a 'google.api.http' proto option. */
    public ?string $restBody;

    /** @var ?Map *Readonly* Map of string to Vector of strings; placeholder name -> list of property getters. */
    public Map $restRoutingHeaders;

    protected function __construct(ServiceDetails $svc, MethodDescriptorProto $desc)
    {
        $catalog = $svc->catalog;
        $this->inputMsg = $catalog->msgsByFullname[$desc->getInputType()];
        $outputMsg = $catalog->msgsByFullname[$desc->getOutputType()];
        $this->name = $desc->getName();
        $this->methodName = Helpers::toCamelCase($this->name);
        $this->testSuccessMethodName = $this->methodName . 'Test';
        $this->testExceptionMethodName = $this->methodName . 'ExceptionTest';
        $this->requestType = Type::fromMessage($this->inputMsg->desc);
        $this->responseType = Type::fromMessage($outputMsg->desc);
        $this->methodReturnType = $this->responseType;
        $this->allFields = Vector::new($this->inputMsg->getField())->map(fn($x) => new FieldDetails($catalog, $x));
        $this->requiredFields = $this->allFields->filter(fn($x) => $x->isRequired);
        $this->optionalFields = $this->allFields->filter(fn($x) => !$x->isRequired);
        $this->docLines = $desc->leadingComments;
        $http = ProtoHelpers::getCustomOption($desc, CustomOptions::GOOGLE_API_HTTP, HttpRule::class);
        if (is_null($http)) {
            $this->restMethod = null;
            $this->restUriTemplate = null;
            $this->restBody = null;
            $this->restRoutingHeaders = Map::new([]);
        } else {
            $this->restMethod = $http->getPattern();
            $uriTemplateGetter = Helpers::toCamelCase("get_{$http->getPattern()}");
            $this->restUriTemplate = $http->$uriTemplateGetter();
            $this->restBody = $http->getBody() === '' ? null : $http->getBody();
            if ($this->restUriTemplate === '') {
                throw new \Exception('REST URI must be specified.');
            }
            if ($this->restUriTemplate[0] !== '/') {
                throw new \Exception("REST URI must be an absolute path starting with '/'");
            }
            $segments = Parser::parseSegments(str_replace(':', '/', substr($this->restUriTemplate, 1)));
            $this->restRoutingHeaders = Vector::new($segments)
                ->filter(fn($x) => $x->getSegmentType() === Segment::VARIABLE_SEGMENT)
                ->toMap(fn($x) => $x->getKey(), function($x) use ($catalog) {
                    $fieldList = Vector::new(explode('.', $x->getKey()));
                    $msg = $this->inputMsg;
                    $result = [];
                    foreach ($fieldList as $index => $fieldName) {
                        $field = $msg->desc->getFieldByName($fieldName);
                        if (is_null($field)) {
                            throw new \Exception("Field '{$fieldName}' does not exist.");
                        }
                        if ($index !== count($fieldList) - 1) {
                            if ($field->isRepeated()) {
                                throw new \Exception("Field '{$fieldName}' must not be repeated.");
                            }
                            if ($field->getType() !== GPBType::MESSAGE) {
                                throw new \Exception("Field '{$fieldName}' must be of message type.");
                            }
                            $msg = $catalog->msgsByFullname[$field->getMessageType()];
                        }
                        $result[] = $field->getGetter();
                    }
                    return Vector::new($result);
                });
        }
    }

    public function isStreaming()
    {
        return $this->methodType === static::BIDI_STREAMING ||
            $this->methodType === static::SERVER_STREAMING ||
            $this->methodType === static::CLIENT_STREAMING;
    }
}
