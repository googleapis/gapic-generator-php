<?php
/*
 * Copyright 2025 Google LLC
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

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.ProductService' => [
            'AddFulfillmentPlaces' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\AddFulfillmentPlacesResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\AddFulfillmentPlacesMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'AddLocalInventories' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\AddLocalInventoriesResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\AddLocalInventoriesMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'ExportProducts' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\ExportProductsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\ExportMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'ImportProducts' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\ImportProductsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\ImportMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'PurgeProducts' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\PurgeProductsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\PurgeProductsMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'RemoveFulfillmentPlaces' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\RemoveFulfillmentPlacesResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\RemoveFulfillmentPlacesMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'RemoveLocalInventories' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\RemoveLocalInventoriesResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\RemoveLocalInventoriesMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'SetInventory' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\SetInventoryResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\SetInventoryMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'ListProducts' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getProducts',
                ],
            ],
        ],
    ],
];
