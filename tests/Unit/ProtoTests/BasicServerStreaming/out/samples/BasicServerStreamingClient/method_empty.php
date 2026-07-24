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

// [START serverstreaming_generated_BasicServerStreaming_MethodEmpty_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\ServerStream;
use Testing\BasicServerStreaming\Client\BasicServerStreamingClient;
use Testing\BasicServerStreaming\EmptyRequest;
use Testing\BasicServerStreaming\Response;

/**
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function method_empty_sample(): void
{
    // Create a client.
    $basicServerStreamingClient = new BasicServerStreamingClient();

    // Prepare the request message.
    $request = new EmptyRequest();

    // Call the API and handle any network failures.
    try {
        /** @var ServerStream $stream */
        $stream = $basicServerStreamingClient->methodEmpty($request);

        /** @var Response $element */
        foreach ($stream->readAll() as $element) {
            printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
        }
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}
// [END serverstreaming_generated_BasicServerStreaming_MethodEmpty_sync]
