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

// [START library-example_generated_LibraryService_GetShelf_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryServiceClient;
use Testing\BasicDiregapic\ShelfResponse;

/**
 * Gets a shelf.
 *
 * @param string $formattedName The name of the shelf to retrieve. For help formatting this field, please see
 *                              {@see LibraryServiceClient::shelfName()}.
 * @param string $options       To test 'options' parameter name conflict.
 */
function get_shelf_sample(string $formattedName, string $options): void
{
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Call the API and handle any network failures.
    try {
        /** @var ShelfResponse $response */
        $response = $libraryServiceClient->getShelf($formattedName, $options);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}

/**
 * Helper to execute the sample.
 *
 * TODO(developer): Replace sample parameters before running the code.
 */
function callSample(): void
{
    $formattedName = LibraryServiceClient::shelfName('[SHELF]');
    $options = '[OPTIONS]';

    get_shelf_sample($formattedName, $options);
}
// [END library-example_generated_LibraryService_GetShelf_sync]
