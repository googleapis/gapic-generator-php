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

// [START localhost:7469_v1beta1_generated_Messaging_SendBlurbs_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\ClientStream;
use Google\Showcase\V1beta1\Client\MessagingClient;
use Google\Showcase\V1beta1\CreateBlurbRequest;
use Google\Showcase\V1beta1\SendBlurbsResponse;

/**
 * This is a stream to create multiple blurbs. If an invalid blurb is
 * requested to be created, the stream will close with an error.
 *
 * @param string $formattedParent The resource name of the chat room or user profile that this blurb will
 *                                be tied to. Please see
 *                                {@see MessagingClient::userName()} for help formatting this field.
 */
function send_blurbs_sample(string $formattedParent): void
{
    // Create a client.
    $messagingClient = new MessagingClient();

    // Prepare the request message.
    $request = (new CreateBlurbRequest())
        ->setParent($formattedParent);

    // Call the API and handle any network failures.
    try {
        /** @var ClientStream $stream */
        $stream = $messagingClient->sendBlurbs();

        /** @var SendBlurbsResponse $response */
        $response = $stream->writeAllAndReadResponse([$request,]);
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
    $formattedParent = MessagingClient::userName('[USER]');

    send_blurbs_sample($formattedParent);
}
// [END localhost:7469_v1beta1_generated_Messaging_SendBlurbs_sync]
