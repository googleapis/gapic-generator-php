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

require_once __DIR__ . '../../../vendor/autoload.php';

// [START paginated_generated_BasicPaginated_MethodPaginated_sync]
use Google\ApiCore\ApiException;
use Testing\BasicPaginated\BasicPaginatedClient;


/**
 *
 * @param string $aField
 * @param string $pageToken Docs on this required standard page_token field will be ignored.
 */
function method_paginated_sample(string $aField, string $pageToken)
{
    try {
        $basicPaginatedClient = new BasicPaginatedClient();
        
        $partOfRequestA = [
            new PartOfRequestA(),
        ];
        // Iterate over pages of elements
        $response = $basicPaginatedClient->methodPaginated($aField, $pageToken, $partOfRequestA);
        foreach ($response->iteratePages() as $page) {
            /** @var array $element */
            foreach ($page as $element) {
                printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
            }

        }

    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}

/**
 * Helper to execute the sample.
 *
 * TODO(developer): Replace sample parameters before running the code.
 */
function callSample()
{
    $aField = 'a_field';
    $pageToken = 'page_token';
    
    method_paginated_sample($aField, $pageToken);
}


// [END paginated_generated_BasicPaginated_MethodPaginated_sync]
