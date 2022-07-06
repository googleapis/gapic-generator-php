<?php

return [
    'numericEnums' => true,
    'interfaces' => [
        'testing.basic.Basic' => [
            'AMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:aMethod',
                'body' => '*',
            ],
            'MethodWithArgs' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodWithArgs',
                'body' => '*',
            ],
        ],
    ],
];
