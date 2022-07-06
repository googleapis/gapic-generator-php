<?php

return [
    'numericEnums' => true,
    'interfaces' => [
        'testing.basicpaginated.BasicPaginated' => [
            'MethodPaginated' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodPaginated',
                'body' => '*',
            ],
        ],
    ],
];
