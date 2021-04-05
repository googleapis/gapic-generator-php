<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.UserEventService' => [
            'ImportUserEvents' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\ImportUserEventsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\ImportMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'PurgeUserEvents' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\PurgeUserEventsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\PurgeMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'RejoinUserEvents' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\RejoinUserEventsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\RejoinUserEventsMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
        ],
    ],
];
