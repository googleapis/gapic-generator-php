<?php

return [
    'interfaces' => [
        'google.cloud.asset.v1.AssetService' => [
            'AnalyzeIamPolicyLongrunning' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Asset\V1\AnalyzeIamPolicyLongrunningResponse',
                    'metadataReturnType' => '\Google\Cloud\Asset\V1\AnalyzeIamPolicyLongrunningMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getAnalysisQuery',
                            'getScope',
                        ],
                        'keyName' => 'analysis_query.scope',
                    ],
                ],
            ],
            'ExportAssets' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Asset\V1\ExportAssetsResponse',
                    'metadataReturnType' => '\Google\Cloud\Asset\V1\ExportAssetsRequest',
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
            'AnalyzeIamPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\AnalyzeIamPolicyResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getAnalysisQuery',
                            'getScope',
                        ],
                        'keyName' => 'analysis_query.scope',
                    ],
                ],
            ],
            'AnalyzeMove' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\AnalyzeMoveResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
            'BatchGetAssetsHistory' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\BatchGetAssetsHistoryResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CreateFeed' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\Feed',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'DeleteFeed' => [
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
            'GetFeed' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\Feed',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'ListAssets' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\ListAssetsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getAssets',
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
            'ListFeeds' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\ListFeedsResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'SearchAllIamPolicies' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\SearchAllIamPoliciesResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getResults',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getScope',
                        ],
                        'keyName' => 'scope',
                    ],
                ],
            ],
            'SearchAllResources' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\SearchAllResourcesResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getResults',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getScope',
                        ],
                        'keyName' => 'scope',
                    ],
                ],
            ],
            'UpdateFeed' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Asset\V1\Feed',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getFeed',
                            'getName',
                        ],
                        'keyName' => 'feed.name',
                    ],
                ],
            ],
        ],
    ],
];
