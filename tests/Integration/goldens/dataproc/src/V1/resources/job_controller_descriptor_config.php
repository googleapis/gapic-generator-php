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
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                ],
            ],
            'CancelJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                    [
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                        'keyName' => 'job_id',
                    ],
                ],
            ],
            'DeleteJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                    [
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                        'keyName' => 'job_id',
                    ],
                ],
            ],
            'GetJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                    [
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                        'keyName' => 'job_id',
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
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                ],
            ],
            'SubmitJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                ],
            ],
            'UpdateJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Job',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                    [
                        'fieldAccessors' => [
                            'getJobId',
                        ],
                        'keyName' => 'job_id',
                    ],
                ],
            ],
        ],
    ],
];
