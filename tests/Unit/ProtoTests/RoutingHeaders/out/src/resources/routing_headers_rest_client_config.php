<?php

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
        ],
    ],
];
