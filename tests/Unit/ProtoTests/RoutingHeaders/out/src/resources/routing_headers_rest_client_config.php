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
                'method' => 'delete',
                'uriTemplate' => '/{name=items/*}/child',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetMethod' => [
                'method' => 'get',
                'uriTemplate' => '/{name=items/*}/child',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetNoPlaceholdersMethod' => [
                'method' => 'get',
                'uriTemplate' => '/root/child',
            ],
            'GetNoTemplateMethod' => [
                'method' => 'get',
                'uriTemplate' => '/{name}/child',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'NestedMethod' => [
                'method' => 'get',
                'uriTemplate' => '/{nest1.nest2.name=items/*}/child',
                'placeholders' => [
                    'nest1.nest2.name' => [
                        'getters' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                    ],
                ],
                'queryParams' => [
                    'nest1',
                    'another_name',
                ],
            ],
            'NestedMultiMethod' => [
                'method' => 'get',
                'uriTemplate' => '/{nest1.nest2.name=items/*}/child1/{name=items/*}/child2/{another_name=more_items/*/and_more/*}/child3',
                'placeholders' => [
                    'another_name' => [
                        'getters' => [
                            'getAnotherName',
                        ],
                    ],
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                    'nest1.nest2.name' => [
                        'getters' => [
                            'getNest1',
                            'getNest2',
                            'getName',
                        ],
                    ],
                ],
                'queryParams' => [
                    'nest1',
                ],
            ],
            'OrderingMethod' => [
                'method' => 'get',
                'uriTemplate' => '/{a=a}/{c=c}/{aa=aa}/{b=b}/{d=d}/{a_id=a_id}/{b_id=b_id}/{e=e}',
                'placeholders' => [
                    'a' => [
                        'getters' => [
                            'getA',
                        ],
                    ],
                    'a_id' => [
                        'getters' => [
                            'getAId',
                        ],
                    ],
                    'aa' => [
                        'getters' => [
                            'getAa',
                        ],
                    ],
                    'b' => [
                        'getters' => [
                            'getB',
                        ],
                    ],
                    'b_id' => [
                        'getters' => [
                            'getBId',
                        ],
                    ],
                    'c' => [
                        'getters' => [
                            'getC',
                        ],
                    ],
                    'd' => [
                        'getters' => [
                            'getD',
                        ],
                    ],
                    'e' => [
                        'getters' => [
                            'getE',
                        ],
                    ],
                ],
            ],
            'PatchMethod' => [
                'method' => 'patch',
                'uriTemplate' => '/{name=items/*}/child',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'PostMethod' => [
                'method' => 'post',
                'uriTemplate' => '/{name=items/*}/child',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'PutMethod' => [
                'method' => 'put',
                'uriTemplate' => '/{name=items/*}/child',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'RoutingRuleWithOutParameters' => [
                'method' => 'get',
                'uriTemplate' => '/{name=items/*}/child',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
                'queryParams' => [
                    'nest1',
                    'another_name',
                ],
            ],
        ],
    ],
    'numericEnums' => true,
];
