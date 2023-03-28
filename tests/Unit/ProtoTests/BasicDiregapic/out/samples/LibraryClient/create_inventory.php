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

// [START example_generated_Library_CreateInventory_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\CreateInventoryRequest;
use Testing\BasicDiregapic\InventoryResponse;
use Testing\BasicDiregapic\LibraryClient;

/**
 * Creates an inventory. Tests singleton resources.
 *
 * @param string $formattedParent Please see {@see LibraryClient::publisherName()} for help formatting this field.
 * @param string $asset
 * @param string $parentAsset
 * @param string $assetsElement
 */
function create_inventory_sample(
    string $formattedParent,
    string $asset,
    string $parentAsset,
    string $assetsElement
): void {
    // Create a client.
    $libraryClient = new LibraryClient();

    // Prepare the request message.
    $assets = [$assetsElement,];
    $request = (new CreateInventoryRequest())
        ->setParent($formattedParent)
        ->setAsset($asset)
        ->setParentAsset($parentAsset)
        ->setAssets($assets);

    // Call the API and handle any network failures.
    try {
        /** @var InventoryResponse $response */
        $response = $libraryClient->createInventory($request);
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
    $formattedParent = LibraryClient::publisherName('[PROJECT]', '[LOCATION]', '[PUBLISHER]');
    $asset = '[ASSET]';
    $parentAsset = '[PARENT_ASSET]';
    $assetsElement = '[ASSETS]';

    create_inventory_sample($formattedParent, $asset, $parentAsset, $assetsElement);
}
// [END example_generated_Library_CreateInventory_sync]
