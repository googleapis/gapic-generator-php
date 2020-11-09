<?php

return [
    'interfaces' => [
        'testing.basicclientstreaming.BasicClientStreaming' => [
            'MethodClient' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ClientStreaming',
                ],
            ],
        ],
    ],
];
