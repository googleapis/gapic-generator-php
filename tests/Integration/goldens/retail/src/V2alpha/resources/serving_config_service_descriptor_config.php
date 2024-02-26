<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.ServingConfigService' => [
            'ListServingConfigs' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getServingConfigs',
                ],
            ],
        ],
    ],
];
