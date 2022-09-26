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

// [START library-example_generated_LibraryService_SaveBook_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryServiceClient;

/**
 * Test using resource messages as request objects. Only used by PubSub
 * (CreateSubscription) for historical reasons. New APIs should always create
 * a separate message for a request.
 *
 * @param string $name The resource name of the book.
 *                     BookResponse names have the form `bookShelves/{shelf_id}/books/{book_id}`.
 *                     Message field comment may include special characters: <>&"`'&#64;.
 */
function save_book_sample(string $name): void
{
    // Create a client.
    $libraryServiceClient = new LibraryServiceClient();

    // Call the API and handle any network failures.
    try {
        $libraryServiceClient->saveBook($name);
        printf('Call completed successfully.');
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
    $name = '[NAME]';

    save_book_sample($name);
}
// [END library-example_generated_LibraryService_SaveBook_sync]
