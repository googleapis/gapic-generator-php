<?php

return [
    'interfaces' => [
        'testing.disablesnippets.DisableSnippets' => [
            'Method1' => [
                'method' => 'post',
                'uriTemplate' => '/path:method1',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
