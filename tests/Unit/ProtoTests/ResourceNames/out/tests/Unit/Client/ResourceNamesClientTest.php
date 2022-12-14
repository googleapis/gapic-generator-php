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

namespace Testing\ResourceNames\Tests\Unit\Client;

use Google\ApiCore\ApiException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\Rpc\Code;
use Testing\ResourceNames\Client\ResourceNamesClient;
use Testing\ResourceNames\FileLevelChildTypeRefRequest;
use Testing\ResourceNames\FileLevelTypeRefRequest;
use Testing\ResourceNames\MultiPatternRequest;
use Testing\ResourceNames\PlaceholderResponse;
use Testing\ResourceNames\SinglePatternRequest;
use Testing\ResourceNames\WildcardChildReferenceRequest;
use Testing\ResourceNames\WildcardMultiPatternRequest;
use Testing\ResourceNames\WildcardPatternRequest;
use Testing\ResourceNames\WildcardReferenceRequest;
use stdClass;

/**
 * @group resourcenames
 *
 * @group gapic
 */
class ResourceNamesClientTest extends GeneratedTest
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

    /** @return ResourceNamesClient */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new ResourceNamesClient($options);
    }

    /** @test */
    public function fileLevelChildTypeRefMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedReqFolderName = $gapicClient->folderName('[FOLDER_ID]');
        $formattedReqFolderMultiName = $gapicClient->folder1Name('[FOLDER1_ID]');
        $formattedReqFolderMultiNameHistory = $gapicClient->folder1Name('[FOLDER1_ID]');
        $formattedReqOrderTest1 = $gapicClient->order2Name('[ORDER2_ID]');
        $formattedReqOrderTest2 = $gapicClient->order2Name('[ORDER2_ID]');
        $request = (new FileLevelChildTypeRefRequest())
            ->setReqFolderName($formattedReqFolderName)
            ->setReqFolderMultiName($formattedReqFolderMultiName)
            ->setReqFolderMultiNameHistory($formattedReqFolderMultiNameHistory)
            ->setReqOrderTest1($formattedReqOrderTest1)
            ->setReqOrderTest2($formattedReqOrderTest2);
        $response = $gapicClient->fileLevelChildTypeRefMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/FileLevelChildTypeRefMethod', $actualFuncCall);
        $actualValue = $actualRequestObject->getReqFolderName();
        $this->assertProtobufEquals($formattedReqFolderName, $actualValue);
        $actualValue = $actualRequestObject->getReqFolderMultiName();
        $this->assertProtobufEquals($formattedReqFolderMultiName, $actualValue);
        $actualValue = $actualRequestObject->getReqFolderMultiNameHistory();
        $this->assertProtobufEquals($formattedReqFolderMultiNameHistory, $actualValue);
        $actualValue = $actualRequestObject->getReqOrderTest1();
        $this->assertProtobufEquals($formattedReqOrderTest1, $actualValue);
        $actualValue = $actualRequestObject->getReqOrderTest2();
        $this->assertProtobufEquals($formattedReqOrderTest2, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function fileLevelChildTypeRefMethodExceptionTest()
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
        $formattedReqFolderName = $gapicClient->folderName('[FOLDER_ID]');
        $formattedReqFolderMultiName = $gapicClient->folder1Name('[FOLDER1_ID]');
        $formattedReqFolderMultiNameHistory = $gapicClient->folder1Name('[FOLDER1_ID]');
        $formattedReqOrderTest1 = $gapicClient->order2Name('[ORDER2_ID]');
        $formattedReqOrderTest2 = $gapicClient->order2Name('[ORDER2_ID]');
        $request = (new FileLevelChildTypeRefRequest())
            ->setReqFolderName($formattedReqFolderName)
            ->setReqFolderMultiName($formattedReqFolderMultiName)
            ->setReqFolderMultiNameHistory($formattedReqFolderMultiNameHistory)
            ->setReqOrderTest1($formattedReqOrderTest1)
            ->setReqOrderTest2($formattedReqOrderTest2);
        try {
            $gapicClient->fileLevelChildTypeRefMethod($request);
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
    public function fileLevelTypeRefMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        $request = new FileLevelTypeRefRequest();
        $response = $gapicClient->fileLevelTypeRefMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/FileLevelTypeRefMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function fileLevelTypeRefMethodExceptionTest()
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
        $request = new FileLevelTypeRefRequest();
        try {
            $gapicClient->fileLevelTypeRefMethod($request);
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
    public function multiPatternMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        $request = new MultiPatternRequest();
        $response = $gapicClient->multiPatternMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/MultiPatternMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function multiPatternMethodExceptionTest()
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
        $request = new MultiPatternRequest();
        try {
            $gapicClient->multiPatternMethod($request);
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
    public function singlePatternMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        $request = new SinglePatternRequest();
        $response = $gapicClient->singlePatternMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/SinglePatternMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function singlePatternMethodExceptionTest()
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
        $request = new SinglePatternRequest();
        try {
            $gapicClient->singlePatternMethod($request);
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
    public function wildcardChildReferenceMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        $request = new WildcardChildReferenceRequest();
        $response = $gapicClient->wildcardChildReferenceMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/WildcardChildReferenceMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function wildcardChildReferenceMethodExceptionTest()
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
        $request = new WildcardChildReferenceRequest();
        try {
            $gapicClient->wildcardChildReferenceMethod($request);
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
    public function wildcardMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        $request = new WildcardPatternRequest();
        $response = $gapicClient->wildcardMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/WildcardMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function wildcardMethodExceptionTest()
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
        $request = new WildcardPatternRequest();
        try {
            $gapicClient->wildcardMethod($request);
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
    public function wildcardMultiMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        $request = new WildcardMultiPatternRequest();
        $response = $gapicClient->wildcardMultiMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/WildcardMultiMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function wildcardMultiMethodExceptionTest()
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
        $request = new WildcardMultiPatternRequest();
        try {
            $gapicClient->wildcardMultiMethod($request);
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
    public function wildcardReferenceMethodTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PlaceholderResponse();
        $transport->addResponse($expectedResponse);
        $request = new WildcardReferenceRequest();
        $response = $gapicClient->wildcardReferenceMethod($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.resourcenames.ResourceNames/WildcardReferenceMethod', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function wildcardReferenceMethodExceptionTest()
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
        $request = new WildcardReferenceRequest();
        try {
            $gapicClient->wildcardReferenceMethod($request);
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
