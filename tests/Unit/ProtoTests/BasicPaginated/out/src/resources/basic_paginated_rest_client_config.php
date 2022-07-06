<?php

return [
    'interfaces' => [
        'testing.basicpaginated.BasicPaginated' => [
            'MethodPaginated' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodPaginated',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
