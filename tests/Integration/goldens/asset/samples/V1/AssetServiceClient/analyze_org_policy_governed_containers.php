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

// [START cloudasset_v1_generated_AssetService_AnalyzeOrgPolicyGovernedContainers_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\PagedListResponse;
use Google\Cloud\Asset\V1\AnalyzeOrgPolicyGovernedContainersResponse\GovernedContainer;
use Google\Cloud\Asset\V1\AssetServiceClient;

/**
 * Analyzes organization policies governed containers (projects, folders or
 * organization) under a scope.
 *
 * @param string $scope      The organization to scope the request. Only organization
 *                           policies within the scope will be analyzed. The output containers will
 *                           also be limited to the ones governed by those in-scope organization
 *                           policies.
 *
 *                           * organizations/{ORGANIZATION_NUMBER} (e.g., "organizations/123456")
 * @param string $constraint The name of the constraint to analyze governed containers for.
 *                           The analysis only contains organization policies for the provided
 *                           constraint.
 */
function analyze_org_policy_governed_containers_sample(string $scope, string $constraint): void
{
    // Create a client.
    $assetServiceClient = new AssetServiceClient();

    // Call the API and handle any network failures.
    try {
        /** @var PagedListResponse $response */
        $response = $assetServiceClient->analyzeOrgPolicyGovernedContainers($scope, $constraint);

        /** @var GovernedContainer $element */
        foreach ($response as $element) {
            printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
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
    $scope = '[SCOPE]';
    $constraint = '[CONSTRAINT]';

    analyze_org_policy_governed_containers_sample($scope, $constraint);
}
// [END cloudasset_v1_generated_AssetService_AnalyzeOrgPolicyGovernedContainers_sync]
