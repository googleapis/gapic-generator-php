<?php

return [
    'interfaces' => [
        'google.cloud.dataproc.v1.WorkflowTemplateService' => [
            'InstantiateInlineWorkflowTemplate' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Protobuf\GPBEmpty',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\WorkflowMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '43200000',
                ],
            ],
            'InstantiateWorkflowTemplate' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Protobuf\GPBEmpty',
                    'metadataReturnType' => '\Google\Cloud\Dataproc\V1\WorkflowMetadata',
                    'initialPollDelayMillis' => '1000',
                    'pollDelayMultiplier' => '2.0',
                    'maxPollDelayMillis' => '10000',
                    'totalPollTimeoutMillis' => '43200000',
                ],
            ],
            'ListWorkflowTemplates' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getTemplates',
                ],
            ],
            'GetIamPolicy' => [
                'interfaceOverride' => 'google.iam.v1.IAMPolicy',
            ],
            'SetIamPolicy' => [
                'interfaceOverride' => 'google.iam.v1.IAMPolicy',
            ],
            'TestIamPermissions' => [
                'interfaceOverride' => 'google.iam.v1.IAMPolicy',
            ],
        ],
    ],
];
