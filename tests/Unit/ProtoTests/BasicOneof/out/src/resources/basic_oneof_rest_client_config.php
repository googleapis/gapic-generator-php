<?php

return [
    'interfaces' => [
        'testing.basiconeof.BasicOneof' => [
            'AMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:aMethod',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
