<?php

return [
    'interfaces' => [
        'google.cloud.dataproc.v1.AutoscalingPolicyService' => [
            'CreateAutoscalingPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\AutoscalingPolicy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'DeleteAutoscalingPolicy' => [
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
            'GetAutoscalingPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\AutoscalingPolicy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'ListAutoscalingPolicies' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\ListAutoscalingPoliciesResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getPolicies',
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
            'UpdateAutoscalingPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\AutoscalingPolicy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getPolicy',
                            'getName',
                        ],
                        'keyName' => 'policy.name',
                    ],
                ],
            ],
        ],
    ],
];
