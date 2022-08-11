<?php

return [
    'interfaces' => [
        'testing.routingheaders.RoutingHeaders' => [
            'DeleteMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'GetNoPlaceholdersMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'GetNoTemplateMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'NestedMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                        'keyName' => 'nest1.nest2.name',
                    ],
                ],
            ],
            'NestedMultiMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                        'keyName' => 'nest1.nest2.name',
                    ],
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                    [
                        'fieldAccessors' => [
                            'getAnotherName',
                        ],
                        'keyName' => 'another_name',
                    ],
                ],
            ],
            'OrderingMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getA',
                        ],
                        'keyName' => 'a',
                    ],
                    [
                        'fieldAccessors' => [
                            'getC',
                        ],
                        'keyName' => 'c',
                    ],
                    [
                        'fieldAccessors' => [
                            'getAa',
                        ],
                        'keyName' => 'aa',
                    ],
                    [
                        'fieldAccessors' => [
                            'getB',
                        ],
                        'keyName' => 'b',
                    ],
                    [
                        'fieldAccessors' => [
                            'getD',
                        ],
                        'keyName' => 'd',
                    ],
                    [
                        'fieldAccessors' => [
                            'getAId',
                        ],
                        'keyName' => 'a_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getBId',
                        ],
                        'keyName' => 'b_id',
                    ],
                    [
                        'fieldAccessors' => [
                            'getE',
                        ],
                        'keyName' => 'e',
                    ],
                ],
            ],
            'PatchMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'PostMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'PutMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                    ],
                ],
            ],
            'RoutingRuleWithOutParameters' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'RoutingRuleWithParameters' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'keyName' => 'name',
                        'matchers' => [
                            '/^(?<name>projects\/[^\/]+)\/foos$/',
                        ],
                    ],
                    [
                        'fieldAccessors' => [
                            'getAnotherName',
                        ],
                        'keyName' => 'foo_name',
                        'matchers' => [
                            '/^(?<foo_name>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/',
                            '/^(?<foo_name>projects\/[^\/]+\/foos\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/',
                        ],
                    ],
                    [
                        'fieldAccessors' => [
                            'getAnotherName',
                        ],
                        'keyName' => 'bar_name',
                        'matchers' => [
                            '/^projects\/[^\/]+\/foos\/[^\/]+\/(?<bar_name>bars\/[^\/]+)(?:\/.*)?$/',
                        ],
                    ],
                    [
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                        'keyName' => 'nested_name',
                    ],
                    [
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                        'keyName' => 'part_of_nested',
                        'matchers' => [
                            '/^(?<part_of_nested>projects\/[^\/]+)\/bars$/',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
