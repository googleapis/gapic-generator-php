<?php

return [
    'interfaces' => [
        'google.cloud.asset.v1.AssetService' => [
            'AnalyzeIamPolicyLongrunning' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Asset\V1\AnalyzeIamPolicyLongrunningResponse',
                    'metadataReturnType' => '\Google\Cloud\Asset\V1\AnalyzeIamPolicyLongrunningMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'analysis_query.scope',
                        'fieldAccessors' => [
                            'getAnalysisQuery',
                            'getScope',
                        ],
                    ],
                ],
            ],
            'ExportAssets' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Asset\V1\ExportAssetsResponse',
                    'metadataReturnType' => '\Google\Cloud\Asset\V1\ExportAssetsRequest',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'AnalyzeIamPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\AnalyzeIamPolicyResponse',
                'headerParams' => [
                    [
                        'keyName' => 'analysis_query.scope',
                        'fieldAccessors' => [
                            'getAnalysisQuery',
                            'getScope',
                        ],
                    ],
                ],
            ],
            'AnalyzeMove' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\AnalyzeMoveResponse',
                'headerParams' => [
                    [
                        'keyName' => 'resource',
                        'fieldAccessors' => [
                            'getResource',
                        ],
                    ],
                ],
            ],
            'BatchGetAssetsHistory' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\BatchGetAssetsHistoryResponse',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'CreateFeed' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\Feed',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'DeleteFeed' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetFeed' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\Feed',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListAssets' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getAssets',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\ListAssetsResponse',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'ListFeeds' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\ListFeedsResponse',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'SearchAllIamPolicies' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getResults',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\SearchAllIamPoliciesResponse',
                'headerParams' => [
                    [
                        'keyName' => 'scope',
                        'fieldAccessors' => [
                            'getScope',
                        ],
                    ],
                ],
            ],
            'SearchAllResources' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getResults',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\SearchAllResourcesResponse',
                'headerParams' => [
                    [
                        'keyName' => 'scope',
                        'fieldAccessors' => [
                            'getScope',
                        ],
                    ],
                ],
            ],
            'UpdateFeed' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\Feed',
                'headerParams' => [
                    [
                        'keyName' => 'feed.name',
                        'fieldAccessors' => [
                            'getFeed',
                            'getName',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'feed' => 'projects/{project}/feeds/{feed}',
                'folderFeed' => 'folders/{folder}/feeds/{feed}',
                'organizationFeed' => 'organizations/{organization}/feeds/{feed}',
                'projectFeed' => 'projects/{project}/feeds/{feed}',
            ],
        ],
    ],
];
