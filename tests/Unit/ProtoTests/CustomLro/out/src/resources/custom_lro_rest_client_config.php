<?php

return [
    'interfaces' => [
        'testing.customlro.CustomLro' => [
            'CreateFoo' => [
                'method' => 'post',
                'uriTemplate' => '/foo',
                'body' => '*',
            ],
        ],
    ],
];
