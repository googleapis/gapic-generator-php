<?php

/*
 * Copyright 2026 Google LLC
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'product',
                        'fieldAccessors' => [
                            'getProduct',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'product',
                        'fieldAccessors' => [
                            'getProduct',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'product',
                        'fieldAccessors' => [
                            'getProduct',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'product',
                        'fieldAccessors' => [
                            'getProduct',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'inventory.name',
                        'fieldAccessors' => [
                            'getInventory',
                            'getName',
                        ],
                    ],
                ],
            ],
            'CreateProduct' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\Product',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'DeleteProduct' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetProduct' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\Product',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
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
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\ListProductsResponse',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'UpdateProduct' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\Product',
                'headerParams' => [
                    [
                        'keyName' => 'product.name',
                        'fieldAccessors' => [
                            'getProduct',
                            'getName',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'branch' => 'projects/{project}/locations/{location}/catalogs/{catalog}/branches/{branch}',
                'product' => 'projects/{project}/locations/{location}/catalogs/{catalog}/branches/{branch}/products/{product}',
            ],
        ],
    ],
];
