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

// [START library-example_generated_LibraryService_PrivateListShelves_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\BookResponse;
use Testing\BasicDiregapic\LibraryServiceClient;

/** This method is not exposed in the GAPIC config. It should be generated. */
function private_list_shelves_sample()
{
    $libraryServiceClient = new LibraryServiceClient();
    
    try {
        /** @var BookResponse $response */
        $response = $libraryServiceClient->privateListShelves();
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}


// [END library-example_generated_LibraryService_PrivateListShelves_sync]
