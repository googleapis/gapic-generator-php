<?php

return [
    'interfaces' => [
        'testing.basiclro.BasicLro' => [
            'Method1' => [
                'longRunning' => [
                    'operationReturnType' => '\Testing\BasicLro\LroResponse',
                    'metadataReturnType' => '\Testing\BasicLro\LroMetadata',
                    'initialPollDelayMillis' => '20000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '45000',
                    'totalPollTimeoutMillis' => '86400000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
            ],
            'MethodNonLro1' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicLro\Request',
            ],
            'MethodNonLro2' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicLro\Request',
            ],
        ],
    ],
];
