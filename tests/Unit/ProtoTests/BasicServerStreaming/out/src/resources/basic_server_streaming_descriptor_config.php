<?php

return [
    'interfaces' => [
        'testing.basicserverstreaming.BasicServerStreaming' => [
            'MethodEmpty' => [
                'callType' => \Google\ApiCore\Call::SERVER_STREAMING_CALL,
                'responseType' => 'Testing\BasicServerStreaming\Response',
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
            ],
            'MethodServer' => [
                'callType' => \Google\ApiCore\Call::SERVER_STREAMING_CALL,
                'responseType' => 'Testing\BasicServerStreaming\Response',
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
            ],
        ],
    ],
];
