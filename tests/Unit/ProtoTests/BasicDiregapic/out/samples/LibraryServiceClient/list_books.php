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

// [START library-example_generated_LibraryService_ListBooks_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryServiceClient;


/**
 * Lists books in a shelf.
 *
 * @param string $formattedName The name of the shelf whose books we'd like to list.
 */
function list_books_sample(string $formattedName)
{
    $libraryServiceClient = new LibraryServiceClient();
    
    try {
        // Iterate over pages of elements
        $response = $libraryServiceClient->listBooks($formattedName);
        foreach ($response->iteratePages() as $page) {
            /** @var array $element */
            foreach ($page as $element) {
                printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
            }
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
function callSample()
{
    $formattedName = LibraryServiceClient::shelfName('[SHELF]');
    
    list_books_sample($formattedName);
}


// [END library-example_generated_LibraryService_ListBooks_sync]
