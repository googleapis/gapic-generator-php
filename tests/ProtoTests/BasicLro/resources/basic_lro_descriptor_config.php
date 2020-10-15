<?php

return [
    'interfaces' => [
        'testing.basiclro.BasicLro' => [
            'Method1' => [
                'longRunning' => [
                    'operationReturnType' => '\Testing\BasicLro\LroResponse',
                    'metadataReturnType' => '\Testing\BasicLro\LroMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.0',
                    'maxPollDelayMillis' => '60000',
                    'totalPollTimeoutMillis' => '86400000',
                ],
            ],
        ],
    ],
];
