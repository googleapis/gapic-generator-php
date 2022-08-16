<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.CatalogService' => [
            'GetDefaultBranch' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\GetDefaultBranchResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getCatalog',
                        ],
                        'keyName' => 'catalog',
                    ],
                ],
            ],
            'ListCatalogs' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\ListCatalogsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getCatalogs',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'SetDefaultBranch' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getCatalog',
                        ],
                        'keyName' => 'catalog',
                    ],
                ],
            ],
            'UpdateCatalog' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\Catalog',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getCatalog',
                            'getName',
                        ],
                        'keyName' => 'catalog.name',
                    ],
                ],
            ],
        ],
    ],
];
