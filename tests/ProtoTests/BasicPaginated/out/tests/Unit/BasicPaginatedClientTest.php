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

/*
 * GENERATED CODE WARNING
 * This file was automatically generated - do not edit!
 */


namespace Testing\BasicPaginated\Tests\Unit;

use Testing\BasicPaginated\BasicPaginatedClient;
use Google\ApiCore\ApiException;
use Google\ApiCore\BidiStream;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\ServerStream;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\LongRunning\GetOperationRequest;
use Google\Protobuf\Any;
use Google\Protobuf\GPBEmpty;
use Google\Rpc\Code;
use PHPUnit\Framework\TestCase;
use Testing\BasicPaginated\BasicPaginatedGrpcClient;
use Testing\BasicPaginated\Request;
use Testing\BasicPaginated\Response;
use stdClass;

/**
 * @group basicpaginated
 *
 * @group gapic
 */
class BasicPaginatedClientTest extends GeneratedTest
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

    /** @return BasicPaginatedClient */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new BasicPaginatedClient($options);
    }

    /** @test */
    public function methodPaginatedTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $theResultsElement = 'theResultsElement-1546403867';
        $theResults = [
            $theResultsElement,
        ];
        $expectedResponse = new Response();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setTheResults($theResults);
        $transport->addResponse($expectedResponse);
        // Mock request
        $aField = 'aField-1289259108';
        $pageToken = 'pageToken1630607433';
        $response = $client->methodPaginated($aField, $pageToken);
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getTheResults()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/testing.basicpaginated.BasicPaginated/MethodPaginated', $actualFuncCall);
        $actualValue = $actualRequestObject->getAField();
        $this->assertProtobufEquals($aField, $actualValue);
        $actualValue = $actualRequestObject->getPageToken();
        $this->assertProtobufEquals($pageToken, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function methodPaginatedExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
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
        $aField = 'aField-1289259108';
        $pageToken = 'pageToken1630607433';
        try {
            $client->methodPaginated($aField, $pageToken);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage , $ex->getMessage());
        }
// Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }
}
