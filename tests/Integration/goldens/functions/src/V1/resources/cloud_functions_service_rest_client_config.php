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
        'google.cloud.functions.v1.CloudFunctionsService' => [
            'CallFunction' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=projects/*/locations/*/functions/*}:call',
                'body' => '*',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'CreateFunction' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{location=projects/*/locations/*}/functions',
                'body' => 'function',
                'placeholders' => [
                    'location' => [
                        'getters' => [
                            'getLocation',
                        ],
                    ],
                ],
            ],
            'DeleteFunction' => [
                'method' => 'delete',
                'uriTemplate' => '/v1/{name=projects/*/locations/*/functions/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GenerateDownloadUrl' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=projects/*/locations/*/functions/*}:generateDownloadUrl',
                'body' => '*',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GenerateUploadUrl' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{parent=projects/*/locations/*}/functions:generateUploadUrl',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'GetFunction' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=projects/*/locations/*/functions/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetIamPolicy' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{resource=projects/*/locations/*/functions/*}:getIamPolicy',
                'placeholders' => [
                    'resource' => [
                        'getters' => [
                            'getResource',
                        ],
                    ],
                ],
            ],
            'ListFunctions' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{parent=projects/*/locations/*}/functions',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'SetIamPolicy' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{resource=projects/*/locations/*/functions/*}:setIamPolicy',
                'body' => '*',
                'placeholders' => [
                    'resource' => [
                        'getters' => [
                            'getResource',
                        ],
                    ],
                ],
            ],
            'TestIamPermissions' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{resource=projects/*/locations/*/functions/*}:testIamPermissions',
                'body' => '*',
                'placeholders' => [
                    'resource' => [
                        'getters' => [
                            'getResource',
                        ],
                    ],
                ],
            ],
            'UpdateFunction' => [
                'method' => 'patch',
                'uriTemplate' => '/v1/{function.name=projects/*/locations/*/functions/*}',
                'body' => 'function',
                'placeholders' => [
                    'function.name' => [
                        'getters' => [
                            'getFunction',
                            'getName',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
