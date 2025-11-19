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
        'google.cloud.retail.v2alpha.ProjectService' => [
            'AcceptTerms' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{project=projects/*/retailProject}:acceptTerms',
                'body' => '*',
                'placeholders' => [
                    'project' => [
                        'getters' => [
                            'getProject',
                        ],
                    ],
                ],
            ],
            'EnrollSolution' => [
                'method' => 'post',
                'uriTemplate' => '/v2alpha/{project=projects/*}:enrollSolution',
                'body' => '*',
                'placeholders' => [
                    'project' => [
                        'getters' => [
                            'getProject',
                        ],
                    ],
                ],
            ],
            'GetAlertConfig' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/alertConfig}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetLoggingConfig' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/loggingConfig}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetProject' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{name=projects/*/retailProject}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListEnrolledSolutions' => [
                'method' => 'get',
                'uriTemplate' => '/v2alpha/{parent=projects/*}:enrolledSolutions',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'UpdateAlertConfig' => [
                'method' => 'patch',
                'uriTemplate' => '/v2alpha/{alert_config.name=projects/*/alertConfig}',
                'body' => 'alert_config',
                'placeholders' => [
                    'alert_config.name' => [
                        'getters' => [
                            'getAlertConfig',
                            'getName',
                        ],
                    ],
                ],
            ],
            'UpdateLoggingConfig' => [
                'method' => 'patch',
                'uriTemplate' => '/v2alpha/{logging_config.name=projects/*/loggingConfig}',
                'body' => 'logging_config',
                'placeholders' => [
                    'logging_config.name' => [
                        'getters' => [
                            'getLoggingConfig',
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
