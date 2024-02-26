<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.ControlService' => [
            'ListControls' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getControls',
                ],
            ],
        ],
    ],
];
