<?php

return [
    'interfaces' => [
        'google.cloud.compute.v1.RegionOperations' => [
            'Get' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Compute\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProject',
                        ],
                        'keyName' => 'project',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                    [
                        'fieldAccessors' => [
                            'getOperation',
                        ],
                        'keyName' => 'operation',
                    ],
                ],
            ],
        ],
    ],
];
