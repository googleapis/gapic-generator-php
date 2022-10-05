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

require_once __DIR__ . '/../../../vendor/autoload.php';

// [START jobs_v4beta1_generated_ApplicationService_CreateApplication_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Talent\V4beta1\Application;
use Google\Cloud\Talent\V4beta1\ApplicationServiceClient;
use Google\Cloud\Talent\V4beta1\Application\ApplicationStage;
use Google\Protobuf\Timestamp;

/**
 * Creates a new application entity.
 *
 * @param string $formattedParent       Resource name of the profile under which the application is created.
 *
 *                                      The format is
 *                                      "projects/{project_id}/tenants/{tenant_id}/profiles/{profile_id}".
 *                                      For example, "projects/foo/tenants/bar/profiles/baz". Please see
 *                                      {@see ApplicationServiceClient::profileName()} for help formatting this field.
 * @param string $applicationExternalId Client side application identifier, used to uniquely identify the
 *                                      application.
 *
 *                                      The maximum number of allowed characters is 255.
 * @param string $formattedJob          Resource name of the job which the candidate applied for.
 *
 *                                      The format is
 *                                      "projects/{project_id}/tenants/{tenant_id}/jobs/{job_id}". For example,
 *                                      "projects/foo/tenants/bar/jobs/baz". Please see
 *                                      {@see ApplicationServiceClient::jobName()} for help formatting this field.
 * @param int    $applicationStage      What is the most recent stage of the application (that is, new,
 *                                      screen, send cv, hired, finished work)?  This field is intentionally not
 *                                      comprehensive of every possible status, but instead, represents statuses
 *                                      that would be used to indicate to the ML models good / bad matches.
 */
function create_application_sample(
    string $formattedParent,
    string $applicationExternalId,
    string $formattedJob,
    int $applicationStage
): void {
    // Create a client.
    $applicationServiceClient = new ApplicationServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $applicationCreateTime = new Timestamp();
    $application = (new Application())
        ->setExternalId($applicationExternalId)
        ->setJob($applicationJob)
        ->setStage($applicationStage)
        ->setCreateTime($applicationCreateTime);

    // Call the API and handle any network failures.
    try {
        /** @var Application $response */
        $response = $applicationServiceClient->createApplication(
            $formattedParent,
            $application,
            $formattedJob
        );
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}

/**
 * Helper to execute the sample.
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function callSample(): void
{
    $formattedParent = ApplicationServiceClient::profileName('[PROJECT]', '[TENANT]', '[PROFILE]');
    $applicationExternalId = '[EXTERNAL_ID]';
    $formattedJob = ApplicationServiceClient::jobName('[PROJECT]', '[TENANT]', '[JOB]');
    $applicationStage = ApplicationStage::APPLICATION_STAGE_UNSPECIFIED;

    create_application_sample(
        $formattedParent,
        $applicationExternalId,
        $formattedJob,
        $applicationStage
    );
}
// [END jobs_v4beta1_generated_ApplicationService_CreateApplication_sync]
