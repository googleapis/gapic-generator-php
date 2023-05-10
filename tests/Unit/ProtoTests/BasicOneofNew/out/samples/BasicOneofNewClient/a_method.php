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

// [START basic_generated_BasicOneofNew_AMethod_sync]
use Google\ApiCore\ApiException;
use Testing\BasicOneofNew\Client\BasicOneofNewClient;
use Testing\BasicOneofNew\Request;
use Testing\BasicOneofNew\Request\Other;
use Testing\BasicOneofNew\Response;

/**
 * Test including method args with required oneofs.
 *
 * @param string $extraDescription Supplemental request description.
 * @param string $otherFirst
 * @param string $requiredOptional
 */
function a_method_sample(
    string $extraDescription,
    string $otherFirst,
    string $requiredOptional
): void {
    // Create a client.
    $basicOneofNewClient = new BasicOneofNewClient();

    // Prepare the request message.
    $other = (new Other())
        ->setFirst($otherFirst);
    $request = (new Request())
        ->setExtraDescription($extraDescription)
        ->setOther($other)
        ->setRequiredOptional($requiredOptional);

    // Call the API and handle any network failures.
    try {
        /** @var Response $response */
        $response = $basicOneofNewClient->aMethod($request);
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
    $extraDescription = '[EXTRA_DESCRIPTION]';
    $otherFirst = '[FIRST]';
    $requiredOptional = '[REQUIRED_OPTIONAL]';

    a_method_sample($extraDescription, $otherFirst, $requiredOptional);
}
// [END basic_generated_BasicOneofNew_AMethod_sync]
