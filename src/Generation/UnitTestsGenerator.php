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
use Google\ApiCore\CredentialsWrapper;
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
use Google\Generator\Collections\Vector;
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
        $this->fail = AST::call(AST::THIS, AST::method('fail'));
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
        return AST::class($this->serviceDetails->unitTestsType, $this->ctx->type(Type::fromName(GeneratedTest::class)))
            ->withMember($this->createTransport())
            ->withMember($this->createCredentials())
            ->withMember($this->createClient())
            ->withMembers($this->serviceDetails->methods->flatMap(fn($x) => Vector::new($this->testCases($x))));
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
                PhpDoc::return($this->ctx->type(Type::fromName(TransportInterface::class)))
            ));
    }

    private function createCredentials(): PhpClassMember
    {
        return AST::method('createCredentials')
            ->withAccess(Access::PRIVATE)
            ->withBody(AST::block(
                AST::return(
                    AST::call(AST::THIS, AST::method('getMockBuilder'))(
                            AST::access($this->ctx->type(Type::fromName(CredentialsWrapper::class)), AST::CLS))
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
        return AST::method('createClient')
            ->withAccess(Access::PRIVATE)
            ->withParams($options)
            ->withBody(AST::block(
                AST::binaryOp($options->var, '+=', AST::array([
                    'credentials' => AST::call(AST::THIS, $this->createCredentials())()
                ])),
                AST::return(AST::new($this->ctx->type($this->serviceDetails->emptyClientType))($options->var))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::return($this->ctx->type($this->serviceDetails->emptyClientType))
            ));
    }

    private function testCases(MethodDetails $method)
    {
        switch ($method->methodType) {
            case MethodDetails::NORMAL:
                yield $this->testSuccessCaseNormal($method);
                yield $this->testExceptionalCaseNormal($method);
                break;
            case MethodDetails::LRO:
                yield $this->testSuccessCaseLro($method);
                yield $this->testExceptionalCaseLro($method);
                break;
            default:
                throw new \Exception("Cannot handle method-type: '{$method->methodType}'");
        }
    }

    private function testSuccessCaseNormal(MethodDetails $method): PhpMethod
    {
        // TODO: Support resource-names in request args.
        // TODO: Support empty-returning RPCs
        $transport = AST::var('transport');
        $client = AST::var('client');
        $expectedResponse = AST::var('expectedResponse');
        $request = AST::var('request');
        $requestVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
        $response = AST::var('response');
        $actualRequests = AST::var('actualRequests');
        $actualFuncCall = AST::var('actualFuncCall');
        $actualRequestObject = AST::var('actualRequestObject');
        $actualValue = AST::var('actualValue');
        return AST::method($method->testSuccessMethodName)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::assign($transport, AST::call(AST::THIS, $this->createTransport())()),
                AST::assign($client, AST::call(AST::THIS, $this->createClient())(AST::array(['transport' => $transport]))),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
                '// Mock response',
                AST::assign($expectedResponse, AST::new($this->ctx->type($method->responseType))()),
                AST::call($transport, AST::method('addResponse'))($expectedResponse),
                '// Mock request',
                Vector::zip($method->requiredFields, $requestVars, fn($f, $v) => AST::assign($v, $f->type->defaultValue())),
                AST::assign($response, $client->instanceCall(AST::method($method->methodName))(...$requestVars)),
                ($this->assertEquals)($expectedResponse, $response),
                AST::assign($actualRequests, $transport->instanceCall(AST::method('popReceivedCalls'))()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($actualRequests)),
                AST::assign($actualFuncCall, AST::index($actualRequests, 0)->instanceCall(AST::method('getFuncCall'))()),
                AST::assign($actualRequestObject, AST::index($actualRequests, 0)->instanceCall(AST::method('getRequestObject'))()),
                ($this->assertSame)("/{$this->serviceDetails->serviceName}/{$method->name}", $actualFuncCall),
                Vector::zip($method->requiredFields, $requestVars, fn($f, $v) => Vector::new([
                    AST::assign($actualValue, $actualRequestObject->instanceCall($f->getter)()),
                    ($this->assertProtobufEquals)($v, $actualValue),
                ])),
                ($this->assertTrue)(AST::call($transport, AST::method('isExhausted'))()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testExceptionalCaseNormal(MethodDetails $method): PhpMethod
    {
        // TODO: Support resource-names in request args.
        // TODO: Support empty-returning RPCs
        $transport = AST::var('transport');
        $client = AST::var('client');
        $status = AST::var('status');
        $expectedExceptionMessage  = AST::var('expectedExceptionMessage ');
        $requestVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
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
                '// Mock request',
                Vector::zip($method->requiredFields, $requestVars, fn($f, $v) => AST::assign($v, $f->type->defaultValue())),
                AST::try(
                    $client->instanceCall(AST::method($method->methodName))(...$requestVars),
                    ($this->fail)('Expected an ApiException, but no exception was thrown.')
                )->catch($this->ctx->type(Type::fromName(ApiException::class)), $ex,
                    ($this->assertEquals)(AST::access($status, AST::property('code')), $ex->instanceCall(AST::method('getCode'))()),
                    ($this->assertEquals)($expectedExceptionMessage, $ex->instanceCall(AST::method('getMessage'))()),
                ),
                // TODO: Fix formatted error (wrong indent) in this comment.
                '// Call popReceivedCalls to ensure the stub is exhausted',
                $transport->instanceCall(AST::method('popReceivedCalls'))(),
                ($this->assertTrue)($transport->instanceCall(AST::method('isExhausted'))()),
            ))
            ->withPhpDoc(PhpDoc::block(PhpDoc::test()));
    }

    private function testSuccessCaseLro(MethodDetails $method): PhpMethod
    {
        // TODO: Support resource-names in request args.
        // TODO: Support empty-returning RPCs
        $lroResponseVars = $method->lroResponseFields->map(fn($x) => AST::var($x->camelName));
        $expectedResponse = AST::var('expectedResponse');
        $anyResponse = AST::var('anyResponse');
        $completeOperation = AST::var('completeOperation');
        $requestVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
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
                Vector::zip($method->lroResponseFields, $lroResponseVars, fn($f, $v) => AST::assign($v, $f->type->defaultValue())),
                AST::assign($expectedResponse, AST::new($this->ctx->type($method->lroResponseType))()),
                Vector::zip($method->lroResponseFields, $lroResponseVars, fn($f, $v) => $expectedResponse->instanceCall($f->setter)($v)),
                AST::assign($anyResponse, AST::new($this->ctx->type(Type::fromName(Any::class)))()),
                $anyResponse->setValue($expectedResponse->serializeToString()),
                AST::assign($completeOperation, AST::new($this->ctx->type(Type::fromName(Operation::class)))()),
                $completeOperation->setName("operations/{$method->testSuccessMethodName}"),
                $completeOperation->setDone(true),
                $completeOperation->setResponse($anyResponse),
                $operationsTransport->addResponse($completeOperation),
                '// Mock request',
                Vector::zip($method->requiredFields, $requestVars, fn($f, $v) => AST::assign($v, $f->type->defaultValue())),
                AST::assign($response, $client->instanceCall(AST::method($method->methodName))(...$requestVars)),
                ($this->assertFalse)($response->isDone()),
                ($this->assertNull)($response->getResult()),
                AST::assign($apiRequests, $transport->popReceivedCalls()),
                ($this->assertSame)(1, AST::call(AST::COUNT)($apiRequests)),
                AST::assign($operationsRequestsEmpty, $operationsTransport->popReceivedCalls()),
                ($this->assertSame)(0, AST::call(AST::COUNT)($operationsRequestsEmpty)),
                AST::assign($actualApiFuncCall, $apiRequests[0]->getFuncCall()),
                AST::assign($actualApiRequestObject, $apiRequests[0]->getRequestObject()),
                ($this->assertSame)("/{$this->serviceDetails->serviceName}/{$method->name}", $actualApiFuncCall),
                Vector::zip($method->requiredFields, $requestVars, fn($f, $v) => Vector::new([
                    AST::assign($actualValue, $actualApiRequestObject->instanceCall($f->getter)()),
                    ($this->assertProtobufEquals)($v, $actualValue),
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
        // TODO: Support resource-names in request args.
        // TODO: Support empty-returning RPCs
        $status = AST::var('status');
        $expectedExceptionMessage = AST::var('expectedExceptionMessage');
        $requestVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
        $response = AST::var('response');
        $expectedOperationsRequestObject = AST::var('expectedOperationsRequestObject');
        $ex = AST::var('ex');
        [$initCode, $operationsTransport, $client, $transport] = $this->lroTestInit($method->testExceptionMethodName);
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
                '// Mock request',
                Vector::zip($method->requiredFields, $requestVars, fn($f, $v) => AST::assign($v, $f->type->defaultValue())),
                AST::assign($response, $client->instanceCall(AST::method($method->methodName))(...$requestVars)),
                ($this->assertFalse)($response->isDone()),
                ($this->assertNull)($response->getResult()),
                AST::assign($expectedOperationsRequestObject, AST::new($this->ctx->type(Type::fromName(GetOperationRequest::class)))()),
                $expectedOperationsRequestObject->setName("operations/{$method->testExceptionMethodName}"),
                AST::try(
                    $response->pollUntilComplete(AST::array([
                        'initialPollDelayMillis' => 1,
                    ])),
                    '// If the pollUntilComplete() method call did not throw, fail the test',
                    ($this->fail)('Expected an ApiException, but no exception was thrown.'),
                )->catch($this->ctx->type(Type::fromName(ApiException::class)), $ex,
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
}
