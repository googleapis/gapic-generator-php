<?php

return [
    'interfaces' => [
        'google.cloud.dataproc.v1.WorkflowTemplateService' => [
            'InstantiateInlineWorkflowTemplate' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Protobuf\GPBEmpty',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\WorkflowMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '43200000',
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
            'InstantiateWorkflowTemplate' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Protobuf\GPBEmpty',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\WorkflowMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '43200000',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'CreateWorkflowTemplate' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\WorkflowTemplate',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'DeleteWorkflowTemplate' => [
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
            'GetWorkflowTemplate' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\WorkflowTemplate',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'ListWorkflowTemplates' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\ListWorkflowTemplatesResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getTemplates',
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
            'UpdateWorkflowTemplate' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Dataproc\V1\WorkflowTemplate',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getTemplate',
                            'getName',
                        ],
                        'keyName' => 'template.name',
                    ],
                ],
            ],
        ],
    ],
];
