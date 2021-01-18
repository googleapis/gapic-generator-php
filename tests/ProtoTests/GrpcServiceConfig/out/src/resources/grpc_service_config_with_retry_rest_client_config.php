<?php

return [
    'interfaces' => [
        'testing.grpcserviceconfig.GrpcServiceConfigWithRetry' => [
            'ServiceLevelRetryMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:serviceLevelRetryMethod',
                'body' => '*',
            ],
            'MethodLevelRetryMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodLevelRetryMethod',
                'body' => '*',
            ],
            'MethodLevelDuplicatedParamsRetryMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodLevelRetryMethod',
                'body' => '*',
            ],
            'MethodTimeoutOnly' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodTimeoutOnly',
                'body' => '*',
            ],
        ],
    ],
];
