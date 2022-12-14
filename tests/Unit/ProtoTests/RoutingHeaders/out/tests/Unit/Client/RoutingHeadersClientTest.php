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

/*
 * GENERATED CODE WARNING
 * This file was automatically generated - do not edit!
 */

namespace Testing\RoutingHeaders\Tests\Unit\Client;

use Google\ApiCore\ApiException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\Rpc\Code;
use Testing\RoutingHeaders\Client\RoutingHeadersClient;
use Testing\RoutingHeaders\NestedRequest;
use Testing\RoutingHeaders\NestedRequest\Inner1;
use Testing\RoutingHeaders\NestedRequest\Inner1\Inner2;
use Testing\RoutingHeaders\OrderRequest;
use Testing\RoutingHeaders\Response;
use Testing\RoutingHeaders\SimpleRequest;
use stdClass;

/**
 * @group routingheaders
 *
 * @group gapic
 */
class RoutingHeadersClientTest extends GeneratedTest
{
    /** @return TransportInterface */
    private function createTransport($deserialize = null)
    {
        return new MockTransport($deserialize);
    }

    /** @return CredentialsWrapper */
    private function createCredentials()
    {
        return $this->getMockBuilder(CredentialsWrapper::class)->disableOriginalConstructor()->getMock();
    }

    /** @return RoutingHeadersClient */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new RoutingHeadersClient($options);
    }

    /** @test */
    public function deleteMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        $request = new SimpleRequest();
        $response = $gapicClient->deleteMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/DeleteMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function deleteMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        $request = new SimpleRequest();
        try {
            $gapicClient->deleteMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        $request = new SimpleRequest();
        $response = $gapicClient->getMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/GetMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        $request = new SimpleRequest();
        try {
            $gapicClient->getMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getNoPlaceholdersMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        $request = new SimpleRequest();
        $response = $gapicClient->getNoPlaceholdersMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/GetNoPlaceholdersMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getNoPlaceholdersMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        $request = new SimpleRequest();
        try {
            $gapicClient->getNoPlaceholdersMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getNoTemplateMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        $request = new SimpleRequest();
        $response = $gapicClient->getNoTemplateMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/GetNoTemplateMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getNoTemplateMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        $request = new SimpleRequest();
        try {
            $gapicClient->getNoTemplateMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function nestedMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        $response = $gapicClient->nestedMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/NestedMethod', $actualFuncCall);
        $actualValue = $actualRequestObject->getNest1();
        $this->assertProtobufEquals($nest1, $actualValue);
        $actualValue = $actualRequestObject->getAnotherName();
        $this->assertProtobufEquals($anotherName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function nestedMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        try {
            $gapicClient->nestedMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function nestedMultiMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        $response = $gapicClient->nestedMultiMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/NestedMultiMethod', $actualFuncCall);
        $actualValue = $actualRequestObject->getNest1();
        $this->assertProtobufEquals($nest1, $actualValue);
        $actualValue = $actualRequestObject->getAnotherName();
        $this->assertProtobufEquals($anotherName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function nestedMultiMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        try {
            $gapicClient->nestedMultiMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function orderingMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        // Mock request
        $a = 'a97';
        $b = 'b98';
        $d = 'd100';
        $c = 'c99';
        $e = 'e101';
        $request = (new OrderRequest())
            ->setA($a)
            ->setB($b)
            ->setD($d)
            ->setC($c)
            ->setE($e);
        $response = $gapicClient->orderingMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/OrderingMethod', $actualFuncCall);
        $actualValue = $actualRequestObject->getA();
        $this->assertProtobufEquals($a, $actualValue);
        $actualValue = $actualRequestObject->getB();
        $this->assertProtobufEquals($b, $actualValue);
        $actualValue = $actualRequestObject->getD();
        $this->assertProtobufEquals($d, $actualValue);
        $actualValue = $actualRequestObject->getC();
        $this->assertProtobufEquals($c, $actualValue);
        $actualValue = $actualRequestObject->getE();
        $this->assertProtobufEquals($e, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function orderingMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $a = 'a97';
        $b = 'b98';
        $d = 'd100';
        $c = 'c99';
        $e = 'e101';
        $request = (new OrderRequest())
            ->setA($a)
            ->setB($b)
            ->setD($d)
            ->setC($c)
            ->setE($e);
        try {
            $gapicClient->orderingMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function patchMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        $request = new SimpleRequest();
        $response = $gapicClient->patchMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/PatchMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function patchMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        $request = new SimpleRequest();
        try {
            $gapicClient->patchMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function postMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        $request = new SimpleRequest();
        $response = $gapicClient->postMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/PostMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function postMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        $request = new SimpleRequest();
        try {
            $gapicClient->postMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function putMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        $request = new SimpleRequest();
        $response = $gapicClient->putMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/PutMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function putMethodExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        $request = new SimpleRequest();
        try {
            $gapicClient->putMethod($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function routingRuleWithOutParametersTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        $response = $gapicClient->routingRuleWithOutParameters($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/RoutingRuleWithOutParameters', $actualFuncCall);
        $actualValue = $actualRequestObject->getNest1();
        $this->assertProtobufEquals($nest1, $actualValue);
        $actualValue = $actualRequestObject->getAnotherName();
        $this->assertProtobufEquals($anotherName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function routingRuleWithOutParametersExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        try {
            $gapicClient->routingRuleWithOutParameters($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function routingRuleWithParametersTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new Response();
        $transport->addResponse($expectedResponse);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        $response = $gapicClient->routingRuleWithParameters($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.routingheaders.RoutingHeaders/RoutingRuleWithParameters', $actualFuncCall);
        $actualValue = $actualRequestObject->getNest1();
        $this->assertProtobufEquals($nest1, $actualValue);
        $actualValue = $actualRequestObject->getAnotherName();
        $this->assertProtobufEquals($anotherName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function routingRuleWithParametersExceptionTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $nest1 = new Inner1();
        $nest1Nest2 = new Inner2();
        $nest2Name = 'nest2Name1031975557';
        $nest1Nest2->setName($nest2Name);
        $nest1->setNest2($nest1Nest2);
        $anotherName = 'anotherName-642443705';
        $request = (new NestedRequest())
            ->setNest1($nest1)
            ->setAnotherName($anotherName);
        try {
            $gapicClient->routingRuleWithParameters($request);
            // If the $gapicClient method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }
}
