<?php

return [
    'interfaces' => [
        'testing.grpcserviceconfig.GrpcServiceConfigNoRetry' => [
            'NoRetryMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:noRetryMethod',
                'body' => '*',
            ],
        ],
    ],
];
