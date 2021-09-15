<?php

return [
    'interfaces' => [
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
