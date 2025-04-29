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

// [START localhost:7469_v1beta1_generated_Compliance_VerifyEnum_sync]
use Google\ApiCore\ApiException;
use Google\Showcase\V1beta1\Client\ComplianceClient;
use Google\Showcase\V1beta1\EnumResponse;

/**
 * This method is used to verify that clients can round-trip enum values, which is particularly important for unknown enum values over REST. VerifyEnum()
 * verifies that its request, which is presumably the response that the client previously got to a GetEnum(), contains the correct data. If so, it responds
 * with the same EnumResponse; otherwise, the RPC errors.
 *
 * This works because the values of enums sent by the server when a known or unknown value is requested will be the same within a single Showcase server run,
 * although they are not guaranteed to be the same across separate Showcase server runs.
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function verify_enum_sample(): void
{
    // Create a client.
    $complianceClient = new ComplianceClient();

    // Prepare the request message.
    $request = new EnumResponse();

    // Call the API and handle any network failures.
    try {
        /** @var EnumResponse $response */
        $response = $complianceClient->verifyEnum($request);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}
// [END localhost:7469_v1beta1_generated_Compliance_VerifyEnum_sync]
