<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.ServingConfigService' => [
            'AddControl' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{serving_config=projects/*/locations/*/catalogs/*/servingConfigs/*}:addControl',
                'body' => '*',
                'placeholders' => [
                    'serving_config' => [
                        'getters' => [
                            'getServingConfig',
                        ],
                    ],
                ],
            ],
            'CreateServingConfig' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*}/servingConfigs',
                'body' => 'serving_config',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
                'queryParams' => [
                    'serving_config_id',
                ],
            ],
            'DeleteServingConfig' => [
                'method' => 'delete',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/servingConfigs/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetServingConfig' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/servingConfigs/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListServingConfigs' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*}/servingConfigs',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'RemoveControl' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{serving_config=projects/*/locations/*/catalogs/*/servingConfigs/*}:removeControl',
                'body' => '*',
                'placeholders' => [
                    'serving_config' => [
                        'getters' => [
                            'getServingConfig',
                        ],
                    ],
                ],
            ],
            'UpdateServingConfig' => [
                'method' => 'patch',
                'uriTemplate' => '/v2alpha/{serving_config.name=projects/*/locations/*/catalogs/*/servingConfigs/*}',
                'body' => 'serving_config',
                'placeholders' => [
                    'serving_config.name' => [
                        'getters' => [
                            'getServingConfig',
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
