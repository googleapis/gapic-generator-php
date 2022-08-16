<?php

return [
    'interfaces' => [
        'google.cloud.talent.v4beta1.EventService' => [
            'CreateClientEvent' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Talent\V4beta1\ClientEvent',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getParent',
                        ],
                        'keyName' => 'parent',
                    ],
                ],
            ],
        ],
    ],
];
