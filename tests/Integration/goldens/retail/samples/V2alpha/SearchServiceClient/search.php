<?php
/*
 * Copyright 2023 Google LLC
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

// [START retail_v2alpha_generated_SearchService_Search_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\PagedListResponse;
use Google\Cloud\Retail\V2alpha\Client\SearchServiceClient;
use Google\Cloud\Retail\V2alpha\SearchRequest;
use Google\Cloud\Retail\V2alpha\SearchResponse\SearchResult;

/**
 * Performs a search.
 *
 * This feature is only available for users who have Retail Search enabled.
 * Please submit a form [here](https://cloud.google.com/contact) to contact
 * cloud sales if you are interested in using Retail Search.
 *
 * @param string $placement The resource name of the search engine placement, such as
 *                          `projects/&#42;/locations/global/catalogs/default_catalog/placements/default_search`.
 *                          This field is used to identify the set of models that will be used to make
 *                          the search.
 *
 *                          We currently support one placement with the following ID:
 *
 *                          * `default_search`.
 * @param string $visitorId A unique identifier for tracking visitors. For example, this
 *                          could be implemented with an HTTP cookie, which should be able to uniquely
 *                          identify a visitor on a single device. This unique identifier should not
 *                          change if the visitor logs in or out of the website.
 *
 *                          The field must be a UTF-8 encoded string with a length limit of 128
 *                          characters. Otherwise, an INVALID_ARGUMENT error is returned.
 */
function search_sample(string $placement, string $visitorId): void
{
    // Create a client.
    $searchServiceClient = new SearchServiceClient();

    // Prepare the request message.
    $request = (new SearchRequest())
        ->setPlacement($placement)
        ->setVisitorId($visitorId);

    // Call the API and handle any network failures.
    try {
        /** @var PagedListResponse $response */
        $response = $searchServiceClient->search($request);

        /** @var SearchResult $element */
        foreach ($response as $element) {
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
    $placement = '[PLACEMENT]';
    $visitorId = '[VISITOR_ID]';

    search_sample($placement, $visitorId);
}
// [END retail_v2alpha_generated_SearchService_Search_sync]
