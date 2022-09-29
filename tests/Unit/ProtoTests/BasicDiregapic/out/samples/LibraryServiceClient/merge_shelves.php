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

// [START library-example_generated_LibraryService_MergeShelves_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryServiceClient;
use Testing\BasicDiregapic\ShelfResponse;

/**
 * Merges two shelves by adding all books from the shelf named
 * `other_shelf_name` to shelf `name`, and deletes
 * `other_shelf_name`. Returns the updated shelf.
 *
 * @param string $formattedName           The name of the shelf we're adding books to. For help formatting this field,
 *                                        please see {@see LibraryServiceClient::shelfName()}.
 * @param string $formattedOtherShelfName The name of the shelf we're removing books from and deleting. For help
 *                                        formatting this field, please see {@see LibraryServiceClient::shelfName()}.
 */
function merge_shelves_sample(string $formattedName, string $formattedOtherShelfName): void
{
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Call the API and handle any network failures.
    try {
        /** @var ShelfResponse $response */
        $response = $libraryServiceClient->mergeShelves($formattedName, $formattedOtherShelfName);
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
    $formattedOtherShelfName = LibraryServiceClient::shelfName('[SHELF]');

    merge_shelves_sample($formattedName, $formattedOtherShelfName);
}
// [END library-example_generated_LibraryService_MergeShelves_sync]
