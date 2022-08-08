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

// [START library-example_generated_LibraryService_CreateInventory_sync]
use Google\ApiCore\ApiException;
use Testing\BasicDiregapic\InventoryResponse;
use Testing\BasicDiregapic\LibraryServiceClient;

/**
 * Creates an inventory. Tests singleton resources.
 *
 * @param string $formattedParent
 * @param string $asset
 * @param string $parentAsset
 * @param array  $assets
 */
function create_inventory_sample(string $formattedParent, string $asset, string $parentAsset, array $assets)
{
    $libraryServiceClient = new LibraryServiceClient();
    
    try {
        /** @var InventoryResponse $response */
        $response = $libraryServiceClient->createInventory($formattedParent, $asset, $parentAsset, $assets);
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
    $formattedParent = LibraryServiceClient::publisherName('[PROJECT]', '[LOCATION]', '[PUBLISHER]');
    $asset = 'asset';
    $parentAsset = 'parent_asset';
    $assets = [];
    
    create_inventory_sample($formattedParent, $asset, $parentAsset, $assets);
}


// [END library-example_generated_LibraryService_CreateInventory_sync]
