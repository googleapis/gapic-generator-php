<?php

return [
    'interfaces' => [
        'testing.basicserverstreaming.BasicServerStreaming' => [
            'MethodServer' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
            ],
            'MethodEmpty' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
            ],
        ],
    ],
];
