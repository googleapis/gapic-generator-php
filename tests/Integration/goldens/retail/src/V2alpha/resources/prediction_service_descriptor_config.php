<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.PredictionService' => [
            'Predict' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\PredictResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getPlacement',
                        ],
                        'keyName' => 'placement',
                    ],
                ],
            ],
        ],
    ],
];
