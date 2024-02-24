<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.MerchantCenterAccountLinkService' => [
            'CreateMerchantCenterAccountLink' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\MerchantCenterAccountLink',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\CreateMerchantCenterAccountLinkMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
        ],
    ],
];
