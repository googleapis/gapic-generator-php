<?php

return [
    'interfaces' => [
        'google.cloud.redis.v1.CloudRedis' => [
            'CreateInstance' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Redis\V1\Instance',
                    'metadataReturnType' => '\Google\Cloud\Redis\V1\OperationMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '360000',
                    'totalPollTimeoutMillis' => '7200000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'DeleteInstance' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Protobuf\GPBEmpty',
                    'metadataReturnType' => '\Google\Cloud\Redis\V1\OperationMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '360000',
                    'totalPollTimeoutMillis' => '1200000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ExportInstance' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Redis\V1\Instance',
                    'metadataReturnType' => '\Google\Cloud\Redis\V1\OperationMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '360000',
                    'totalPollTimeoutMillis' => '18000000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'FailoverInstance' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Redis\V1\Instance',
                    'metadataReturnType' => '\Google\Cloud\Redis\V1\OperationMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '360000',
                    'totalPollTimeoutMillis' => '1200000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ImportInstance' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Redis\V1\Instance',
                    'metadataReturnType' => '\Google\Cloud\Redis\V1\OperationMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '360000',
                    'totalPollTimeoutMillis' => '18000000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'UpdateInstance' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Redis\V1\Instance',
                    'metadataReturnType' => '\Google\Cloud\Redis\V1\OperationMetadata',
                    'initialPollDelayMillis' => '60000',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '360000',
                    'totalPollTimeoutMillis' => '7200000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'instance.name',
                        'fieldAccessors' => [
                            'getInstance',
                            'getName',
                        ],
                    ],
                ],
            ],
            'UpgradeInstance' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Redis\V1\Instance',
                    'metadataReturnType' => '\Google\Cloud\Redis\V1\OperationMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetInstance' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Redis\V1\Instance',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListInstances' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Redis\V1\ListInstancesResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getInstances',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'instance' => 'projects/{project}/locations/{location}/instances/{instance}',
                'location' => 'projects/{project}/locations/{location}',
            ],
        ],
    ],
];
