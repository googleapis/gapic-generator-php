<?php

return [
    'interfaces' => [
        'testing.basicbidistreaming.BasicBidiStreaming' => [
            'MethodBidi' => [
                'callType' => \Google\ApiCore\Call::BIDI_STREAMING_CALL,
                'responseType' => 'Testing\BasicBidiStreaming\Response',
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
            ],
            'MethodEmpty' => [
                'callType' => \Google\ApiCore\Call::BIDI_STREAMING_CALL,
                'responseType' => 'Testing\BasicBidiStreaming\Response',
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
            ],
        ],
    ],
];
