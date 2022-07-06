<?php

return [
    'numericEnums' => true,
    'interfaces' => [
        'testing.basicserverstreaming.BasicServerStreaming' => [
            'MethodServer' => [
                'method' => 'post',
                'uriTemplate' => '/path:serverStreaming',
                'body' => '*',
            ],
        ],
    ],
];
