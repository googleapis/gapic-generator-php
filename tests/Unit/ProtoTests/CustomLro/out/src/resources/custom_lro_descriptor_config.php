<?php

return [
    'interfaces' => [
        'testing.customlro.CustomLro' => [
            'CreateFoo' => [
                'longRunning' => [
                    'additionalArgumentMethods' => [
                        'getProject',
                        'getRegion',
                    ],
                    'getOperationMethod' => 'get',
                    'cancelOperationMethod' => null,
                    'deleteOperationMethod' => null,
                    'operationStatusMethod' => 'getStatus',
                    'operationStatusDoneValue' => 'Status::DONE',
                ],
            ],
        ],
    ],
];
