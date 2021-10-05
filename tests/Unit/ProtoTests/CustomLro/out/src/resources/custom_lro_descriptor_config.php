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
                    'operationErrorCodeMethod' => 'getHttpErrorStatusCode',
                    'operationNameMethod' => 'getName',
                    'operationStatusMethod' => 'getStatus',
                    'operationStatusDoneValue' => \Testing\CustomLro\CustomOperationResponse\Status::DONE,
                ],
            ],
        ],
    ],
];
