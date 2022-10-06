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

// [START jobs_v4beta1_generated_ProfileService_UpdateProfile_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Talent\V4beta1\Profile;
use Google\Cloud\Talent\V4beta1\ProfileServiceClient;

/**
 * Updates the specified profile and returns the updated result.
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function update_profile_sample(): void
{
    // Create a client.
    $profileServiceClient = new ProfileServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $profile = new Profile();

    // Call the API and handle any network failures.
    try {
        /** @var Profile $response */
        $response = $profileServiceClient->updateProfile($profile);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}
// [END jobs_v4beta1_generated_ProfileService_UpdateProfile_sync]
