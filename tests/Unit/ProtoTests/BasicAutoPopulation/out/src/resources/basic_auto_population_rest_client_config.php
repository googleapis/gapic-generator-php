<?php

return [
    'interfaces' => [
        'testing.basicautopopulation.BasicAutoPopulation' => [
            'CreateFoo' => [
                'method' => 'post',
                'uriTemplate' => '/foo',
                'body' => '*',
            ],
            'GetFoo' => [
                'method' => 'get',
                'uriTemplate' => '/foo',
                'queryParams' => [
                    'a_field',
                ],
            ],
        ],
    ],
    'numericEnums' => true,
];
