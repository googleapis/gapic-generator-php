<?php

return [
    'interfaces' => [
        'testing.basicclientstreaming.BasicClientStreaming' => [
            'MethodClient' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ClientStreaming',
                ],
                'callType' => \Google\ApiCore\Call::CLIENT_STREAMING_CALL,
                'responseType' => 'Testing\BasicClientStreaming\Response',
            ],
            'MethodEmpty' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ClientStreaming',
                ],
                'callType' => \Google\ApiCore\Call::CLIENT_STREAMING_CALL,
                'responseType' => 'Testing\BasicClientStreaming\Response',
            ],
        ],
    ],
];
