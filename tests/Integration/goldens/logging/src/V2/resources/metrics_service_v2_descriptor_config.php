<?php

return [
    'interfaces' => [
        'google.logging.v2.MetricsServiceV2' => [
            'CreateLogMetric' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Logging\V2\LogMetric',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'DeleteLogMetric' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getMetricName',
                        ],
                        'keyName' => 'metric_name',
                    ],
                ],
            ],
            'GetLogMetric' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Logging\V2\LogMetric',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getMetricName',
                        ],
                        'keyName' => 'metric_name',
                    ],
                ],
            ],
            'ListLogMetrics' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Logging\V2\ListLogMetricsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getMetrics',
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
            'UpdateLogMetric' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Logging\V2\LogMetric',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getMetricName',
                        ],
                        'keyName' => 'metric_name',
                    ],
                ],
            ],
        ],
    ],
];
