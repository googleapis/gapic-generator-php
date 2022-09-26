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

// [START dataproc_v1_generated_AutoscalingPolicyService_DeleteAutoscalingPolicy_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Dataproc\V1\AutoscalingPolicyServiceClient;

/**
 * Deletes an autoscaling policy. It is an error to delete an autoscaling
 * policy that is in use by one or more clusters.
 *
 * @param string $formattedName The "resource name" of the autoscaling policy, as described
 *                              in https://cloud.google.com/apis/design/resource_names.
 *
 *                              * For `projects.regions.autoscalingPolicies.delete`, the resource name
 *                              of the policy has the following format:
 *                              `projects/{project_id}/regions/{region}/autoscalingPolicies/{policy_id}`
 *
 *                              * For `projects.locations.autoscalingPolicies.delete`, the resource name
 *                              of the policy has the following format:
 *                              `projects/{project_id}/locations/{location}/autoscalingPolicies/{policy_id}`
 */
function delete_autoscaling_policy_sample(string $formattedName): void
{
    // Create a client.
    $autoscalingPolicyServiceClient = new AutoscalingPolicyServiceClient();

    // Call the API and handle any network failures.
    try {
        $autoscalingPolicyServiceClient->deleteAutoscalingPolicy($formattedName);
        printf('Call completed successfully.');
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}

/**
 * Helper to execute the sample.
 *
 * TODO(developer): Replace sample parameters before running the code.
 */
function callSample(): void
{
    $formattedName = AutoscalingPolicyServiceClient::autoscalingPolicyName(
        '[PROJECT]',
        '[LOCATION]',
        '[AUTOSCALING_POLICY]'
    );

    delete_autoscaling_policy_sample($formattedName);
}
// [END dataproc_v1_generated_AutoscalingPolicyService_DeleteAutoscalingPolicy_sync]
