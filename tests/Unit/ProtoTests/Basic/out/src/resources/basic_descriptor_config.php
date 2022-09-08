<?php

return [
    'interfaces' => [
        'testing.basic.Basic' => [
            'AMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\Basic\Response',
            ],
            'MethodWithArgs' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\Basic\Response',
            ],
        ],
    ],
];
