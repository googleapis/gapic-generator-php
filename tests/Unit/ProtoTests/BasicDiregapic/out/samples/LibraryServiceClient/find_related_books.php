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

// [START library-example_generated_LibraryService_FindRelatedBooks_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\PagedListResponse;
use Testing\BasicDiregapic\LibraryServiceClient;

/**
 *
 * @param string $formattedNamesElement
 * @param string $formattedShelvesElement
 */
function find_related_books_sample(
    string $formattedNamesElement,
    string $formattedShelvesElement
): void {
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $formattedNames = [$formattedNamesElement,];
    $formattedShelves = [$formattedShelvesElement,];

    // Call the API and handle any network failures.
    try {
        /** @var PagedListResponse $response */
        $response = $libraryServiceClient->findRelatedBooks(
            $formattedNames,
            $formattedShelves
        );

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
 * TODO(developer): Replace sample parameters before running the code.
 */
function callSample(): void
{
    $formattedNamesElement = LibraryServiceClient::bookName(
        '[SHELF]',
        '[BOOK_ONE]',
        '[BOOK_TWO]'
    );
    $formattedShelvesElement = LibraryServiceClient::shelfName('[SHELF]');

    find_related_books_sample($formattedNamesElement, $formattedShelvesElement);
}
// [END library-example_generated_LibraryService_FindRelatedBooks_sync]
