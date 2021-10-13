<?php

return [
    'interfaces' => [
        'testing.customlro.CustomLro' => [
            'CreateFoo' => [
                'method' => 'post',
                'uriTemplate' => '/foo',
                'body' => '*',
            ],
        ],
        'testing.customlro.CustomLroOperations' => [
            'Get' => [
                'method' => 'get',
                'uriTemplate' => '/operation',
                'queryParams' => [
                    'project',
                    'region',
                ],
            ],
        ],
    ],
];
