<?php

return [
    'interfaces' => [
        'spanner.cloud.database_v1.DatabaseAdmin' => [
            'AMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:aMethod',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
