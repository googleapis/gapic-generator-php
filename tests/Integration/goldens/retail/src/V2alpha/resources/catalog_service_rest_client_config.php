<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.CatalogService' => [
            'ListCatalogs' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*}/catalogs',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'UpdateCatalog' => [
                'method' => 'patch',
                'uriTemplate' => '/v2alpha/{catalog.name=projects/*/locations/*/catalogs/*}',
                'body' => 'catalog',
                'placeholders' => [
                    'catalog.name' => [
                        'getters' => [
                            'getCatalog',
                            'getName',
                        ],
                    ],
                ],
            ],
        ],
        'google.longrunning.Operations' => [
            'GetOperation' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/operations/*}',
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
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*}/operations',
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
