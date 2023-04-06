<?php

return [
    'interfaces' => [
        'google.cloud.retail.v2alpha.SearchService' => [
            'Search' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getResults',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\SearchResponse',
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
                'branch' => 'projects/{project}/locations/{location}/catalogs/{catalog}/branches/{branch}',
            ],
        ],
    ],
];
