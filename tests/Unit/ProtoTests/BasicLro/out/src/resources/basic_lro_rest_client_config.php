<?php

return [
    'interfaces' => [
        'testing.basiclro.BasicLro' => [
            'Method1' => [
                'method' => 'post',
                'uriTemplate' => '/path:method1',
                'body' => '*',
            ],
            'MethodNonLro1' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodNonLro1',
                'body' => '*',
            ],
            'MethodNonLro2' => [
                'method' => 'post',
                'uriTemplate' => '/path:methodNonLro2',
                'body' => '*',
            ],
        ],
    ],
];
