<?php

return [
    'interfaces' => [
        'google.cloud.dataproc.v1.JobController' => [
            'SubmitJobAsOperation' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Dataproc\V1\Job',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\JobMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'project_id',
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                ],
            ],
            'CancelJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'keyName' => 'project_id',
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                    [
                        'keyName' => 'job_id',
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                    ],
                ],
            ],
            'DeleteJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'project_id',
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                    [
                        'keyName' => 'job_id',
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                    ],
                ],
            ],
            'GetJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'keyName' => 'project_id',
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                    [
                        'keyName' => 'job_id',
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                    ],
                ],
            ],
            'ListJobs' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\ListJobsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getJobs',
                ],
                'headerParams' => [
                    [
                        'keyName' => 'project_id',
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                ],
            ],
            'SubmitJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'keyName' => 'project_id',
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                ],
            ],
            'UpdateJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'keyName' => 'project_id',
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                    ],
                    [
                        'keyName' => 'region',
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                    ],
                    [
                        'keyName' => 'job_id',
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
