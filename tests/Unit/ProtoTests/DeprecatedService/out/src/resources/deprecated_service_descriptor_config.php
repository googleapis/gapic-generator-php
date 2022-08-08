<?php

return [
    'interfaces' => [
        'testing.deprecated_service.DeprecatedService' => [
            'FastFibonacci' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
            ],
            'SlowFibonacci' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
            ],
        ],
    ],
];
