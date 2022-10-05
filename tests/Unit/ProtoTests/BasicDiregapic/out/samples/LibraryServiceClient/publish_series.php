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

// [START example_generated_LibraryService_PublishSeries_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\BookResponse;
use Testing\BasicDiregapic\LibraryServiceClient;
use Testing\BasicDiregapic\PublishSeriesResponse;
use Testing\BasicDiregapic\SeriesUuidResponse;
use Testing\BasicDiregapic\ShelfResponse;

/**
 * Creates a series of books.
 * Tests PHP required nested fields.
 *
 * @param string $shelfName The resource name of the shelf.
 *                          ShelfResponse names have the form `shelves/{shelf}`.
 * @param string $booksName The resource name of the book.
 *                          BookResponse names have the form `bookShelves/{shelf_id}/books/{book_id}`.
 *                          Message field comment may include special characters: <>&"`'&#64;.
 */
function publish_series_sample(string $shelfName, string $booksName): void
{
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $shelf = (new ShelfResponse())
        ->setName($shelfName);
    $bookResponse = (new BookResponse())
        ->setName($booksName);
    $books = [$bookResponse,];
    $seriesUuid = new SeriesUuidResponse();

    // Call the API and handle any network failures.
    try {
        /** @var PublishSeriesResponse $response */
        $response = $libraryServiceClient->publishSeries($shelf, $books, $seriesUuid);
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
 *
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function callSample(): void
{
    $shelfName = '[NAME]';
    $booksName = '[NAME]';

    publish_series_sample($shelfName, $booksName);
}
// [END example_generated_LibraryService_PublishSeries_sync]
