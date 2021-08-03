<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.ProductService' => [
            'CreateProduct' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*/branches/*}/products',
                'body' => 'product',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
                'queryParams' => [
                    'product_id',
                ],
            ],
            'DeleteProduct' => [
                'method' => 'delete',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/products/**}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetProduct' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/products/**}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ImportProducts' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*/branches/*}/products:import',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'UpdateProduct' => [
                'method' => 'patch',
                'uriTemplate' => '/v2alpha/{product.name=projects/*/locations/*/catalogs/*/branches/*/products/**}',
                'body' => 'product',
                'placeholders' => [
                    'product.name' => [
                        'getters' => [
                            'getProduct',
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
