<?php

return [
    'interfaces' => [
        'testing.customlro.CustomLroOperations' => [
            'Cancel' => [
                'method' => 'patch',
                'uriTemplate' => '/operation',
            ],
            'Delete' => [
                'method' => 'delete',
                'uriTemplate' => '/operation',
            ],
            'Get' => [
                'method' => 'get',
                'uriTemplate' => '/operation',
                'queryParams' => [
                    'project',
                    'region',
                    'foo',
                ],
            ],
        ],
    ],
];
