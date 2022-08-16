<?php

return [
    'interfaces' => [
        'google.cloud.kms.v1.KeyManagementService' => [
            'AsymmetricDecrypt' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\AsymmetricDecryptResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'AsymmetricSign' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\AsymmetricSignResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'CreateCryptoKey' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKey',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CreateCryptoKeyVersion' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKeyVersion',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CreateImportJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\ImportJob',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'CreateKeyRing' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\KeyRing',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'Decrypt' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\DecryptResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'DestroyCryptoKeyVersion' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKeyVersion',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'Encrypt' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\EncryptResponse',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetCryptoKey' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKey',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetCryptoKeyVersion' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKeyVersion',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetIamPolicy' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Iam\V1\Policy',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getResource',
                        ],
                        'keyName' => 'resource',
                    ],
                ],
            ],
            'GetImportJob' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\ImportJob',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetKeyRing' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\KeyRing',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetPublicKey' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\PublicKey',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'ImportCryptoKeyVersion' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKeyVersion',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
            'ListCryptoKeyVersions' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\ListCryptoKeyVersionsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getCryptoKeyVersions',
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
            'ListCryptoKeys' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\ListCryptoKeysResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getCryptoKeys',
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
            'ListImportJobs' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\ListImportJobsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getImportJobs',
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
            'ListKeyRings' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\ListKeyRingsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getKeyRings',
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
            'RestoreCryptoKeyVersion' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKeyVersion',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'UpdateCryptoKey' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKey',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getCryptoKey',
                            'getName',
                        ],
                        'keyName' => 'crypto_key.name',
                    ],
                ],
            ],
            'UpdateCryptoKeyPrimaryVersion' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKey',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'UpdateCryptoKeyVersion' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Kms\V1\CryptoKeyVersion',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getCryptoKeyVersion',
                            'getName',
                        ],
                        'keyName' => 'crypto_key_version.name',
                    ],
                ],
            ],
            'GetLocation' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Location\Location',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'ListLocations' => [
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Google\Cloud\Location\ListLocationsResponse',
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getLocations',
                ],
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
        ],
    ],
];
