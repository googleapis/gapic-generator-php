<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.PredictionService' => [
            'Predict' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\PredictResponse',
                'headerParams' => [
                    [
                        'keyName' => 'placement',
                        'fieldAccessors' => [
                            'getPlacement',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'product' => 'projects/{project}/locations/{location}/catalogs/{catalog}/branches/{branch}/products/{product}',
            ],
        ],
    ],
];
