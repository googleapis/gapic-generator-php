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

// [START library-example_generated_LibraryService_GetBookFromAnywhere_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\BookFromAnywhereResponse;
use Testing\BasicDiregapic\LibraryServiceClient;

/**
 * Gets a book from a shelf or archive.
 *
 * @param string $formattedName        The name of the book to retrieve. For help formatting this field, please see
 *                                     {@see LibraryServiceClient::bookName()}.
 * @param string $formattedAltBookName An alternate book name, used to test restricting flattened field to a single
 *                                     resource name type in a oneof. For help formatting this field, please see {@see
 *                                     LibraryServiceClient::bookName()}.
 * @param string $formattedPlace       For help formatting this field, please see {@see
 *                                     LibraryServiceClient::locationName()}.
 * @param string $formattedFolder      For help formatting this field, please see {@see
 *                                     LibraryServiceClient::folderName()}.
 */
function get_book_from_anywhere_sample(
    string $formattedName,
    string $formattedAltBookName,
    string $formattedPlace,
    string $formattedFolder
): void {
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Call the API and handle any network failures.
    try {
        /** @var BookFromAnywhereResponse $response */
        $response = $libraryServiceClient->getBookFromAnywhere(
            $formattedName,
            $formattedAltBookName,
            $formattedPlace,
            $formattedFolder
        );
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
    $formattedName = LibraryServiceClient::bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
    $formattedAltBookName = LibraryServiceClient::bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
    $formattedPlace = LibraryServiceClient::locationName('[PROJECT]', '[LOCATION]');
    $formattedFolder = LibraryServiceClient::folderName('[FOLDER]');

    get_book_from_anywhere_sample(
        $formattedName,
        $formattedAltBookName,
        $formattedPlace,
        $formattedFolder
    );
}
// [END library-example_generated_LibraryService_GetBookFromAnywhere_sync]
