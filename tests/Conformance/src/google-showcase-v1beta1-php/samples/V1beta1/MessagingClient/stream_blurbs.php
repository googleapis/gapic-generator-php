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

// [START localhost:7469_v1beta1_generated_Messaging_StreamBlurbs_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\ServerStream;
use Google\Protobuf\Timestamp;
use Google\Showcase\V1beta1\Client\MessagingClient;
use Google\Showcase\V1beta1\StreamBlurbsRequest;
use Google\Showcase\V1beta1\StreamBlurbsResponse;

/**
 * This returns a stream that emits the blurbs that are created for a
 * particular chat room or user profile.
 *
 * @param string $formattedName The resource name of a chat room or user profile whose blurbs to stream. Please see
 *                              {@see MessagingClient::userName()} for help formatting this field.
 */
function stream_blurbs_sample(string $formattedName): void
{
    // Create a client.
    $messagingClient = new MessagingClient();

    // Prepare the request message.
    $expireTime = new Timestamp();
    $request = (new StreamBlurbsRequest())
        ->setName($formattedName)
        ->setExpireTime($expireTime);

    // Call the API and handle any network failures.
    try {
        /** @var ServerStream $stream */
        $stream = $messagingClient->streamBlurbs($request);

        /** @var StreamBlurbsResponse $element */
        foreach ($stream->readAll() as $element) {
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
    $formattedName = MessagingClient::userName('[USER]');

    stream_blurbs_sample($formattedName);
}
// [END localhost:7469_v1beta1_generated_Messaging_StreamBlurbs_sync]
