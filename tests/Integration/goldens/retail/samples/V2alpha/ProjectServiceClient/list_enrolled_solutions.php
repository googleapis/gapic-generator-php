<?php
/*
 * Copyright 2026 Google LLC
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

// [START retail_v2alpha_generated_ProjectService_ListEnrolledSolutions_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Retail\V2alpha\ListEnrolledSolutionsResponse;
use Google\Cloud\Retail\V2alpha\ProjectServiceClient;

/**
 * Lists all the retail API solutions the project has enrolled.
 *
 * @param string $formattedParent Full resource name of parent. Format:
 *                                `projects/{project_number_or_id}`
 *                                Please see {@see ProjectServiceClient::projectName()} for help formatting this field.
 */
function list_enrolled_solutions_sample(string $formattedParent): void
{
    // Create a client.
    $projectServiceClient = new ProjectServiceClient();

    // Call the API and handle any network failures.
    try {
        /** @var ListEnrolledSolutionsResponse $response */
        $response = $projectServiceClient->listEnrolledSolutions($formattedParent);
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
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function callSample(): void
{
    $formattedParent = ProjectServiceClient::projectName('[PROJECT]');

    list_enrolled_solutions_sample($formattedParent);
}
// [END retail_v2alpha_generated_ProjectService_ListEnrolledSolutions_sync]
