<?php

return [
    'interfaces' => [
        'testing.basicbidistreaming.BasicBidiStreaming' => [
            'MethodBidi' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
            ],
            'MethodEmpty' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
            ],
        ],
    ],
];
