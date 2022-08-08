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

// [START library-example_generated_LibraryService_UpdateBookIndex_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\LibraryServiceClient;

/**
 * Updates the index of a book.
 *
 * @param string $formattedName The name of the book to update.
 * @param string $indexName     The name of the index for the book
 */
function update_book_index_sample(string $formattedName, string $indexName)
{
    $libraryServiceClient = new LibraryServiceClient();
    $indexMap = [
        'indexMapKey' => $indexMapValue,
    ];
    
    try {
        $libraryServiceClient->updateBookIndex($formattedName, $indexName, $indexMap);
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
    $formattedName = LibraryServiceClient::bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
    $indexName = 'index_name';
    
    update_book_index_sample($formattedName, $indexName);
}


// [END library-example_generated_LibraryService_UpdateBookIndex_sync]
