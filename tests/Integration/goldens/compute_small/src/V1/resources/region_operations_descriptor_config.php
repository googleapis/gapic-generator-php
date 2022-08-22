<?php

return [
    'interfaces' => [
        'google.cloud.compute.v1.RegionOperations' => [
            'Get' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Compute\V1\Operation',
                'headerParams' => [
                    [
                        'keyName' => 'project',
                        'fieldAccessors' => [
                            'getProject',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                    [
                        'keyName' => 'operation',
                        'fieldAccessors' => [
                            'getOperation',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
