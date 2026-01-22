<?php
/*
 * Copyright 2026 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * GENERATED CODE WARNING
 * This file was automatically generated - do not edit!
 */

return [
    'interfaces' => [
        'testing.routingheaders.RoutingHeaders' => [
            'DeleteMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
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
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'NestedMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'nest1.nest2.name',
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                    ],
                ],
            ],
            'NestedMultiMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'nest1.nest2.name',
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                    ],
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                    [
                        'keyName' => 'another_name',
                        'fieldAccessors' => [
                            'getAnotherName',
                        ],
                    ],
                ],
            ],
            'OrderingMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'a',
                        'fieldAccessors' => [
                            'getA',
                        ],
                    ],
                    [
                        'keyName' => 'c',
                        'fieldAccessors' => [
                            'getC',
                        ],
                    ],
                    [
                        'keyName' => 'aa',
                        'fieldAccessors' => [
                            'getAa',
                        ],
                    ],
                    [
                        'keyName' => 'b',
                        'fieldAccessors' => [
                            'getB',
                        ],
                    ],
                    [
                        'keyName' => 'd',
                        'fieldAccessors' => [
                            'getD',
                        ],
                    ],
                    [
                        'keyName' => 'a_id',
                        'fieldAccessors' => [
                            'getAId',
                        ],
                    ],
                    [
                        'keyName' => 'b_id',
                        'fieldAccessors' => [
                            'getBId',
                        ],
                    ],
                    [
                        'keyName' => 'e',
                        'fieldAccessors' => [
                            'getE',
                        ],
                    ],
                ],
            ],
            'PatchMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'PostMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'PutMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\RoutingHeaders\Response',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
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
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                        'matchers' => [
                            '/^(?<name>projects\/[^\/]+)\/foos$/',
                        ],
                    ],
                    [
                        'keyName' => 'foo_name',
                        'fieldAccessors' => [
                            'getAnotherName',
                        ],
                        'matchers' => [
                            '/^(?<foo_name>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/',
                            '/^(?<foo_name>projects\/[^\/]+\/foos\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/',
                        ],
                    ],
                    [
                        'keyName' => 'bar_name',
                        'fieldAccessors' => [
                            'getAnotherName',
                        ],
                        'matchers' => [
                            '/^projects\/[^\/]+\/foos\/[^\/]+\/(?<bar_name>bars\/[^\/]+)(?:\/.*)?$/',
                        ],
                    ],
                    [
                        'keyName' => 'nested_name',
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                    ],
                    [
                        'keyName' => 'part_of_nested',
                        'fieldAccessors' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                        'matchers' => [
                            '/^(?<part_of_nested>projects\/[^\/]+)\/bars$/',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
