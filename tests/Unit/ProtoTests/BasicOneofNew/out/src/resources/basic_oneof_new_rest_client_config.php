<?php

return [
    'interfaces' => [
        'testing.basiconeofnew.BasicOneofNew' => [
            'AMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:aMethod',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
