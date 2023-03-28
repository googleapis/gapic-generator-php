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

// [START example_generated_Library_AddTag_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\AddTagRequest;
use Testing\BasicDiregapic\AddTagResponse;
use Testing\BasicDiregapic\LibraryClient;

/**
 * Adds a tag to the book. This RPC is a mixin.
 *
 * @param string $resource REQUIRED: The resource which the tag is being added to.
 *                         In the form "shelves/{shelf_id}/books/{book_id}".
 * @param string $tag      REQUIRED: The tag to add.
 */
function add_tag_sample(string $resource, string $tag): void
{
    // Create a client.
    $libraryClient = new LibraryClient();

    // Prepare the request message.
    $request = (new AddTagRequest())
        ->setResource($resource)
        ->setTag($tag);

    // Call the API and handle any network failures.
    try {
        /** @var AddTagResponse $response */
        $response = $libraryClient->addTag($request);
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
    $resource = '[RESOURCE]';
    $tag = '[TAG]';

    add_tag_sample($resource, $tag);
}
// [END example_generated_Library_AddTag_sync]
