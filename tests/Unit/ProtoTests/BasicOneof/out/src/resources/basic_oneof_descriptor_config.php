<?php

return [
    'interfaces' => [
        'testing.basiconeof.BasicOneof' => [
            'AMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicOneof\Response',
            ],
        ],
    ],
];
