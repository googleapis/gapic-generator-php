<?php

return [
    'interfaces' => [
        'testing.basiclro.BasicLro' => [
            'Method1' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Testing\BasicLro\LroResponse',
                    'metadataReturnType' => '\Testing\BasicLro\LroMetadata',
                    'initialPollDelayMillis' => '20000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '45000',
                    'totalPollTimeoutMillis' => '86400000',
                ],
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
