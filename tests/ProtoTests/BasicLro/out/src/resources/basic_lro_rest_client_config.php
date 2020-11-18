<?php

return [
    'interfaces' => [
        'testing.basiclro.BasicLro' => [
            'Method1' => [
                'method' => 'post',
                'uriTemplate' => '/path:method1',
                'body' => '*',
            ],
        ],
    ],
];
