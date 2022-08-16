<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.SearchService' => [
            'Search' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\SearchResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getResults',
                ],
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
