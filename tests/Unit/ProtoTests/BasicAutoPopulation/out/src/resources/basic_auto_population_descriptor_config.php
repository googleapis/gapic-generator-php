<?php

return [
    'interfaces' => [
        'testing.basicautopopulation.BasicAutoPopulation' => [
            'CreateFoo' => [
                'autoPopulatedFields' => [
                    'requestId' => \Google\Api\FieldInfo\Format::UUID4,
                ],
            ],
        ],
    ],
];
