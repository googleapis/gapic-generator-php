<?php

return [
    'interfaces' => [
        'google.cloud.securitycenter.v1.SecurityCenter' => [
            'RunAssetDiscovery' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\SecurityCenter\V1\RunAssetDiscoveryResponse',
                    'metadataReturnType' => '\Google\Protobuf\GPBEmpty',
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
            'CreateFinding' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\Finding',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CreateNotificationConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\NotificationConfig',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CreateSource' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\Source',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'DeleteNotificationConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetIamPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Iam\V1\Policy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
            'GetNotificationConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\NotificationConfig',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetOrganizationSettings' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\OrganizationSettings',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetSource' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\Source',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GroupAssets' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\GroupAssetsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getGroupByResults',
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
            'GroupFindings' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\GroupFindingsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getGroupByResults',
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
            'ListAssets' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\ListAssetsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getListAssetsResults',
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
            'ListFindings' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\ListFindingsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getListFindingsResults',
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
            'ListNotificationConfigs' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\ListNotificationConfigsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getNotificationConfigs',
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
            'ListSources' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\ListSourcesResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getSources',
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
            'SetFindingState' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\Finding',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'SetIamPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Iam\V1\Policy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
            'TestIamPermissions' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Iam\V1\TestIamPermissionsResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
            'UpdateFinding' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\Finding',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getFinding',
                            'getName',
                        ],
                        'keyName' => 'finding.name',
                    ],
                ],
            ],
            'UpdateNotificationConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\NotificationConfig',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getNotificationConfig',
                            'getName',
                        ],
                        'keyName' => 'notification_config.name',
                    ],
                ],
            ],
            'UpdateOrganizationSettings' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\OrganizationSettings',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getOrganizationSettings',
                            'getName',
                        ],
                        'keyName' => 'organization_settings.name',
                    ],
                ],
            ],
            'UpdateSecurityMarks' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\SecurityMarks',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getSecurityMarks',
                            'getName',
                        ],
                        'keyName' => 'security_marks.name',
                    ],
                ],
            ],
            'UpdateSource' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\SecurityCenter\V1\Source',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getSource',
                            'getName',
                        ],
                        'keyName' => 'source.name',
                    ],
                ],
            ],
        ],
    ],
];
