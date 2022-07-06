<?php

return [
    'numericEnums' => true,
    'interfaces' => [
        'testing.basiconeof.BasicOneof' => [
            'AMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:aMethod',
                'body' => '*',
            ],
        ],
    ],
];
