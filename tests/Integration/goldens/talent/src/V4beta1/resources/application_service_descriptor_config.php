<?php

return [
    'interfaces' => [
        'google.cloud.talent.v4beta1.ApplicationService' => [
            'CreateApplication' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\Application',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'DeleteApplication' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetApplication' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\Application',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListApplications' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getApplications',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\ListApplicationsResponse',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'UpdateApplication' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\Application',
                'headerParams' => [
                    [
                        'keyName' => 'application.name',
                        'fieldAccessors' => [
                            'getApplication',
                            'getName',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'application' => 'projects/{project}/tenants/{tenant}/profiles/{profile}/applications/{application}',
                'company' => 'projects/{project}/tenants/{tenant}/companies/{company}',
                'job' => 'projects/{project}/tenants/{tenant}/jobs/{job}',
                'profile' => 'projects/{project}/tenants/{tenant}/profiles/{profile}',
                'projectCompany' => 'projects/{project}/companies/{company}',
                'projectJob' => 'projects/{project}/jobs/{job}',
                'projectTenantCompany' => 'projects/{project}/tenants/{tenant}/companies/{company}',
                'projectTenantJob' => 'projects/{project}/tenants/{tenant}/jobs/{job}',
            ],
        ],
    ],
];
