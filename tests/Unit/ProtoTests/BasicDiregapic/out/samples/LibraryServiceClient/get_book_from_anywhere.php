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

// [START example_generated_LibraryService_GetBookFromAnywhere_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\BookFromAnywhereResponse;
use Testing\BasicDiregapic\Client\LibraryServiceClient;
use Testing\BasicDiregapic\GetBookFromAnywhereRequest;

/**
 * Gets a book from a shelf or archive.
 *
 * @param string $formattedName        The name of the book to retrieve. Please see
 *                                     {@see LibraryServiceClient::bookName()} for help formatting this field.
 * @param string $formattedAltBookName An alternate book name, used to test restricting flattened field to a
 *                                     single resource name type in a oneof. Please see
 *                                     {@see LibraryServiceClient::bookName()} for help formatting this field.
 * @param string $formattedPlace       Please see {@see LibraryServiceClient::locationName()} for help formatting this field.
 * @param string $formattedFolder      Please see {@see LibraryServiceClient::folderName()} for help formatting this field.
 */
function get_book_from_anywhere_sample(
    string $formattedName,
    string $formattedAltBookName,
    string $formattedPlace,
    string $formattedFolder
): void {
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Prepare the request message.
    $request = (new GetBookFromAnywhereRequest())
        ->setName($formattedName)
        ->setAltBookName($formattedAltBookName)
        ->setPlace($formattedPlace)
        ->setFolder($formattedFolder);

    // Call the API and handle any network failures.
    try {
        /** @var BookFromAnywhereResponse $response */
        $response = $libraryServiceClient->getBookFromAnywhere($request);
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
// [END example_generated_LibraryService_GetBookFromAnywhere_sync]
