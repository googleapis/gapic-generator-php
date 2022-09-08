<?php

return [
    'interfaces' => [
        'testing.customlro.CustomLroOperations' => [
            'Cancel' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
            ],
            'Delete' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
            ],
            'Get' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\CustomLro\CustomOperationResponse',
            ],
        ],
    ],
];
