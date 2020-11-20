<?php

return [
    'interfaces' => [
        'testing.basicclientstreaming.BasicClientStreaming' => [
            'MethodClient' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ClientStreaming',
                ],
            ],
            'MethodEmpty' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ClientStreaming',
                ],
            ],
        ],
    ],
];
