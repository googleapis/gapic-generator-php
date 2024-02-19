<?php

return [
    'interfaces' => [
        'testing.basicautopopulation.BasicAutoPopulation' => [
            'CreateFoo' => [
                'autoPopulatedFields' => [
                    'requestId',
                ],
            ],
        ],
    ],
];
