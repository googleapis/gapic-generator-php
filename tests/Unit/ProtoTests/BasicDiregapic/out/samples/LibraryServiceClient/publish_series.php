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

// [START library-example_generated_LibraryService_PublishSeries_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryServiceClient;
use Testing\BasicDiregapic\PublishSeriesResponse;
use Testing\BasicDiregapic\SeriesUuidResponse;
use Testing\BasicDiregapic\ShelfResponse;

/**
 * Creates a series of books.
 * Tests PHP required nested fields.
 *
 * @param string $shelfResponseName The shelf in which the series is created.
 * @param string $bookResponseName  The books to publish in the series.
 */
function publish_series_sample(string $shelfResponseName, string $bookResponseName)
{
    $libraryServiceClient = new LibraryServiceClient();
    $shelf = (new ShelfResponse())->setName($shelfResponseName);
    $books = [
        (new BookResponse())->setName($bookResponseName),
    ];
    $seriesUuid = new SeriesUuidResponse();
    
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
 * TODO(developer): Replace sample parameters before running the code.
 */
function callSample()
{
    $shelfResponseName = 'name';
    $bookResponseName = 'name';
    
    publish_series_sample($shelfResponseName, $bookResponseName);
}


// [END library-example_generated_LibraryService_PublishSeries_sync]
