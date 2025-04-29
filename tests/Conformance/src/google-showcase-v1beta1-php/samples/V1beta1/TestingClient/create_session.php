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

// [START localhost:7469_v1beta1_generated_Testing_CreateSession_sync]
use Google\ApiCore\ApiException;
use Google\Showcase\V1beta1\Client\TestingClient;
use Google\Showcase\V1beta1\CreateSessionRequest;
use Google\Showcase\V1beta1\Session;

/**
 * Creates a new testing session.
 * Adding this comment with special characters for comment formatting tests:
 * 1. (abra->kadabra->alakazam)
 * 2) [Nonsense][]: `pokemon/&#42;/psychic/*`
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function create_session_sample(): void
{
    // Create a client.
    $testingClient = new TestingClient();

    // Prepare the request message.
    $request = new CreateSessionRequest();

    // Call the API and handle any network failures.
    try {
        /** @var Session $response */
        $response = $testingClient->createSession($request);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}
// [END localhost:7469_v1beta1_generated_Testing_CreateSession_sync]
