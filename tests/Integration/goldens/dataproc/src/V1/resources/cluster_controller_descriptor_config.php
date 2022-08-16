<?php

return [
    'interfaces' => [
        'google.cloud.dataproc.v1.ClusterController' => [
            'CreateCluster' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Dataproc\V1\Cluster',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\ClusterOperationMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '900000',
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
            'DeleteCluster' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Protobuf\GPBEmpty',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\ClusterOperationMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '900000',
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
                    [
                        'fieldAccessors' => [
                            'getClusterName',
                        ],
                        'keyName' => 'cluster_name',
                    ],
                ],
            ],
            'DiagnoseCluster' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Dataproc\V1\DiagnoseClusterResults',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\ClusterOperationMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '30000',
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
                    [
                        'fieldAccessors' => [
                            'getClusterName',
                        ],
                        'keyName' => 'cluster_name',
                    ],
                ],
            ],
            'StartCluster' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Dataproc\V1\Cluster',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\ClusterOperationMetadata',
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
                    [
                        'fieldAccessors' => [
                            'getClusterName',
                        ],
                        'keyName' => 'cluster_name',
                    ],
                ],
            ],
            'StopCluster' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Dataproc\V1\Cluster',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\ClusterOperationMetadata',
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
                    [
                        'fieldAccessors' => [
                            'getClusterName',
                        ],
                        'keyName' => 'cluster_name',
                    ],
                ],
            ],
            'UpdateCluster' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Dataproc\V1\Cluster',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\ClusterOperationMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '900000',
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
                    [
                        'fieldAccessors' => [
                            'getClusterName',
                        ],
                        'keyName' => 'cluster_name',
                    ],
                ],
            ],
            'GetCluster' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\Cluster',
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
                            'getClusterName',
                        ],
                        'keyName' => 'cluster_name',
                    ],
                ],
            ],
            'ListClusters' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\ListClustersResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getClusters',
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
        ],
    ],
];
