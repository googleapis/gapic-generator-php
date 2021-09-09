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

use Google\ApiCore\ApiException;
use Google\ApiCore\BidiStream;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\ServerStream;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\ApiCore\Transport\TransportInterface;
use Google\Generator\Ast\Access;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpClassMember;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\Type;
use Google\LongRunning\GetOperationRequest;
use Google\LongRunning\Operation;
use Google\Protobuf\Any;
use Google\Rpc\Code;

class UnitTestsGenerator
{
    public static function generate(SourceFileContext $ctx, ServiceDetails $serviceDetails): PhpFile
    {
        return (new UnitTestsGenerator($ctx, $serviceDetails))->generateImpl();
    }

    private SourceFileContext $ctx;
    private ServiceDetails $serviceDetails;
    private $assertTrue;
    private $assertFalse;
    private $assertEquals;
    private $assertSame;
    private $assertProtobufEquals;
    private $assertNull;
    private $assertInstanceOf;
    private $assertArrayHasKey;
    private $fail;

    private function __construct(SourceFileContext $ctx, ServiceDetails $serviceDetails)
    {
        $this->ctx = $ctx;
        $this->serviceDetails = $serviceDetails;
        $this->assertTrue = AST::call(AST::THIS, AST::method('assertTrue'));
        $this->assertFalse = AST::call(AST::THIS, AST::method('assertFalse'));
        $this->assertEquals = AST::call(AST::THIS, AST::method('assertEquals'));
        $this->assertSame = AST::call(AST::THIS, AST::method('assertSame'));
        $this->assertProtobufEquals = AST::call(AST::THIS, AST::method('assertProtobufEquals'));
        $this->assertNull = AST::call(AST::THIS, AST::method('assertNull'));
        $this->assertInstanceOf = AST::call(AST::THIS, AST::method('assertInstanceOf'));
        $this->assertArrayHasKey = AST::call(AST::THIS, AST::method('assertArrayHasKey'));
        $this->fail = AST::call(AST::THIS, AST::method('fail'));
    }

