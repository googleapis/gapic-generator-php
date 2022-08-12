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

// [START library-example_generated_LibraryService_CreateShelf_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryServiceClient;
use Testing\BasicDiregapic\ShelfResponse;

/**
 * Creates a shelf, and returns the new Shelf.
 * RPC method comment may include special characters: <>&"`'&#64;.
 *
 * @param string $shelfResponseName The shelf to create.
 */
function create_shelf_sample(string $shelfResponseName)
{
    $libraryServiceClient = new LibraryServiceClient();
    $shelf = (new ShelfResponse())->setName($shelfResponseName);
    
    try {
        /** @var ShelfResponse $response */
        $response = $libraryServiceClient->createShelf($shelf);
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
    
    create_shelf_sample($shelfResponseName);
}


// [END library-example_generated_LibraryService_CreateShelf_sync]
