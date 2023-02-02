<?php
/*
 * Copyright 2023 Google LLC
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

namespace Google\Cloud\Talent\Tests\Unit\V4beta1\Client;

use Google\ApiCore\ApiException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\Cloud\Talent\V4beta1\Client\ProfileServiceClient;
use Google\Cloud\Talent\V4beta1\CreateProfileRequest;
use Google\Cloud\Talent\V4beta1\DeleteProfileRequest;
use Google\Cloud\Talent\V4beta1\GetProfileRequest;
use Google\Cloud\Talent\V4beta1\HistogramQueryResult;
use Google\Cloud\Talent\V4beta1\ListProfilesRequest;
use Google\Cloud\Talent\V4beta1\ListProfilesResponse;
use Google\Cloud\Talent\V4beta1\Profile;
use Google\Cloud\Talent\V4beta1\RequestMetadata;
use Google\Cloud\Talent\V4beta1\SearchProfilesRequest;
use Google\Cloud\Talent\V4beta1\SearchProfilesResponse;
use Google\Cloud\Talent\V4beta1\UpdateProfileRequest;
use Google\Protobuf\GPBEmpty;
use Google\Rpc\Code;
use stdClass;

/**
 * @group talent
 *
 * @group gapic
 */
class ProfileServiceClientTest extends GeneratedTest
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

    /** @return ProfileServiceClient */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new ProfileServiceClient($options);
    }

    /** @test */
    public function createProfileTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $externalId = 'externalId-1153075697';
        $source = 'source-896505829';
        $uri = 'uri116076';
        $groupId = 'groupId506361563';
        $processed = true;
        $keywordSnippet = 'keywordSnippet1325317319';
        $expectedResponse = new Profile();
        $expectedResponse->setName($name);
        $expectedResponse->setExternalId($externalId);
        $expectedResponse->setSource($source);
        $expectedResponse->setUri($uri);
        $expectedResponse->setGroupId($groupId);
        $expectedResponse->setProcessed($processed);
        $expectedResponse->setKeywordSnippet($keywordSnippet);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $gapicClient->tenantName('[PROJECT]', '[TENANT]');
        $profile = new Profile();
        $request = (new CreateProfileRequest())
            ->setParent($formattedParent)
            ->setProfile($profile);
        $response = $gapicClient->createProfile($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ProfileService/CreateProfile', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $actualValue = $actualRequestObject->getProfile();
        $this->assertProtobufEquals($profile, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function createProfileExceptionTest()
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
        $formattedParent = $gapicClient->tenantName('[PROJECT]', '[TENANT]');
        $profile = new Profile();
        $request = (new CreateProfileRequest())
            ->setParent($formattedParent)
            ->setProfile($profile);
        try {
            $gapicClient->createProfile($request);
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
    public function deleteProfileTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new GPBEmpty();
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $request = (new DeleteProfileRequest())
            ->setName($formattedName);
        $gapicClient->deleteProfile($request);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ProfileService/DeleteProfile', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function deleteProfileExceptionTest()
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
        $formattedName = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $request = (new DeleteProfileRequest())
            ->setName($formattedName);
        try {
            $gapicClient->deleteProfile($request);
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
    public function getProfileTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $externalId = 'externalId-1153075697';
        $source = 'source-896505829';
        $uri = 'uri116076';
        $groupId = 'groupId506361563';
        $processed = true;
        $keywordSnippet = 'keywordSnippet1325317319';
        $expectedResponse = new Profile();
        $expectedResponse->setName($name2);
        $expectedResponse->setExternalId($externalId);
        $expectedResponse->setSource($source);
        $expectedResponse->setUri($uri);
        $expectedResponse->setGroupId($groupId);
        $expectedResponse->setProcessed($processed);
        $expectedResponse->setKeywordSnippet($keywordSnippet);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $request = (new GetProfileRequest())
            ->setName($formattedName);
        $response = $gapicClient->getProfile($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ProfileService/GetProfile', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getProfileExceptionTest()
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
        $formattedName = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $request = (new GetProfileRequest())
            ->setName($formattedName);
        try {
            $gapicClient->getProfile($request);
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
    public function listProfilesTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $profilesElement = new Profile();
        $profiles = [
            $profilesElement,
        ];
        $expectedResponse = new ListProfilesResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setProfiles($profiles);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $gapicClient->tenantName('[PROJECT]', '[TENANT]');
        $request = (new ListProfilesRequest())
            ->setParent($formattedParent);
        $response = $gapicClient->listProfiles($request);
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getProfiles()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ProfileService/ListProfiles', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function listProfilesExceptionTest()
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
        $formattedParent = $gapicClient->tenantName('[PROJECT]', '[TENANT]');
        $request = (new ListProfilesRequest())
            ->setParent($formattedParent);
        try {
            $gapicClient->listProfiles($request);
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
    public function searchProfilesTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $estimatedTotalSize = 1882144769;
        $nextPageToken = '';
        $resultSetId2 = 'resultSetId2-1530601043';
        $histogramQueryResultsElement = new HistogramQueryResult();
        $histogramQueryResults = [
            $histogramQueryResultsElement,
        ];
        $expectedResponse = new SearchProfilesResponse();
        $expectedResponse->setEstimatedTotalSize($estimatedTotalSize);
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setResultSetId($resultSetId2);
        $expectedResponse->setHistogramQueryResults($histogramQueryResults);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $gapicClient->tenantName('[PROJECT]', '[TENANT]');
        $requestMetadata = new RequestMetadata();
        $request = (new SearchProfilesRequest())
            ->setParent($formattedParent)
            ->setRequestMetadata($requestMetadata);
        $response = $gapicClient->searchProfiles($request);
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getHistogramQueryResults()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ProfileService/SearchProfiles', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $actualValue = $actualRequestObject->getRequestMetadata();
        $this->assertProtobufEquals($requestMetadata, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function searchProfilesExceptionTest()
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
        $formattedParent = $gapicClient->tenantName('[PROJECT]', '[TENANT]');
        $requestMetadata = new RequestMetadata();
        $request = (new SearchProfilesRequest())
            ->setParent($formattedParent)
            ->setRequestMetadata($requestMetadata);
        try {
            $gapicClient->searchProfiles($request);
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
    public function updateProfileTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $externalId = 'externalId-1153075697';
        $source = 'source-896505829';
        $uri = 'uri116076';
        $groupId = 'groupId506361563';
        $processed = true;
        $keywordSnippet = 'keywordSnippet1325317319';
        $expectedResponse = new Profile();
        $expectedResponse->setName($name);
        $expectedResponse->setExternalId($externalId);
        $expectedResponse->setSource($source);
        $expectedResponse->setUri($uri);
        $expectedResponse->setGroupId($groupId);
        $expectedResponse->setProcessed($processed);
        $expectedResponse->setKeywordSnippet($keywordSnippet);
        $transport->addResponse($expectedResponse);
        // Mock request
        $profile = new Profile();
        $request = (new UpdateProfileRequest())
            ->setProfile($profile);
        $response = $gapicClient->updateProfile($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ProfileService/UpdateProfile', $actualFuncCall);
        $actualValue = $actualRequestObject->getProfile();
        $this->assertProtobufEquals($profile, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function updateProfileExceptionTest()
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
        $profile = new Profile();
        $request = (new UpdateProfileRequest())
            ->setProfile($profile);
        try {
            $gapicClient->updateProfile($request);
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
    public function createProfileAsyncTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $externalId = 'externalId-1153075697';
        $source = 'source-896505829';
        $uri = 'uri116076';
        $groupId = 'groupId506361563';
        $processed = true;
        $keywordSnippet = 'keywordSnippet1325317319';
        $expectedResponse = new Profile();
        $expectedResponse->setName($name);
        $expectedResponse->setExternalId($externalId);
        $expectedResponse->setSource($source);
        $expectedResponse->setUri($uri);
        $expectedResponse->setGroupId($groupId);
        $expectedResponse->setProcessed($processed);
        $expectedResponse->setKeywordSnippet($keywordSnippet);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $gapicClient->tenantName('[PROJECT]', '[TENANT]');
        $profile = new Profile();
        $request = (new CreateProfileRequest())
            ->setParent($formattedParent)
            ->setProfile($profile);
        $response = $gapicClient->createProfileAsync($request)->wait();
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ProfileService/CreateProfile', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $actualValue = $actualRequestObject->getProfile();
        $this->assertProtobufEquals($profile, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }
}
