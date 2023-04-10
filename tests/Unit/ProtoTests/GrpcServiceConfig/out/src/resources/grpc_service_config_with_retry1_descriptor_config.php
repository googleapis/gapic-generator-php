<?php

return [
    'interfaces' => [
        'testing.grpcserviceconfig.GrpcServiceConfigWithRetry1' => [
            'Method1BLro' => [
                'longRunning' => [
                    'operationReturnType' => '\Testing\GrpcServiceConfig\LroResponse',
                    'metadataReturnType' => '\Testing\GrpcServiceConfig\LroMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
            ],
            'Method1A' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\GrpcServiceConfig\Response1',
            ],
            'Method1BidiStreaming' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
                'callType' => \Google\ApiCore\Call::BIDI_STREAMING_CALL,
                'responseType' => 'Testing\GrpcServiceConfig\Response1',
            ],
            'Method1CServiceLevelRetry' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\GrpcServiceConfig\Response1',
            ],
            'Method1DTimeoutOnlyRetry' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\GrpcServiceConfig\Response1',
            ],
            'Method1ServerStreaming' => [
                'grpcStreaming' => [
                    'grpcStreamingType' => 'ServerStreaming',
                ],
                'callType' => \Google\ApiCore\Call::SERVER_STREAMING_CALL,
                'responseType' => 'Testing\GrpcServiceConfig\Response1',
            ],
        ],
    ],
];
