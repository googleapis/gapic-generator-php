<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.AnalyticsService' => [
            'ExportAnalyticsMetrics' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\ExportAnalyticsMetricsResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\ExportMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
        ],
    ],
];
