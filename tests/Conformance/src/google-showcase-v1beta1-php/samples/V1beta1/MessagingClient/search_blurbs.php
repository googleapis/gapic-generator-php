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

// [START localhost:7469_v1beta1_generated_Messaging_SearchBlurbs_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Google\Rpc\Status;
use Google\Showcase\V1beta1\Client\MessagingClient;
use Google\Showcase\V1beta1\SearchBlurbsRequest;
use Google\Showcase\V1beta1\SearchBlurbsResponse;

/**
 * This method searches through all blurbs across all rooms and profiles
 * for blurbs containing to words found in the query. Only posts that
 * contain an exact match of a queried word will be returned.
 *
 * @param string $query The query used to search for blurbs containing to words of this string.
 *                      Only posts that contain an exact match of a queried word will be returned.
 */
function search_blurbs_sample(string $query): void
{
    // Create a client.
    $messagingClient = new MessagingClient();

    // Prepare the request message.
    $request = (new SearchBlurbsRequest())
        ->setQuery($query);

    // Call the API and handle any network failures.
    try {
        /** @var OperationResponse $response */
        $response = $messagingClient->searchBlurbs($request);
        $response->pollUntilComplete();

        if ($response->operationSucceeded()) {
            /** @var SearchBlurbsResponse $result */
            $result = $response->getResult();
            printf('Operation successful with response data: %s' . PHP_EOL, $result->serializeToJsonString());
        } else {
            /** @var Status $error */
            $error = $response->getError();
            printf('Operation failed with error data: %s' . PHP_EOL, $error->serializeToJsonString());
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
    $query = '[QUERY]';

    search_blurbs_sample($query);
}
// [END localhost:7469_v1beta1_generated_Messaging_SearchBlurbs_sync]
