<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.CompletionService' => [
            'CompleteQuery' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{catalog=projects/*/locations/*/catalogs/*}:completeQuery',
                'placeholders' => [
                    'catalog' => [
                        'getters' => [
                            'getCatalog',
                        ],
                    ],
                ],
            ],
            'ImportCompletionData' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*}/completionData:import',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
        ],
        'google.longrunning.Operations' => [
            'GetOperation' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/operations/*}',
                'additionalBindings' => [
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/operations/*}',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/operations/*}',
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
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*}/operations',
                'additionalBindings' => [
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*}/operations',
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
