<?php

return [
    'interfaces' => [
        'testing.grpcserviceconfig.GrpcServiceConfigWithRetry1' => [
            'Method1A' => [
                'method' => 'post',
                'uriTemplate' => '/path:method1A',
                'body' => '*',
            ],
            'Method1BLro' => [
                'method' => 'post',
                'uriTemplate' => '/path:method1BLro',
                'body' => '*',
            ],
            'Method1CServiceLevelRetry' => [
                'method' => 'post',
                'uriTemplate' => '/path:method1CServiceLevelRetry',
                'body' => '*',
            ],
            'Method1DTimeoutOnlyRetry' => [
                'method' => 'post',
                'uriTemplate' => '/path:method1DTimeoutOnlyRetry',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
