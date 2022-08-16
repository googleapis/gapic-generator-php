<?php

return [
    'interfaces' => [
        'google.cloud.talent.v4beta1.CompanyService' => [
            'CreateCompany' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\Company',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'DeleteCompany' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetCompany' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\Company',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'ListCompanies' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\ListCompaniesResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getCompanies',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'UpdateCompany' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\Company',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getCompany',
                            'getName',
                        ],
                        'keyName' => 'company.name',
                    ],
                ],
            ],
        ],
    ],
];
