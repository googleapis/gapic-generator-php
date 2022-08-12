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

// [START library-example_generated_LibraryService_GetBigNothing_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Testing\BasicDiregapic\LibraryServiceClient;

/**
 * Test long-running operations with empty return type.
 *
 * @param string $formattedName The name of the book to retrieve.
 */
function get_big_nothing_sample(string $formattedName)
{
    $libraryServiceClient = new LibraryServiceClient();
    
    try {
        /** @var OperationResponse $response */
        $response = $libraryServiceClient->getBigNothing($formattedName);
        $response->pollUntilComplete();
        
        if ($response->operationSucceeded()) {
            // operation succeeded and returns no value
        } else {
            /** @var Status $error */
            $error = $response->getError();
            printf('Operation failed with data: %s' . PHP_EOL, $error->serializeToJsonString());
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
    $formattedName = LibraryServiceClient::bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
    
    get_big_nothing_sample($formattedName);
}


// [END library-example_generated_LibraryService_GetBigNothing_sync]
