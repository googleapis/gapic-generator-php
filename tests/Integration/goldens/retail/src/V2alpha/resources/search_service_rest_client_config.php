<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.SearchService' => [
            'Search' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{placement=projects/*/locations/*/catalogs/*/placements/*}:search',
                'body' => '*',
                'additionalBindings' => [
                    [
                        'method' => 'post',
                        'uriTemplate' => '/v2alpha/{placement=projects/*/locations/*/catalogs/*/servingConfigs/*}:search',
                        'body' => '*',
                    ],
                ],
                'placeholders' => [
                    'placement' => [
                        'getters' => [
                            'getPlacement',
                        ],
                    ],
                ],
            ],
        ],
        'google.longrunning.Operations' => [
            'GetOperation' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/operations/*}',
                'additionalBindings' => [
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/places/*/operations/*}',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/operations/*}',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/operations/*}',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/operations/*}',
                    ],
                ],
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListOperations' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*}/operations',
                'additionalBindings' => [
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*}/operations',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*}/operations',
                    ],
                ],
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
