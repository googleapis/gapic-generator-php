<?php

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
