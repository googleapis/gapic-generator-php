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

// [START localhost:7469_v1beta1_generated_Messaging_Connect_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\BidiStream;
use Google\Showcase\V1beta1\Client\MessagingClient;
use Google\Showcase\V1beta1\ConnectRequest;
use Google\Showcase\V1beta1\StreamBlurbsResponse;

/**
 * This method starts a bidirectional stream that receives all blurbs that
 * are being created after the stream has started and sends requests to create
 * blurbs. If an invalid blurb is requested to be created, the stream will
 * close with an error.
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function connect_sample(): void
{
    // Create a client.
    $messagingClient = new MessagingClient();

    // Prepare the request message.
    $request = new ConnectRequest();

    // Call the API and handle any network failures.
    try {
        /** @var BidiStream $stream */
        $stream = $messagingClient->connect();
        $stream->writeAll([$request,]);

        /** @var StreamBlurbsResponse $element */
        foreach ($stream->closeWriteAndReadAll() as $element) {
            printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
        }
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}
// [END localhost:7469_v1beta1_generated_Messaging_Connect_sync]
