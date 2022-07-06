<?php

return [
    'interfaces' => [
        'testing.basicserverstreaming.BasicServerStreaming' => [
            'MethodServer' => [
                'method' => 'post',
                'uriTemplate' => '/path:serverStreaming',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
