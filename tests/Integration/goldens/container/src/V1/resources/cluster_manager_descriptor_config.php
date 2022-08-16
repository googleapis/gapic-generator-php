<?php

return [
    'interfaces' => [
        'google.container.v1.ClusterManager' => [
            'CancelOperation' => [
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
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getOperationId',
                        ],
                        'keyName' => 'operation_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'CompleteIPRotation' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'CreateCluster' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CreateNodePool' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'DeleteCluster' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'DeleteNodePool' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNodePoolId',
                        ],
                        'keyName' => 'node_pool_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetCluster' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Cluster',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetJSONWebKeys' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\GetJSONWebKeysResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'GetNodePool' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\NodePool',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNodePoolId',
                        ],
                        'keyName' => 'node_pool_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetOperation' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getOperationId',
                        ],
                        'keyName' => 'operation_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetServerConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\ServerConfig',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'ListClusters' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\ListClustersResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'ListNodePools' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\ListNodePoolsResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'ListOperations' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\ListOperationsResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'ListUsableSubnetworks' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Container\V1\ListUsableSubnetworksResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getSubnetworks',
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
            'RollbackNodePoolUpgrade' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNodePoolId',
                        ],
                        'keyName' => 'node_pool_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetAddonsConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetLabels' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetLegacyAbac' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetLocations' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetLoggingService' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetMaintenancePolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetMasterAuth' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetMonitoringService' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetNetworkPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetNodePoolAutoscaling' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNodePoolId',
                        ],
                        'keyName' => 'node_pool_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetNodePoolManagement' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNodePoolId',
                        ],
                        'keyName' => 'node_pool_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetNodePoolSize' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNodePoolId',
                        ],
                        'keyName' => 'node_pool_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'StartIPRotation' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'UpdateCluster' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'UpdateMaster' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'UpdateNodePool' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Container\V1\Operation',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProjectId',
                        ],
                        'keyName' => 'project_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getZone',
                        ],
                        'keyName' => 'zone',
                    ],
                    [
                        'fieldAccessors' => [
                            'getClusterId',
                        ],
                        'keyName' => 'cluster_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNodePoolId',
                        ],
                        'keyName' => 'node_pool_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
        ],
    ],
];
