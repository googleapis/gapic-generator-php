<?php

return [
    'interfaces' => [
        'google.cloud.compute.v1.Addresses' => [
            'Delete' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'responseType' => 'Google\Cloud\Compute\V1\Operation',
                'longRunning' => [
                    'additionalArgumentMethods' => [
                        'getProject',
                        'getRegion',
                    ],
                    'getOperationMethod' => 'get',
                    'cancelOperationMethod' => null,
                    'deleteOperationMethod' => null,
                    'operationErrorCodeMethod' => 'getHttpErrorStatusCode',
                    'operationErrorMessageMethod' => 'getHttpErrorMessage',
                    'operationNameMethod' => 'getName',
                    'operationStatusMethod' => 'getStatus',
                    'operationStatusDoneValue' => \Google\Cloud\Compute\V1\Operation\Status::DONE,
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProject',
                        ],
                        'keyName' => 'project',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                    [
                        'fieldAccessors' => [
                            'getAddress',
                        ],
                        'keyName' => 'address',
                    ],
                ],
            ],
            'Insert' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'responseType' => 'Google\Cloud\Compute\V1\Operation',
                'longRunning' => [
                    'additionalArgumentMethods' => [
                        'getProject',
                        'getRegion',
                    ],
                    'getOperationMethod' => 'get',
                    'cancelOperationMethod' => null,
                    'deleteOperationMethod' => null,
                    'operationErrorCodeMethod' => 'getHttpErrorStatusCode',
                    'operationErrorMessageMethod' => 'getHttpErrorMessage',
                    'operationNameMethod' => 'getName',
                    'operationStatusMethod' => 'getStatus',
                    'operationStatusDoneValue' => \Google\Cloud\Compute\V1\Operation\Status::DONE,
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProject',
                        ],
                        'keyName' => 'project',
                    ],
                    [
                        'fieldAccessors' => [
                            'getRegion',
                        ],
                        'keyName' => 'region',
                    ],
                ],
            ],
            'AggregatedList' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Compute\V1\AddressAggregatedList',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getMaxResults',
                    'requestPageSizeSetMethod' => 'setMaxResults',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getItems',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProject',
                        ],
                        'keyName' => 'project',
                    ],
                ],
            ],
            'List' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Compute\V1\AddressList',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getMaxResults',
                    'requestPageSizeSetMethod' => 'setMaxResults',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getItems',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getProject',
                        ],
                        'keyName' => 'project',
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
