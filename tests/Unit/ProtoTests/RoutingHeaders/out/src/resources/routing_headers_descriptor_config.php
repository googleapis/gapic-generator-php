<?php

return [
    'interfaces' => [
        'testing.routingheaders.RoutingHeaders' => [
            'DeleteMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'GetMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'GetNoPlaceholdersMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'GetNoTemplateMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'NestedMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'NestedMultiMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'OrderingMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'PatchMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'PostMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
            ],
            'PutMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
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
