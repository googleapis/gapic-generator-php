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

// [START example_generated_LibraryService_FindRelatedBooks_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\PagedListResponse;
use Testing\BasicDiregapic\Client\LibraryServiceClient;
use Testing\BasicDiregapic\FindRelatedBooksRequest;

/**
 * @param string $namesElement   Please see {@see LibraryServiceClient::bookName()} for help formatting this field.
 * @param string $shelvesElement Please see {@see LibraryServiceClient::shelfName()} for help formatting this field.
 */
function find_related_books_sample(string $namesElement, string $shelvesElement): void
{
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Prepare the request message.
    $names = [$namesElement,];
    $shelves = [$shelvesElement,];
    $request = (new FindRelatedBooksRequest())
        ->setNames($names)
        ->setShelves($shelves);

    // Call the API and handle any network failures.
    try {
        /** @var PagedListResponse $response */
        $response = $libraryServiceClient->findRelatedBooks($request);

        /** @var string $element */
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
    $namesElement = LibraryServiceClient::bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
    $shelvesElement = LibraryServiceClient::shelfName('[SHELF]');

    find_related_books_sample($namesElement, $shelvesElement);
}
// [END example_generated_LibraryService_FindRelatedBooks_sync]
