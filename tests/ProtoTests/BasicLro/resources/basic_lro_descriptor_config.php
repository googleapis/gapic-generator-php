<?php

return [
    'interfaces' => [
        'testing.basiclro.BasicLro' => [
            'Method1' => [
                'longRunning' => [
                    'operationReturnType' => '\Testing\BasicLro\LroResponse',
                    'metadataReturnType' => '\Testing\BasicLro\LroMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '360000',
                    'totalPollTimeoutMillis' => '7200000',
                ],
            ],
        ],
    ],
];
