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
use Google\Cloud\Talent\V4beta1\Application;
use Google\Cloud\Talent\V4beta1\Application\ApplicationStage;
use Google\Cloud\Talent\V4beta1\Client\ApplicationServiceClient;
use Google\Cloud\Talent\V4beta1\CreateApplicationRequest;
use Google\Cloud\Talent\V4beta1\DeleteApplicationRequest;
use Google\Cloud\Talent\V4beta1\GetApplicationRequest;
use Google\Cloud\Talent\V4beta1\ListApplicationsRequest;
use Google\Cloud\Talent\V4beta1\ListApplicationsResponse;
use Google\Cloud\Talent\V4beta1\UpdateApplicationRequest;
use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Timestamp;
use Google\Rpc\Code;
use stdClass;

/**
 * @group talent
 *
 * @group gapic
 */
class ApplicationServiceClientTest extends GeneratedTest
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

    /** @return ApplicationServiceClient */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new ApplicationServiceClient($options);
    }

    /** @test */
    public function createApplicationTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $externalId = 'externalId-1153075697';
        $profile = 'profile-309425751';
        $job = 'job105405';
        $company = 'company950484093';
        $outcomeNotes = 'outcomeNotes-355961964';
        $jobTitleSnippet = 'jobTitleSnippet-1100512972';
        $expectedResponse = new Application();
        $expectedResponse->setName($name);
        $expectedResponse->setExternalId($externalId);
        $expectedResponse->setProfile($profile);
        $expectedResponse->setJob($job);
        $expectedResponse->setCompany($company);
        $expectedResponse->setOutcomeNotes($outcomeNotes);
        $expectedResponse->setJobTitleSnippet($jobTitleSnippet);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $application = new Application();
        $applicationExternalId = 'applicationExternalId-266656842';
        $application->setExternalId($applicationExternalId);
        $applicationJob = $gapicClient->jobName('[PROJECT]', '[TENANT]', '[JOB]');
        $application->setJob($applicationJob);
        $applicationStage = ApplicationStage::APPLICATION_STAGE_UNSPECIFIED;
        $application->setStage($applicationStage);
        $applicationCreateTime = new Timestamp();
        $application->setCreateTime($applicationCreateTime);
        $request = (new CreateApplicationRequest())
            ->setParent($formattedParent)
            ->setApplication($application);
        $response = $gapicClient->createApplication($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ApplicationService/CreateApplication', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $actualValue = $actualRequestObject->getApplication();
        $this->assertProtobufEquals($application, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function createApplicationExceptionTest()
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
        $formattedParent = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $application = new Application();
        $applicationExternalId = 'applicationExternalId-266656842';
        $application->setExternalId($applicationExternalId);
        $applicationJob = $gapicClient->jobName('[PROJECT]', '[TENANT]', '[JOB]');
        $application->setJob($applicationJob);
        $applicationStage = ApplicationStage::APPLICATION_STAGE_UNSPECIFIED;
        $application->setStage($applicationStage);
        $applicationCreateTime = new Timestamp();
        $application->setCreateTime($applicationCreateTime);
        $request = (new CreateApplicationRequest())
            ->setParent($formattedParent)
            ->setApplication($application);
        try {
            $gapicClient->createApplication($request);
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
    public function deleteApplicationTest()
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
        $formattedName = $gapicClient->applicationName('[PROJECT]', '[TENANT]', '[PROFILE]', '[APPLICATION]');
        $request = (new DeleteApplicationRequest())
            ->setName($formattedName);
        $gapicClient->deleteApplication($request);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ApplicationService/DeleteApplication', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function deleteApplicationExceptionTest()
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
        $formattedName = $gapicClient->applicationName('[PROJECT]', '[TENANT]', '[PROFILE]', '[APPLICATION]');
        $request = (new DeleteApplicationRequest())
            ->setName($formattedName);
        try {
            $gapicClient->deleteApplication($request);
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
    public function getApplicationTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $externalId = 'externalId-1153075697';
        $profile = 'profile-309425751';
        $job = 'job105405';
        $company = 'company950484093';
        $outcomeNotes = 'outcomeNotes-355961964';
        $jobTitleSnippet = 'jobTitleSnippet-1100512972';
        $expectedResponse = new Application();
        $expectedResponse->setName($name2);
        $expectedResponse->setExternalId($externalId);
        $expectedResponse->setProfile($profile);
        $expectedResponse->setJob($job);
        $expectedResponse->setCompany($company);
        $expectedResponse->setOutcomeNotes($outcomeNotes);
        $expectedResponse->setJobTitleSnippet($jobTitleSnippet);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $gapicClient->applicationName('[PROJECT]', '[TENANT]', '[PROFILE]', '[APPLICATION]');
        $request = (new GetApplicationRequest())
            ->setName($formattedName);
        $response = $gapicClient->getApplication($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ApplicationService/GetApplication', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function getApplicationExceptionTest()
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
        $formattedName = $gapicClient->applicationName('[PROJECT]', '[TENANT]', '[PROFILE]', '[APPLICATION]');
        $request = (new GetApplicationRequest())
            ->setName($formattedName);
        try {
            $gapicClient->getApplication($request);
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
    public function listApplicationsTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $applicationsElement = new Application();
        $applications = [
            $applicationsElement,
        ];
        $expectedResponse = new ListApplicationsResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setApplications($applications);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $request = (new ListApplicationsRequest())
            ->setParent($formattedParent);
        $response = $gapicClient->listApplications($request);
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getApplications()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ApplicationService/ListApplications', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function listApplicationsExceptionTest()
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
        $formattedParent = $gapicClient->profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
        $request = (new ListApplicationsRequest())
            ->setParent($formattedParent);
        try {
            $gapicClient->listApplications($request);
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
    public function updateApplicationTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $externalId = 'externalId-1153075697';
        $profile = 'profile-309425751';
        $job = 'job105405';
        $company = 'company950484093';
        $outcomeNotes = 'outcomeNotes-355961964';
        $jobTitleSnippet = 'jobTitleSnippet-1100512972';
        $expectedResponse = new Application();
        $expectedResponse->setName($name);
        $expectedResponse->setExternalId($externalId);
        $expectedResponse->setProfile($profile);
        $expectedResponse->setJob($job);
        $expectedResponse->setCompany($company);
        $expectedResponse->setOutcomeNotes($outcomeNotes);
        $expectedResponse->setJobTitleSnippet($jobTitleSnippet);
        $transport->addResponse($expectedResponse);
        // Mock request
        $application = new Application();
        $applicationExternalId = 'applicationExternalId-266656842';
        $application->setExternalId($applicationExternalId);
        $applicationJob = $gapicClient->jobName('[PROJECT]', '[TENANT]', '[JOB]');
        $application->setJob($applicationJob);
        $applicationStage = ApplicationStage::APPLICATION_STAGE_UNSPECIFIED;
        $application->setStage($applicationStage);
        $applicationCreateTime = new Timestamp();
        $application->setCreateTime($applicationCreateTime);
        $request = (new UpdateApplicationRequest())
            ->setApplication($application);
        $response = $gapicClient->updateApplication($request);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.talent.v4beta1.ApplicationService/UpdateApplication', $actualFuncCall);
        $actualValue = $actualRequestObject->getApplication();
        $this->assertProtobufEquals($application, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /** @test */
    public function updateApplicationExceptionTest()
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
        $application = new Application();
        $applicationExternalId = 'applicationExternalId-266656842';
        $application->setExternalId($applicationExternalId);
        $applicationJob = $gapicClient->jobName('[PROJECT]', '[TENANT]', '[JOB]');
        $application->setJob($applicationJob);
        $applicationStage = ApplicationStage::APPLICATION_STAGE_UNSPECIFIED;
        $application->setStage($applicationStage);
        $applicationCreateTime = new Timestamp();
        $application->setCreateTime($applicationCreateTime);
        $request = (new UpdateApplicationRequest())
            ->setApplication($application);
        try {
            $gapicClient->updateApplication($request);
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
