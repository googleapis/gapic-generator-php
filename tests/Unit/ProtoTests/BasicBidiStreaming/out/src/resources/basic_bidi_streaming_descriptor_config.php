<?php

return [
    'interfaces' => [
        'testing.basicbidistreaming.BasicBidiStreaming' => [
            'MethodBidi' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
                'callType' => \Google\ApiCore\Call::BIDI_STREAMING_CALL,
                'responseType' => 'Testing\BasicBidiStreaming\Response',
            ],
            'MethodEmpty' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
                'callType' => \Google\ApiCore\Call::BIDI_STREAMING_CALL,
                'responseType' => 'Testing\BasicBidiStreaming\Response',
            ],
        ],
    ],
];
