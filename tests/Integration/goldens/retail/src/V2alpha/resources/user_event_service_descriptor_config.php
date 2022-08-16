<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.UserEventService' => [
            'ImportUserEvents' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\ImportUserEventsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\ImportMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'PurgeUserEvents' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\PurgeUserEventsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\PurgeMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'RejoinUserEvents' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\RejoinUserEventsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\RejoinUserEventsMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CollectUserEvent' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Api\HttpBody',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'WriteUserEvent' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\UserEvent',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
        ],
    ],
];
