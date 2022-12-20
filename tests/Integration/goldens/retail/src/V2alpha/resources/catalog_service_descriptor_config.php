<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.CatalogService' => [
            'GetDefaultBranch' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\GetDefaultBranchResponse',
                'headerParams' => [
                    [
                        'keyName' => 'catalog',
                        'fieldAccessors' => [
                            'getCatalog',
                        ],
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
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'SetDefaultBranch' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'catalog',
                        'fieldAccessors' => [
                            'getCatalog',
                        ],
                    ],
                ],
            ],
            'UpdateCatalog' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\Catalog',
                'headerParams' => [
                    [
                        'keyName' => 'catalog.name',
                        'fieldAccessors' => [
                            'getCatalog',
                            'getName',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'branch' => 'projects/{project}/locations/{location}/catalogs/{catalog}/branches/{branch}',
                'catalog' => 'projects/{project}/locations/{location}/catalogs/{catalog}',
                'location' => 'projects/{project}/locations/{location}',
            ],
        ],
    ],
];