    private function generateImpl(): PhpFile
    {
        // TODO(vNext): Remove the forced addition of these `use` clauses.
        $this->ctx->type(Type::fromName(BidiStream::class));
        $this->ctx->type(Type::fromName(\Google\ApiCore\LongRunning\OperationsClient::class));
        $this->ctx->type(Type::fromName(ServerStream::class));
        $this->ctx->type(Type::fromName(GetOperationRequest::class));
        $this->ctx->type(Type::fromName(Any::class));
        $this->ctx->type(Type::fromName(\Google\Protobuf\GPBEmpty::class));
        $this->ctx->type(Type::fromName(\PHPUnit\Framework\TestCase::class));
        $this->ctx->type(Type::fromName(ApiException::class));
        $this->ctx->type(Type::fromName(Code::class));
        $this->ctx->type(Type::stdClass());
        $this->ctx->type($this->serviceDetails->grpcClientType);
        foreach ($this->serviceDetails->methods->filter(fn ($x) => $x->methodType !== MethodDetails::CLIENT_STREAMING) as $method) {
            $this->ctx->type($method->requestType);
        }
        foreach ($this->serviceDetails->methods as $method) {
            foreach ($method->allFields as $field) {
                if ($field->isRepeated && $field->typeSingular->isClass() && $field->isRequired) {
                    $this->ctx->type($field->typeSingular);
                }
            }
        }
        // Generate file content
        $file = AST::file($this->generateClass())
            ->withApacheLicense($this->ctx->licenseYear)
            ->withGeneratedCodeWarning();
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function generateClass(): PhpClass
    {
        return AST::class($this->serviceDetails->unitTestsType, $this->ctx->type(Type::fromName(GeneratedTest::class)))
            ->withPhpDoc(PhpDoc::block(
                is_null($this->serviceDetails->unitTestGroupName) ? null : PhpDoc::group($this->serviceDetails->unitTestGroupName),
                PhpDoc::group('gapic')
            ))
            ->withMember($this->createTransport())
            ->withMember($this->createCredentials())
            ->withMember($this->createClient())
            ->withMembers($this->serviceDetails->methods->flatMap(fn ($x) => Vector::new($this->testCases($x))));
    }

    private function createTransport(): PhpClassMember
    {
        $deserialize = AST::param(null, AST::var('deserialize'), AST::NULL);
        return AST::method('createTransport')
            ->withAccess(Access::PRIVATE)
            ->withParams($deserialize)
            ->withBody(AST::block(
                AST::return(AST::new($this->ctx->type(Type::fromName(MockTransport::class)))($deserialize->var))
            ))
            ->withPhpDoc(PhpDoc::block(
                // TODO(vNext): Don't use broken no-import here.
                PhpDoc::return($this->ctx->type(Type::fromName(TransportInterface::class), false, true))
            ));
    }

    private function createCredentials(): PhpClassMember
    {
        return AST::method('createCredentials')
            ->withAccess(Access::PRIVATE)
            ->withBody(AST::block(
                AST::return(
                    AST::call(AST::THIS, AST::method('getMockBuilder'))(
                        AST::access($this->ctx->type(Type::fromName(CredentialsWrapper::class)), AST::CLS)
                    )
                        ->instanceCall(AST::method('disableOriginalConstructor'))()
                        ->instanceCall(AST::method('getMock'))()
                )
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::return($this->ctx->type(Type::fromName(CredentialsWrapper::class)))
            ));
    }

    private function createClient(): PhpClassMember
    {
        $options = AST::param($this->ctx->type(Type::array()), AST::var('options'), AST::array([]));
        // TODO(vNext): Remove this brokenness. Note that this only "works" because this code is never called when it's broken.
        $brokenNoImport = !$this->serviceDetails->methods->any(fn ($x) => $x->methodType !== MethodDetails::CLIENT_STREAMING);
        return AST::method('createClient')
            ->withAccess(Access::PRIVATE)
            ->withParams($options)
            ->withBody(AST::block(
                AST::binaryOp($options->var, '+=', AST::array([
                    'credentials' => AST::call(AST::THIS, $this->createCredentials())()
                ])),
                AST::return(AST::new($this->ctx->type($this->serviceDetails->emptyClientType, false, $brokenNoImport))($options->var))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::return($this->ctx->type($this->serviceDetails->emptyClientType, false, $brokenNoImport))
            ));
    }

    private function testCases(MethodDetails $method)
    {
        switch ($method->methodType) {
            case MethodDetails::CUSTOM_OP:
            case MethodDetails::NORMAL:
                yield $this->testSuccessCaseNormal($method);
                yield $this->testExceptionalCaseNormal($method);
                break;
            case MethodDetails::LRO:
                yield $this->testSuccessCaseLro($method);
                yield $this->testExceptionalCaseLro($method);
                break;
            case MethodDetails::PAGINATED:
                yield $this->testSuccessCasePaginated($method);
                yield $this->testExceptionalCaseNormal($method); // Paginated exceptional case is the same as for normal method.
                break;
            case MethodDetails::BIDI_STREAMING:
                yield $this->testSuccessCaseBidiStreaming($method);
                yield $this->testExceptionalCaseBidiStreaming($method);
                break;
            case MethodDetails::SERVER_STREAMING:
                yield $this->testSuccessCaseServerStreaming($method);
                yield $this->testExceptionalCaseServerStreaming($method);
                break;
            case MethodDetails::CLIENT_STREAMING:
                // TODO(vNext): The monolithic generator does not generate client-streaming test code.
                // That behaviour is reproduced here, but we may add new tests after the
                // initial release of this micro-generator.
                break;
            default:
                throw new \Exception("Cannot handle method-type: '{$method->methodType}'");
        }
    }

    private function testSuccessCaseNormal(MethodDetails $method): PhpMethod
    {
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $transport = AST::var('transport');
        $client = AST::var('client');
        $expectedResponse = AST::var('expectedResponse');
        [$requestPerField, $requestCallArgs] = $prod->perFieldRequest($method);
        $response = AST::var('response');
        // Response fields must be initialized after request fields, to ensure naming which is compatible with monolith.
        $responsePerField = $prod->perField($method->responseFields->filter(fn ($x) => $x->isInTestResponse));
        $actualRequests = AST::var('actualRequests');
        $actualFuncCall = AST::var('actualFuncCall');
        $actualRequestObject = AST::var('actualRequestObject');
        $actualValue = AST::var('actualValue');
        $rpcHostServiceName = $method->isMixin() ? $method->mixinServiceFullname : $this->serviceDetails->serviceName;
        return AST::method($method->testSuccessMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
                '// Mock response',
                $responsePerField->map(fn ($x) => AST::assign($x->var, $x->value)),
                AST::assign($expectedResponse, AST::new($this->ctx->type($method->responseType))()),
                $responsePerField->map(fn ($x) => $expectedResponse->instanceCall($x->field->setter)($x->var)),
                AST::call($transport, AST::method('addResponse'))($expectedResponse),
                // TODO(vNext): Always add this comment.
                $method->requiredFields->any() ? '// Mock request' : null,
                $requestPerField->map(fn ($x) => $x->initCode),
                $method->hasEmptyResponse ?
                    $client->instanceCall(AST::method($method->methodName))($requestCallArgs) :
                    Vector::new([
                        AST::assign($response, $client->instanceCall(AST::method($method->methodName))($requestCallArgs)),
                        ($this->assertEquals)($expectedResponse, $response)
                    ]),
                AST::assign($actualRequests, $transport->instanceCall(AST::method('popReceivedCalls'))()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($actualRequests)),
                AST::assign($actualFuncCall, AST::index($actualRequests, 0)->instanceCall(AST::method('getFuncCall'))()),
                AST::assign($actualRequestObject, AST::index($actualRequests, 0)->instanceCall(AST::method('getRequestObject'))()),
                ($this->assertSame)("/$rpcHostServiceName/{$method->name}", $actualFuncCall),
                $requestPerField->map(fn ($x) => Vector::new([
                    AST::assign($actualValue, $actualRequestObject->instanceCall($x->field->getter)()),
                    !$x->field->isOneOf
                        ? ($this->assertProtobufEquals)($x->var, $actualValue)
                        : ($this->assertTrue)(
                            // Variable of type oneof wrapper, e.g. supplementaryData.
                            AST::call(
                                AST::var(Helpers::toCamelCase($x->field->getOneofDesc()->getName())),
                                AST::method("is" . Helpers::toUpperCamelCase($x->field->name))
                            )()
                        )
                ])),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testExceptionalCaseNormal(MethodDetails $method): PhpMethod
    {
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $transport = AST::var('transport');
        $client = AST::var('client');
        $status = AST::var('status');
        $expectedExceptionMessage  = AST::var('expectedExceptionMessage ');
        [$requestPerField, $requestCallArgs] = $prod->perFieldRequest($method);
        $ex = AST::var('ex');
        return AST::method($method->testExceptionMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
                AST::assign($status, AST::new($this->ctx->type(Type::stdClass()))()),
                AST::assign(AST::access($status, AST::property('code')), AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS'))),
                AST::assign(AST::access($status, AST::property('details')), 'internal error'),
                AST::assign($expectedExceptionMessage, AST::call(AST::method('json_encode'))(AST::array([
                    'message' => 'internal error',
                    'code' => AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS')),
                    'status' => 'DATA_LOSS',
                    'details' => AST::array([]),
                ]), AST::constant('JSON_PRETTY_PRINT'))),
                $transport->instanceCall(AST::method('addResponse'))(AST::NULL, $status),
                // TODO(vNext): Always add this comment.
                $method->requiredFields->any() ? '// Mock request' : null,
                $requestPerField->map(fn ($x) => $x->initCode),
                AST::try(
                    $client->instanceCall(AST::method($method->methodName))($requestCallArgs),
                    '// If the $client method call did not throw, fail the test',
                    ($this->fail)('Expected an ApiException, but no exception was thrown.')
                )->catch($this->ctx->type(Type::fromName(ApiException::class)), $ex)(
                    ($this->assertEquals)(AST::access($status, AST::property('code')), $ex->instanceCall(AST::method('getCode'))()),
                    ($this->assertEquals)($expectedExceptionMessage, $ex->instanceCall(AST::method('getMessage'))()),
                ),
                '// Call popReceivedCalls to ensure the stub is exhausted',
                $transport->instanceCall(AST::method('popReceivedCalls'))(),
                ($this->assertTrue)($transport->instanceCall(AST::method('isExhausted'))()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testSuccessCaseLro(MethodDetails $method): PhpMethod
    {
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $expectedResponse = AST::var('expectedResponse');
        $anyResponse = AST::var('anyResponse');
        $completeOperation = AST::var('completeOperation');
        [$requestPerField, $requestCallArgs] = $prod->perFieldRequest($method);
        // LRO response initialisation must come after request initialisation, to be monolith compatible.
        $lroResponsePerField = $prod->perField($method->lroResponseFields->filter(fn ($x) => $x->isInTestResponse));
        $response = AST::var('response');
        $apiRequests = AST::var('apiRequests');
        $operationsRequestsEmpty = AST::var('operationsRequestsEmpty');
        $actualApiFuncCall = AST::var('actualApiFuncCall');
        $actualApiRequestObject = AST::var('actualApiRequestObject');
        $actualValue = AST::var('actualValue');
        $expectedOperationsRequestObject = AST::var('expectedOperationsRequestObject');
        $apiRequestsEmpty = AST::var('apiRequestsEmpty');
        $operationsRequests = AST::var('operationsRequests');
        $actualOperationsFuncCall = AST::var('actualOperationsFuncCall');
        $actualOperationsRequestObject = AST::var('actualOperationsRequestObject');
        [$initCode, $operationsTransport, $client, $transport] = $this->lroTestInit($method->testSuccessMethodName);
        return AST::method($method->testSuccessMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                $initCode,
                $lroResponsePerField->map(fn ($x) => AST::assign($x->var, $x->value)),
                AST::assign($expectedResponse, AST::new($this->ctx->type($method->lroResponseType))()),
                $lroResponsePerField->map(fn ($x) => $expectedResponse->instanceCall($x->field->setter)($x->var)),
                AST::assign($anyResponse, AST::new($this->ctx->type(Type::fromName(Any::class)))()),
                $anyResponse->setValue($expectedResponse->serializeToString()),
                AST::assign($completeOperation, AST::new($this->ctx->type(Type::fromName(Operation::class)))()),
                $completeOperation->setName("operations/{$method->testSuccessMethodName}"),
                $completeOperation->setDone(true),
                $completeOperation->setResponse($anyResponse),
                $operationsTransport->addResponse($completeOperation),
                // TODO(vNext): Always add this comment.
                $method->requiredFields->any() ? '// Mock request' : null,
                $requestPerField->map(fn ($x) => $x->initCode),
                AST::assign($response, $client->instanceCall(AST::method($method->methodName))($requestCallArgs)),
                ($this->assertFalse)($response->isDone()),
                ($this->assertNull)($response->getResult()),
                AST::assign($apiRequests, $transport->popReceivedCalls()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($apiRequests)),
                AST::assign($operationsRequestsEmpty, $operationsTransport->popReceivedCalls()),
                ($this->assertSame)(0, AST::call(AST::COUNT)($operationsRequestsEmpty)),
                AST::assign($actualApiFuncCall, $apiRequests[0]->getFuncCall()),
                AST::assign($actualApiRequestObject, $apiRequests[0]->getRequestObject()),
                ($this->assertSame)("/{$this->serviceDetails->serviceName}/{$method->name}", $actualApiFuncCall),
                $requestPerField->map(fn ($x) => Vector::new([
                    AST::assign($actualValue, $actualApiRequestObject->instanceCall($x->field->getter)()),
                    ($this->assertProtobufEquals)($x->var, $actualValue),
                ])),
                AST::assign($expectedOperationsRequestObject, AST::new($this->ctx->type(Type::fromName(GetOperationRequest::class)))()),
                $expectedOperationsRequestObject->setName("operations/{$method->testSuccessMethodName}"),
                $response->pollUntilComplete(AST::array([
                    'initialPollDelayMillis' => 1,
                ])),
                ($this->assertTrue)($response->isDone()),
                ($this->assertEquals)($expectedResponse, $response->getResult()),
                AST::assign($apiRequestsEmpty, $transport->popReceivedCalls()),
                ($this->assertSame)(0, AST::call(AST::COUNT)($apiRequestsEmpty)),
                AST::assign($operationsRequests, $operationsTransport->popReceivedCalls()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($operationsRequests)),
                AST::assign($actualOperationsFuncCall, $operationsRequests[0]->getFuncCall()),
                AST::assign($actualOperationsRequestObject, $operationsRequests[0]->getRequestObject()),
                ($this->assertSame)('/google.longrunning.Operations/GetOperation', $actualOperationsFuncCall),
                ($this->assertEquals)($expectedOperationsRequestObject, $actualOperationsRequestObject),
                ($this->assertTrue)($transport->isExhausted()),
                ($this->assertTrue)($operationsTransport->isExhausted()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testExceptionalCaseLro(MethodDetails $method): PhpMethod
    {
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $status = AST::var('status');
        $expectedExceptionMessage = AST::var('expectedExceptionMessage');
        [$requestPerField, $requestCallArgs] = $prod->perFieldRequest($method);
        $response = AST::var('response');
        $expectedOperationsRequestObject = AST::var('expectedOperationsRequestObject');
        $ex = AST::var('ex');
        [$initCode, $operationsTransport, $client, $transport] = $this->lroTestInit($method->testSuccessMethodName);
        return AST::method($method->testExceptionMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                $initCode,
                AST::assign($status, AST::new($this->ctx->type(Type::stdClass()))()),
                AST::assign(AST::access($status, AST::property('code')), AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS'))),
                AST::assign(AST::access($status, AST::property('details')), 'internal error'),
                AST::assign($expectedExceptionMessage, AST::call(AST::method('json_encode'))(AST::array([
                    'message' => 'internal error',
                    'code' => AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS')),
                    'status' => 'DATA_LOSS',
                    'details' => AST::array([]),
                ]), AST::constant('JSON_PRETTY_PRINT'))),
                $operationsTransport->instanceCall(AST::method('addResponse'))(AST::NULL, $status),
                // TODO(vNext): Always add this comment.
                $method->requiredFields->any() ? '// Mock request' : null,
                $requestPerField->map(fn ($x) => $x->initCode),
                AST::assign($response, $client->instanceCall(AST::method($method->methodName))($requestCallArgs)),
                ($this->assertFalse)($response->isDone()),
                ($this->assertNull)($response->getResult()),
                AST::assign($expectedOperationsRequestObject, AST::new($this->ctx->type(Type::fromName(GetOperationRequest::class)))()),
                $expectedOperationsRequestObject->setName("operations/{$method->testSuccessMethodName}"),
                AST::try(
                    $response->pollUntilComplete(AST::array([
                        'initialPollDelayMillis' => 1,
                    ])),
                    '// If the pollUntilComplete() method call did not throw, fail the test',
                    ($this->fail)('Expected an ApiException, but no exception was thrown.'),
                )->catch($this->ctx->type(Type::fromName(ApiException::class)), $ex)(
                    ($this->assertEquals)(AST::access($status, AST::property('code')), $ex->instanceCall(AST::method('getCode'))()),
                    ($this->assertEquals)($expectedExceptionMessage, $ex->instanceCall(AST::method('getMessage'))()),
                ),
                '// Call popReceivedCalls to ensure the stubs are exhausted',
                $transport->popReceivedCalls(),
                $operationsTransport->popReceivedCalls(),
                ($this->assertTrue)($transport->isExhausted()),
                ($this->assertTrue)($operationsTransport->isExhausted()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function lroTestInit($testName)
    {
        $operationsTransport = AST::var('operationsTransport');
        $operationsClient = AST::var('operationsClient');
        $transport = AST::var('transport');
        $client = AST::var('client');
        $incompleteOperation = AST::var('incompleteOperation');
        $initCode = Vector::new([
            AST::assign($operationsTransport, AST::call(AST::THIS, $this->createTransport())()),
            AST::assign($operationsClient, AST::new($this->ctx->type(Type::fromName(OperationsClient::class)))(AST::array([
                'serviceAddress' => '',
                'transport' => $operationsTransport,
                'credentials' => AST::call(AST::THIS, $this->createCredentials())(),
            ]))),
            AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
            AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array([
                'transport' => $transport,
                'operationsClient' => $operationsClient,
            ]))),
            ($this->assertTrue)($transport->isExhausted()),
            ($this->assertTrue)($operationsTransport->isExhausted()),
            '// Mock response',
            AST::assign($incompleteOperation, AST::new($this->ctx->type(Type::fromName(Operation::class)))()),
            $incompleteOperation->setName("operations/{$testName}"),
            $incompleteOperation->setDone(false),
            $transport->addResponse($incompleteOperation),
        ]);
        return [$initCode, $operationsTransport, $client, $transport];
    }

    private function testSuccessCasePaginated(MethodDetails $method): PhpMethod
    {
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $transport = AST::var('transport');
        $client = AST::var('client');
        $expectedResponse = AST::var('expectedResponse');
        $mockResourceElement = AST::var("{$method->resourcesFieldName}Element");
        $mockResource = AST::var($method->resourcesFieldName);
        [$requestPerField, $requestCallArgs] = $prod->perFieldRequest($method);
        // Response fields must be initialized after request fields, to ensure naming which is compatible with monolith.
        $responsePerField = $prod->perField(
            $method->responseFields->filter(fn ($x) => $x->isInTestResponse),
            Map::new(['next_page_token' => ''])
        );
        $response = AST::var('response');
        $resources = AST::var('resources');
        $mockResourceElementValue =
            $prod->value($method->resourcesField, $method->resourcesField->name . '_element', false);
        $actualRequests = AST::var('actualRequests');
        $actualFuncCall = AST::var('actualFuncCall');
        $actualRequestObject = AST::var('actualRequestObject');
        $actualValue = AST::var('actualValue');
        $rpcHostServiceName = $method->isMixin() ? $method->mixinServiceFullname : $this->serviceDetails->serviceName;

        // Should apply only to REST-only transports. This allowance of map-as-resource-field is set
        // in MethodDetails.php.
        $resourcesIsMap = $method->resourcesField->isMap;
        // Type switching, ugh...
        $resourcesKeyIndex = $resourcesIsMap
            ? Helpers::toCamelCase($method->resourcesField->camelName . '_key')
            : 0;

        return AST::method($method->testSuccessMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
                '// Mock response',
                $responsePerField->map(fn ($x) => AST::assign($x->var, $x->value)),

                // Maps will be initialized directly.
                $resourcesIsMap ? null : AST::assign($mockResourceElement, $mockResourceElementValue),
                $resourcesIsMap
                    ? AST::assign($mockResource, $prod->value($method->resourcesField, $method->resourcesField->name, false))
                    : AST::assign($mockResource, AST::array([$mockResourceElement])),
                AST::assign($expectedResponse, AST::new($this->ctx->type($method->responseType))()),
                $responsePerField->map(fn ($x) => $expectedResponse->instanceCall($x->field->setter)($x->var)),
                $expectedResponse->instanceCall($method->resourcesSetter)($mockResource),
                AST::call($transport, AST::method('addResponse'))($expectedResponse),
                // TODO(vNext): Always add this comment.
                $method->requiredFields->any() ? '// Mock request' : null,
                $requestPerField->map(fn ($x) => $x->initCode),
                AST::assign($response, $client->instanceCall(AST::method($method->methodName))($requestCallArgs)),
                ($this->assertEquals)($expectedResponse, $response->getPage()->getResponseObject()),
                AST::assign($resources, AST::call(AST::ITERATOR_TO_ARRAY)($response->iterateAllElements())),
                ($this->assertSame)(1, AST::call(AST::COUNT)($resources)),

                // List and map accesses differ.
                $resourcesIsMap
                    ? ($this->assertArrayHasKey)($resourcesKeyIndex, $expectedResponse->instanceCall($method->resourcesGetter)())
                    : null,
                $resourcesIsMap ? ($this->assertArrayHasKey)($resourcesKeyIndex, $resources) : null,
                ($this->assertEquals)($expectedResponse->instanceCall($method->resourcesGetter)()[$resourcesKeyIndex], $resources[$resourcesKeyIndex]),
                AST::assign($actualRequests, $transport->instanceCall(AST::method('popReceivedCalls'))()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($actualRequests)),
                AST::assign($actualFuncCall, AST::index($actualRequests, 0)->instanceCall(AST::method('getFuncCall'))()),
                AST::assign($actualRequestObject, AST::index($actualRequests, 0)->instanceCall(AST::method('getRequestObject'))()),
                ($this->assertSame)("/$rpcHostServiceName/{$method->name}", $actualFuncCall),
                $requestPerField->map(fn ($x) => Vector::new([
                    AST::assign($actualValue, $actualRequestObject->instanceCall($x->field->getter)()),
                    ($this->assertProtobufEquals)($x->var, $actualValue),
                ])),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testSuccessCaseBidiStreaming(MethodDetails $method): PhpMethod
    {
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $transport = AST::var('transport');
        $client = AST::var('client');
        $expectedResponseList = Vector::range(1, 3)->map(fn ($i) => AST::var('expectedResponse' . ($i === 1 ? '' : $i)));
        $expectedResponsesVarList = Vector::range(1, 3)->map(fn ($i) => $prod->perField(
            $method->responseFields->filter(fn ($x) => $x->isInTestResponse)
        ));
        $requestList = Vector::range(1, 3)->map(fn ($i) => AST::var('request' . ($i === 1 ? '' : $i)));
        $requestsVarList = Vector::range(1, 3)->map(fn ($i) => $prod->perFieldRequest($method));
        $bidi = AST::var('bidi');
        $responses = AST::var('responses');
        $response = AST::var('response');
        $expectedResponses = AST::var('expectedResponses');
        $createStreamRequests = AST::var('createStreamRequests');
        $streamFuncCall = AST::var('streamFuncCall');
        $streamRequestObject = AST::var('streamRequestObject');
        $callObjects = AST::var('callObjects');
        $bidiCall = AST::var('bidiCall');
        $writeRequests = AST::var('writeRequests');
        $expectedRequests = AST::var('expectedRequests');
        $rpcHostServiceName = $method->isMixin() ? $method->mixinServiceFullname : $this->serviceDetails->serviceName;
        return AST::method($method->testSuccessMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
                '// Mock response',
                Vector::zip($expectedResponseList, $expectedResponsesVarList, fn ($responseVar, $xs) => Vector::new([
                    $xs->map(fn ($x) => AST::assign($x->var, $x->value)),
                    AST::assign($responseVar, AST::new($this->ctx->type($method->responseType))()),
                    $xs->map(fn ($x) => $responseVar->instancecall($x->field->setter)($x->var)),
                    $transport->addResponse($responseVar),
                ])),
                '// Mock request',
                Vector::zip($requestList, $requestsVarList, fn ($request, $xs) => Vector::new([
                    $xs[0]->map(fn ($x) => $x->initCode),
                    AST::assign($request, AST::new($this->ctx->type($method->requestType))()),
                    $xs[0]->map(fn ($x) => $request->instanceCall($x->field->setter)($x->var)),
                ])),
                AST::assign($bidi, $client->instanceCall(AST::method($method->methodName))()),
                ($this->assertInstanceOf)(AST::access($this->ctx->type(Type::fromName(BidiStream::class)), AST::CLS), $bidi),
                $bidi->write($requestList[0]),
                AST::assign($responses, AST::array([])),
                AST::assign(AST::index($responses, null), $bidi->read()),
                $bidi->writeAll(AST::array($requestList->skip(1)->toArray())),
                AST::foreach($bidi->closeWriteAndReadAll(), $response)(
                    AST::assign(AST::index($responses, null), $response),
                ),
                AST::assign($expectedResponses, AST::array([])),
                $expectedResponseList->map(fn ($x) => AST::assign(AST::index($expectedResponses, null), $x)),
                ($this->assertEquals)($expectedResponses, $responses),
                AST::assign($createStreamRequests, $transport->popReceivedCalls()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($createStreamRequests)),
                AST::assign($streamFuncCall, $createStreamRequests[0]->getFuncCall()),
                AST::assign($streamRequestObject, $createStreamRequests[0]->getRequestObject()),
                ($this->assertSame)("/$rpcHostServiceName/{$method->name}", $streamFuncCall),
                ($this->assertNull)($streamRequestObject),
                AST::assign($callObjects, $transport->popCallObjects()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($callObjects)),
                AST::assign($bidiCall, $callObjects[0]),
                AST::assign($writeRequests, $bidiCall->popReceivedCalls()),
                AST::assign($expectedRequests, AST::array([])),
                $requestList->map(fn ($x) => AST::assign(AST::index($expectedRequests, null), $x)),
                ($this->assertEquals)($expectedRequests, $writeRequests),
                ($this->assertTrue)($transport->isExhausted()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testExceptionalCaseBidiStreaming(MethodDetails $method): PhpMethod
    {
        // TODO: Support resource-names in request args.
        $transport = AST::var('transport');
        $client = AST::var('client');
        $status = AST::var('status');
        $expectedExceptionMessage = AST::var('expectedExceptionMessage');
        $bidi = AST::var('bidi');
        $results = AST::var('results');
        $ex = AST::var('ex');
        return AST::method($method->testExceptionMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                AST::assign($status, AST::new($this->ctx->type(Type::stdClass()))()),
                AST::assign(AST::access($status, AST::property('code')), AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS'))),
                AST::assign(AST::access($status, AST::property('details')), 'internal error'),
                AST::assign($expectedExceptionMessage, AST::call(AST::method('json_encode'))(AST::array([
                    'message' => 'internal error',
                    'code' => AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS')),
                    'status' => 'DATA_LOSS',
                    'details' => AST::array([]),
                ]), AST::constant('JSON_PRETTY_PRINT'))),
                $transport->setStreamingStatus($status),
                ($this->assertTrue)($transport->isExhausted()),
                AST::assign($bidi, $client->instanceCall(AST::method($method->methodName))()),
                AST::assign($results, $bidi->closeWriteAndReadAll()),
                AST::try(
                    AST::call(AST::ITERATOR_TO_ARRAY)($results),
                    '// If the close stream method call did not throw, fail the test',
                    ($this->fail)('Expected an ApiException, but no exception was thrown.'),
                )->catch($this->ctx->type(Type::fromName(ApiException::class)), $ex)(
                    ($this->assertEquals)(AST::access($status, AST::property('code')), $ex->getCode()),
                    ($this->assertEquals)($expectedExceptionMessage, $ex->getMessage()),
                ),
                '// Call popReceivedCalls to ensure the stub is exhausted',
                $transport->popReceivedCalls(),
                ($this->assertTrue)($transport->isExhausted()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testSuccessCaseServerStreaming(MethodDetails $method): PhpMethod
    {
        // TODO: Support resource-names in request args.
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $transport = AST::var('transport');
        $client = AST::var('client');
        $expectedResponseList = Vector::range(1, 3)->map(fn ($i) => AST::var('expectedResponse' . ($i === 1 ? '' : $i)));
        [$requestPerField, $requestCallArgs] = $prod->perFieldRequest($method);
        $serverStream = AST::var('serverStream');
        $responses = AST::var('responses');
        $responsePerFields = Vector::range(1, 3)->map(function ($i) use ($prod, $method) {
            $result = $prod->perField($method->responseFields->filter(fn ($x) => $x->isInTestResponse));
            // TODO(vNext): Remove this craziness; it's only required for monolithic compatibility.
            if ($i === 1) {
                foreach ($method->requiredFields as $f) {
                    $prod->name($f->name);
                    $prod->name($f->name);
                }
            }
            return $result;
        });
        $expectedResponses = AST::var('expectedResponses');
        $actualRequests = AST::var('actualRequests');
        $actualFuncCall = AST::var('actualFuncCall');
        $actualRequestObject = AST::var('actualRequestObject');
        $actualValue = AST::var('actualValue');
        $rpcHostServiceName = $method->isMixin() ? $method->mixinServiceFullname : $this->serviceDetails->serviceName;
        return AST::method($method->testSuccessMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
                '// Mock response',
                $expectedResponseList->map(fn ($x, $i) => Vector::new([
                    $responsePerFields[$i]->map(fn ($y) => AST::assign($y->var, $y->value)),
                    AST::assign($x, AST::new($this->ctx->type($method->responseType))()),
                    $responsePerFields[$i]->map(fn ($y) => $x->instanceCall($y->field->setter)($y->var)),
                    $transport->addResponse($x),
                ])),
                '// Mock request',
                $requestPerField->map(fn ($x) => $x->initCode),
                AST::assign($serverStream, $client->instanceCall(AST::method($method->methodName))($requestCallArgs)),
                ($this->assertInstanceOf)(AST::access($this->ctx->type(Type::fromName(ServerStream::class)), AST::CLS), $serverStream),
                AST::assign($responses, AST::call(AST::ITERATOR_TO_ARRAY)($serverStream->readAll())),
                AST::assign($expectedResponses, AST::array([])),
                $expectedResponseList->map(fn ($x) => AST::assign(AST::index($expectedResponses, null), $x)),
                ($this->assertEquals)($expectedResponses, $responses),
                AST::assign($actualRequests, $transport->popReceivedCalls()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($actualRequests)),
                AST::assign($actualFuncCall, $actualRequests[0]->getFuncCall()),
                AST::assign($actualRequestObject, $actualRequests[0]->getRequestObject()),
                ($this->assertSame)("/$rpcHostServiceName/{$method->name}", $actualFuncCall),
                $requestPerField->map(fn ($x) => Vector::new([
                    AST::assign($actualValue, $actualRequestObject->instanceCall($x->field->getter)()),
                    ($this->assertProtobufEquals)($x->var, $actualValue),
                ])),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testExceptionalCaseServerStreaming(MethodDetails $method): PhpMethod
    {
        $prod = new TestNameValueProducer($method->catalog, $this->ctx);
        $transport = AST::var('transport');
        $client = AST::var('client');
        $status = AST::var('status');
        $expectedExceptionMessage = AST::var('expectedExceptionMessage');
        [$requestPerField, $requestCallArgs] = $prod->perFieldRequest($method);
        $serverStream = AST::var('serverStream');
        $results = AST::var('results');
        $ex = AST::var('ex');
        return AST::method($method->testExceptionMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                AST::assign($status, AST::new($this->ctx->type(Type::stdClass()))()),
                AST::assign(AST::access($status, AST::property('code')), AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS'))),
                AST::assign(AST::access($status, AST::property('details')), 'internal error'),
                AST::assign($expectedExceptionMessage, AST::call(AST::method('json_encode'))(AST::array([
                    'message' => 'internal error',
                    'code' => AST::access($this->ctx->type(Type::fromName(Code::class)), AST::constant('DATA_LOSS')),
                    'status' => 'DATA_LOSS',
                    'details' => AST::array([]),
                ]), AST::constant('JSON_PRETTY_PRINT'))),
                $transport->setStreamingStatus($status),
                ($this->assertTrue)($transport->isExhausted()),
                '// Mock request',
                $requestPerField->map(fn ($x) => $x->initCode),
                AST::assign($serverStream, $client->instanceCall(AST::method($method->methodName))($requestCallArgs)),
                AST::assign($results, $serverStream->readAll()),
                AST::try(
                    AST::call(AST::ITERATOR_TO_ARRAY)($results),
                    '// If the close stream method call did not throw, fail the test',
                    ($this->fail)('Expected an ApiException, but no exception was thrown.'),
                )->catch($this->ctx->type(Type::fromName(ApiException::class)), $ex)(
                    ($this->assertEquals)(AST::access($status, AST::property('code')), $ex->getCode()),
                    ($this->assertEquals)($expectedExceptionMessage, $ex->getMessage()),
                ),
                '// Call popReceivedCalls to ensure the stub is exhausted',
                $transport->popReceivedCalls(),
                ($this->assertTrue)($transport->isExhausted()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }
}
