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

// [START example_generated_Library_CreateShelf_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryClient;
use Testing\BasicDiregapic\ShelfResponse;

/**
 * Creates a shelf, and returns the new Shelf.
 * RPC method comment may include special characters: <>&"`'&#64;.
 *
 * @param string $shelfName The resource name of the shelf.
 *                          ShelfResponse names have the form `shelves/{shelf}`.
 */
function create_shelf_sample(string $shelfName): void
{
    // Create a client.
    $libraryClient = new LibraryClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $shelf = (new ShelfResponse())
        ->setName($shelfName);

    // Call the API and handle any network failures.
    try {
        /** @var ShelfResponse $response */
        $response = $libraryClient->createShelf($shelf);
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
    $shelfName = '[NAME]';

    create_shelf_sample($shelfName);
}
// [END example_generated_Library_CreateShelf_sync]
