<?php

return [
    'interfaces' => [
        'testing.basicserverstreaming.BasicServerStreaming' => [
            'MethodEmpty' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
            ],
            'MethodServer' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
            ],
        ],
    ],
];
