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
        'google.cloud.retail.v2alpha.ProjectService' => [
            'EnrollSolution' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Retail\V2alpha\EnrollSolutionResponse',
                    'metadataReturnType' => '\Google\Cloud\Retail\V2alpha\EnrollSolutionMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'project',
                        'fieldAccessors' => [
                            'getProject',
                        ],
                    ],
                ],
            ],
            'AcceptTerms' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\Project',
                'headerParams' => [
                    [
                        'keyName' => 'project',
                        'fieldAccessors' => [
                            'getProject',
                        ],
                    ],
                ],
            ],
            'GetAlertConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\AlertConfig',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetLoggingConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\LoggingConfig',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetProject' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\Project',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListEnrolledSolutions' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\ListEnrolledSolutionsResponse',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'UpdateAlertConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\AlertConfig',
                'headerParams' => [
                    [
                        'keyName' => 'alert_config.name',
                        'fieldAccessors' => [
                            'getAlertConfig',
                            'getName',
                        ],
                    ],
                ],
            ],
            'UpdateLoggingConfig' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Retail\V2alpha\LoggingConfig',
                'headerParams' => [
                    [
                        'keyName' => 'logging_config.name',
                        'fieldAccessors' => [
                            'getLoggingConfig',
                            'getName',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'alertConfig' => 'projects/{project}/alertConfig',
                'loggingConfig' => 'projects/{project}/loggingConfig',
                'project' => 'projects/{project}',
                'retailProject' => 'projects/{project}/retailProject',
            ],
        ],
    ],
];
