<?php

return [
    'interfaces' => [
        'testing.customlro.CustomLro' => [
            'CreateFoo' => [
                'longRunning' => [
                    'additionalArgumentMethods' => [
                        'getProject',
                        'getRegion',
                        'getFoo',
                    ],
                    'getOperationMethod' => 'get',
                    'cancelOperationMethod' => 'cancel',
                    'deleteOperationMethod' => 'delete',
                    'operationErrorCodeMethod' => 'getHttpErrorStatusCode',
                    'operationErrorMessageMethod' => 'getHttpErrorMessage',
                    'operationNameMethod' => 'getName',
                    'operationStatusMethod' => 'getStatus',
                    'operationStatusDoneValue' => \Testing\CustomLro\CustomOperationResponse\Status::DONE,
                ],
            ],
        ],
    ],
];
