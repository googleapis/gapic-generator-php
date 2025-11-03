<?php
/*
 * Copyright 2025 Google LLC
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

// [START retail_v2alpha_generated_ProjectService_EnrollSolution_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Google\Cloud\Retail\V2alpha\EnrollSolutionResponse;
use Google\Cloud\Retail\V2alpha\ProjectServiceClient;
use Google\Cloud\Retail\V2alpha\SolutionType;
use Google\Rpc\Status;

/**
 * The method enrolls a solution of type [Retail
 * Search][google.cloud.retail.v2alpha.SolutionType.SOLUTION_TYPE_SEARCH]
 * into a project.
 *
 * The [Recommendations AI solution
 * type][google.cloud.retail.v2alpha.SolutionType.SOLUTION_TYPE_RECOMMENDATION]
 * is enrolled by default when your project enables Retail API, so you don't
 * need to call the enrollSolution method for recommendations.
 *
 * @param string $formattedProject Full resource name of parent. Format:
 *                                 `projects/{project_number_or_id}`
 *                                 Please see {@see ProjectServiceClient::projectName()} for help formatting this field.
 * @param int    $solution         Solution to enroll.
 */
function enroll_solution_sample(string $formattedProject, int $solution): void
{
    // Create a client.
    $projectServiceClient = new ProjectServiceClient();

    // Call the API and handle any network failures.
    try {
        /** @var OperationResponse $response */
        $response = $projectServiceClient->enrollSolution($formattedProject, $solution);
        $response->pollUntilComplete();

        if ($response->operationSucceeded()) {
            /** @var EnrollSolutionResponse $result */
            $result = $response->getResult();
            printf('Operation successful with response data: %s' . PHP_EOL, $result->serializeToJsonString());
        } else {
            /** @var Status $error */
            $error = $response->getError();
            printf('Operation failed with error data: %s' . PHP_EOL, $error->serializeToJsonString());
        }
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
    $formattedProject = ProjectServiceClient::projectName('[PROJECT]');
    $solution = SolutionType::SOLUTION_TYPE_UNSPECIFIED;

    enroll_solution_sample($formattedProject, $solution);
}
// [END retail_v2alpha_generated_ProjectService_EnrollSolution_sync]
