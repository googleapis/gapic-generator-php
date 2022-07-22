<?php

return [
    'interfaces' => [
        'testing.resourcenames.ResourceNames' => [
            'FileLevelChildTypeRefMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:fileLevelChildTypeRefMethod',
                'body' => '*',
            ],
            'FileLevelTypeRefMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:fileLevelTypeRefMethod',
                'body' => '*',
            ],
            'MultiPatternMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:multiPatternMethod',
                'body' => '*',
            ],
            'SinglePatternMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:singlePatternMethod',
                'body' => '*',
            ],
            'WildcardChildReferenceMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:wildcardChildReference',
                'body' => '*',
            ],
            'WildcardMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:wildcardMethod',
                'body' => '*',
            ],
            'WildcardMultiMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:wildcardMultiMethod',
                'body' => '*',
            ],
            'WildcardReferenceMethod' => [
                'method' => 'post',
                'uriTemplate' => '/path:wildcardReference',
                'body' => '*',
            ],
        ],
    ],
    'numericEnums' => true,
];
