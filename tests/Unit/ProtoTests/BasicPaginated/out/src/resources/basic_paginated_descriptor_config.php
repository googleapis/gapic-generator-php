<?php

return [
    'interfaces' => [
        'testing.basicpaginated.BasicPaginated' => [
            'MethodPaginated' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getTheResults',
                ],
            ],
        ],
    ],
];
