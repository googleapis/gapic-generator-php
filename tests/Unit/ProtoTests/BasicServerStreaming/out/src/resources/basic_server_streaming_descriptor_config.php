<?php

return [
    'interfaces' => [
        'testing.basicserverstreaming.BasicServerStreaming' => [
            'MethodEmpty' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
                'callType' => \Google\ApiCore\Call::SERVER_STREAMING_CALL,
                'responseType' => 'Testing\BasicServerStreaming\Response',
            ],
            'MethodServer' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
                'callType' => \Google\ApiCore\Call::SERVER_STREAMING_CALL,
                'responseType' => 'Testing\BasicServerStreaming\Response',
            ],
        ],
    ],
];
