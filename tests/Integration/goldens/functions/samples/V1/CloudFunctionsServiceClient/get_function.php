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

// [START cloudfunctions_v1_generated_CloudFunctionsService_GetFunction_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Functions\V1\CloudFunction;
use Google\Cloud\Functions\V1\CloudFunctionsServiceClient;

/**
 * Returns a function with the given name from the requested project.
 *
 * @param string $formattedName The name of the function which details should be obtained. For help formatting
 *                              this field, please see {@see CloudFunctionsServiceClient::cloudFunctionName()}.
 */
function get_function_sample(string $formattedName): void
{
    // Create a client.
    $cloudFunctionsServiceClient = new CloudFunctionsServiceClient();

    // Call the API and handle any network failures.
    try {
        /** @var CloudFunction $response */
        $response = $cloudFunctionsServiceClient->getFunction($formattedName);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
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
    $formattedName = CloudFunctionsServiceClient::cloudFunctionName(
        '[PROJECT]',
        '[LOCATION]',
        '[FUNCTION]'
    );

    get_function_sample($formattedName);
}
// [END cloudfunctions_v1_generated_CloudFunctionsService_GetFunction_sync]
