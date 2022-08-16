<?php

return [
    'interfaces' => [
        'google.iam.v1.IAMPolicy' => [
            'GetIamPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Iam\V1\Policy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
            'SetIamPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Iam\V1\Policy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
            'TestIamPermissions' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Iam\V1\TestIamPermissionsResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
        ],
    ],
];
