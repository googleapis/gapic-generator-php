<?php
/*
 * Copyright 2025 Google LLC
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
        'google.cloud.retail.v2alpha.ProductService' => [
            'AddFulfillmentPlaces' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{product=projects/*/locations/*/catalogs/*/branches/*/products/**}:addFulfillmentPlaces',
                'body' => '*',
                'placeholders' => [
                    'product' => [
                        'getters' => [
                            'getProduct',
                        ],
                    ],
                ],
            ],
            'AddLocalInventories' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{product=projects/*/locations/*/catalogs/*/branches/*/products/**}:addLocalInventories',
                'body' => '*',
                'placeholders' => [
                    'product' => [
                        'getters' => [
                            'getProduct',
                        ],
                    ],
                ],
            ],
            'CreateProduct' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*/branches/*}/products',
                'body' => 'product',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
                'queryParams' => [
                    'product_id',
                ],
            ],
            'DeleteProduct' => [
                'method' => 'delete',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/products/**}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ExportProducts' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*/branches/*}/products:export',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'GetProduct' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/products/**}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ImportProducts' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*/branches/*}/products:import',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'ListProducts' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*/branches/*}/products',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'PurgeProducts' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{parent=projects/*/locations/*/catalogs/*/branches/*}/products:purge',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'RemoveFulfillmentPlaces' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{product=projects/*/locations/*/catalogs/*/branches/*/products/**}:removeFulfillmentPlaces',
                'body' => '*',
                'placeholders' => [
                    'product' => [
                        'getters' => [
                            'getProduct',
                        ],
                    ],
                ],
            ],
            'RemoveLocalInventories' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{product=projects/*/locations/*/catalogs/*/branches/*/products/**}:removeLocalInventories',
                'body' => '*',
                'placeholders' => [
                    'product' => [
                        'getters' => [
                            'getProduct',
                        ],
                    ],
                ],
            ],
            'SetInventory' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{inventory.name=projects/*/locations/*/catalogs/*/branches/*/products/**}:setInventory',
                'body' => '*',
                'placeholders' => [
                    'inventory.name' => [
                        'getters' => [
                            'getInventory',
                            'getName',
                        ],
                    ],
                ],
            ],
            'UpdateProduct' => [
                'method' => 'patch',
                'uriTemplate' => '/v2alpha/{product.name=projects/*/locations/*/catalogs/*/branches/*/products/**}',
                'body' => 'product',
                'placeholders' => [
                    'product.name' => [
                        'getters' => [
                            'getProduct',
                            'getName',
                        ],
                    ],
                ],
            ],
        ],
        'google.longrunning.Operations' => [
            'GetOperation' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/operations/*}',
                'additionalBindings' => [
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/branches/*/places/*/operations/*}',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*/operations/*}',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/operations/*}',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/operations/*}',
                    ],
                ],
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListOperations' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/locations/*/catalogs/*}/operations',
                'additionalBindings' => [
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*/locations/*}/operations',
                    ],
                    [
                        'method' => 'get',
                        'uriTemplate' => '/v2alpha/{name=projects/*}/operations',
                    ],
                ],
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
