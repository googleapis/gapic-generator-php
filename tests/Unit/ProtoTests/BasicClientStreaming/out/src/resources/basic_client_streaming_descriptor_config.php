<?php

return [
    'interfaces' => [
        'testing.basicclientstreaming.BasicClientStreaming' => [
            'MethodClient' => [
                'callType' => \Google\ApiCore\Call::CLIENT_STREAMING_CALL,
                'responseType' => 'Testing\BasicClientStreaming\Response',
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ClientStreaming',
                ],
            ],
            'MethodEmpty' => [
                'callType' => \Google\ApiCore\Call::CLIENT_STREAMING_CALL,
                'responseType' => 'Testing\BasicClientStreaming\Response',
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ClientStreaming',
                ],
            ],
        ],
    ],
];
