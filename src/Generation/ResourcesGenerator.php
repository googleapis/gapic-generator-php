<?php
/*
 * Copyright 2020 Google LLC
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
declare(strict_types=1);

namespace Google\Generator\Generation;

use Google\Api\HttpRule;
use Google\ApiCore\ResourceTemplate\Parser;
use Google\ApiCore\ResourceTemplate\Segment;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\Expression;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\GapicYamlConfig;
use Google\Generator\Utils\GrpcServiceConfig;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\ServiceYamlConfig;
use Google\Rpc\Code;

class ResourcesGenerator
{
    // TODO(vNext): Remove this; only required for monolith compatibility.
    private static function ensureDecimal(string $s): string
    {
        return strpos($s, '.') === false ? ($s . '.0') : $s;
    }

    public static function generateDescriptorConfig(ServiceDetails $serviceDetails, GapicYamlConfig $gapicYamlConfig): string
    {
        $perMethod = function ($method) use ($gapicYamlConfig, $serviceDetails) {
            switch ($method->methodType) {
                case MethodDetails::CUSTOM_OP:
                    $name = $method->operationNameField;
                    $status = $method->operationStatusField;
                    $errorCode = $method->operationErrorCodeField;
                    $errorMessage = $method->operationErrorMessageField;
                    $doneValue = $status->isEnum ? AST::literal($status->type->getFullName() . '::DONE') : true;
                    return Map::new(
                        [
                            'longRunning' => AST::array([
                                'additionalArgumentMethods' => AST::array(
                                    $method->operationRequestFields
                                        ->values()
                                        ->map(fn ($x) => $x->getter->getName())->toArray()
                                ),
                                'getOperationMethod' => $method->operationPollingMethod->methodName,
                                'cancelOperationMethod' => $serviceDetails->hasCustomOpCancel ? 'cancel': AST::NULL,
                                'deleteOperationMethod' => $serviceDetails->hasCustomOpDelete ? 'delete': AST::NULL,
                                'operationErrorCodeMethod' => $errorCode->getter->getName(),
                                'operationErrorMessageMethod' => $errorMessage->getter->getName(),
                                'operationNameMethod' => $name->getter->getName(),
                                'operationStatusMethod' => $status->getter->getName(),
                                'operationStatusDoneValue' => $doneValue,
                            ])
                        ]
                    );
                case MethodDetails::LRO:
                    $methodGapicConfig = $gapicYamlConfig->configsByMethodName->get($method->name, null);
                    if (!is_null($methodGapicConfig) && isset($methodGapicConfig['long_running'])) {
                        $lroConfig = $methodGapicConfig['long_running'];
                        $initialPollDelayMillis = strval($lroConfig['initial_poll_delay_millis']);
                        $pollDelayMultiplier = strval($lroConfig['poll_delay_multiplier']);
                        $maxPollDelayMillis = strval($lroConfig['max_poll_delay_millis']);
                        $totalPollTimeoutMillis = strval($lroConfig['total_poll_timeout_millis']);
                    } else {
                        // Default LRO timings if not specified.
                        $initialPollDelayMillis = '500';
                        $pollDelayMultiplier = '1.5';
                        $maxPollDelayMillis = '5000';
                        $totalPollTimeoutMillis = '300000';
                    }
                    return Map::new(['longRunning' => AST::array([
                        'operationReturnType' => $method->lroResponseType->getFullname(),
                        'metadataReturnType' => $method->lroMetadataType->getFullname(),
                        'initialPollDelayMillis' => $initialPollDelayMillis,
                        'pollDelayMultiplier' => static::ensureDecimal($pollDelayMultiplier),
                        'maxPollDelayMillis' => $maxPollDelayMillis,
                        'totalPollTimeoutMillis' => $totalPollTimeoutMillis,
                    ])]);
                case MethodDetails::PAGINATED:
                    return Map::new(['pageStreaming' => AST::array([
                        'requestPageTokenGetMethod' => $method->requestPageTokenGetter->name,
                        'requestPageTokenSetMethod' => $method->requestPageTokenSetter->name,
                        'requestPageSizeGetMethod' => $method->requestPageSizeGetter->name,
                        'requestPageSizeSetMethod' => $method->requestPageSizeSetter->name,
                        'responsePageTokenGetMethod' => $method->responseNextPageTokenGetter->name,
                        'resourcesGetMethod' => $method->resourcesGetter->name,
                    ])]);
                case MethodDetails::BIDI_STREAMING:
                    return Map::new(['grpcStreaming' => AST::array([
                        'grpcStreamingType' => 'BidiStreaming',
                    ])]);
                case MethodDetails::SERVER_STREAMING:
                    return Map::new(['grpcStreaming' => AST::array([
                        'grpcStreamingType' => 'ServerStreaming',
                    ])]);
                case MethodDetails::CLIENT_STREAMING:
                    return Map::new(['grpcStreaming' => AST::array([
                        'grpcStreamingType' => 'ClientStreaming',
                    ])]);
                default:
                    return Map::new();
            }
        };

        $return = AST::return(
            AST::array([
                'interfaces' => AST::array([
                    $serviceDetails->serviceName => AST::array(
                        $serviceDetails->methods
                            ->map(fn ($x) => [$x->name, $perMethod($x)])
                            ->filter(fn ($x) => count($x[1]) > 0)
                            ->orderBy(fn ($x) => isset($x[1]['longRunning']) ? 0 : 1) // LRO come first
                            ->toArray(fn ($x) => $x[0], fn ($x) => AST::array($x[1]))
                    )
                ])
            ])
        );

        return "<?php\n\n{$return->toCode()};";
    }

    private static function restMethodDetails(ProtoCatalog $catalog, $methodOrMethodName, HttpRule $httpRule, bool $topLevel, ?string $defaultBody): Expression
    {
        $httpMethod = $httpRule->getPattern();
        $uriTemplateGetter = Helpers::toCamelCase("get_{$httpMethod}");
        $uriTemplate = $httpRule->$uriTemplateGetter();
        $body = $httpRule->getBody();
        $restBody = $body === '' ? $defaultBody : $body;
        $additionalBindings = Vector::new($httpRule->getAdditionalBindings());
        $queryParams = $methodOrMethodName instanceof MethodDetails ? self::getQueryParams($methodOrMethodName) : Vector::new([]);
        if ($topLevel) {
            // Merges plcaeholders for all bindings; ie includes additional bindings.
            $placeholders = $additionalBindings
                ->map(fn ($x) => ProtoHelpers::restPlaceholders($catalog, $x, null))
                ->append(ProtoHelpers::restPlaceholders($catalog, $httpRule, null))
                ->flatMap(fn ($x) => $x->mapValues(fn ($k, $v) => [$k, $v])->values())
                ->groupBy(fn ($x) => $x[0])
                ->mapValues(fn ($k, $v) => $v[0][1]);
        } else {
            $placeholders = Map::new();
        }

        $methodDetails = [
            'method' => $httpMethod,
            'uriTemplate' => $uriTemplate,
            'body' => $restBody,
            'additionalBindings' => !$additionalBindings->any() ? null :
                AST::array($additionalBindings->map(fn ($x) => static::restMethodDetails($catalog, $methodOrMethodName, $x, false, $restBody))->toArray()),
            'placeholders' => count($placeholders) === 0 ? null : AST::array(
                $placeholders
                    ->mapValues(fn ($k, $v) => [$k, AST::array(['getters' => AST::array($v->toArray())])])
                    ->values()
                    ->orderBy(fn ($x) => $x[0])
                    ->toArray(fn ($x) => $x[0], fn ($x) => $x[1])
            )
        ];
        if ($queryParams->count() > 0) {
            $methodDetails['queryParams'] = AST::array($queryParams->toArray());
        }

        return AST::array($methodDetails);
    }

    public static function generateRestConfig(ServiceDetails $serviceDetails, ServiceYamlConfig $serviceYamlConfig): string
    {
        $allInterfaces = static::compileRestConfigInterfaces($serviceDetails, $serviceYamlConfig);
        if ($serviceDetails->hasCustomOp) {
            $opService = $serviceDetails->customOperationService;
            $opFile = $serviceDetails->catalog->filesByService[$opService];
            $customOpDetails = new ServiceDetails(
                $serviceDetails->catalog,
                $serviceDetails->namespace,
                $opFile->getPackage(),
                $opService,
                $opFile,
                $serviceDetails->transportType
            );
            $opInter = static::compileRestConfigInterfaces($customOpDetails, $serviceYamlConfig);
            $allInterfaces = array_merge($allInterfaces, $opInter);
        }
        
        $return = AST::return(
            AST::array([
                'interfaces' => AST::array($allInterfaces)
            ])
        );
        return "<?php\n\n{$return->toCode()};";
    }

    private static function compileRestConfigInterfaces(ServiceDetails $serviceDetails, ServiceYamlConfig $serviceYamlConfig)
    {
        return $serviceDetails->methods
            ->filter(fn ($method) => !is_null($method->httpRule) && !$method->isStreaming() && !$method->isMixin())
            ->map(fn ($method) => [$serviceDetails->serviceName, $method, $method->httpRule])
            ->concat($serviceYamlConfig->httpRules->map(fn ($x) => [
                Vector::new(explode('.', $x->getSelector()))->skipLast(1)->join('.'),
                Vector::new(explode('.', $x->getSelector()))->last(),
                $x
            ])) // [service name, method, httpRule]
            ->groupBy(fn ($x) => $x[0])
            ->mapValues(fn ($k, $v) => [$k, $v])
            ->values()
            ->orderBy(fn ($x) => $x[0]) // order by service name
            ->toArray(fn ($x) => $x[0], fn ($x) => AST::array($x[1]->toArray(
                // Weird edge case that sometimes occurs for LROs, e.g. on Retail.
                fn ($y) => $y[1] instanceof MethodDetails ? $y[1]->name : $y[1],
                fn ($y) => static::restMethodDetails($serviceDetails->catalog, $y[1], $y[2], true, null),
            )));
    }

    public static function generateClientConfig(
        ServiceDetails $serviceDetails,
        GapicYamlConfig $gapicYamlConfig,
        GrpcServiceConfig $grpcServiceConfig
    ): string {
        $serviceName = $serviceDetails->serviceName;
        $durationToMillis = fn ($d) => (int)($d->getSeconds() * 1000 + $d->getNanos() / 1e6);

        if ($grpcServiceConfig->isPresent) {
            $configsByMethodName = Map::new();
            $retryCodes = Vector::new([
                ['no_retry_codes', []]
            ]);
            $retryParams = Vector::new([
                ['no_retry_params', [
                    'initial_retry_delay_millis' => 0,
                    'retry_delay_multiplier' => 0,
                    'max_retry_delay_millis' => 0,
                    'initial_rpc_timeout_millis' => 0,
                    'rpc_timeout_multiplier' => 1.0,
                    'max_rpc_timeout_millis' => 0,
                    'total_timeout_millis' => 0,
                ]]
            ]);
            $retryIndex = 1;
            $noRetryIndex = 1;
            foreach ($grpcServiceConfig->methods as $method) {
                $policyName = $method->getRetryOrHedgingPolicy() === 'retry_policy' ? 'retry_policy_' . $retryIndex++ : 'no_retry_' . $noRetryIndex++;
                if (Vector::new($method->getName())->any(fn ($x) => substr($x->getService(), 0, strlen($serviceName)) === $serviceName)) {
                    $codesName = "{$policyName}_codes";
                    $paramsName = "{$policyName}_params";
                    $policy = $method->getRetryPolicy();
                    $timeout = $method->hasTimeout() ? $durationToMillis($method->getTimeout()) : null;
                    $retryCodes = $retryCodes->append([
                        $codesName,
                        Vector::new(is_null($policy) ? [] : $policy->getRetryableStatusCodes())->map(fn ($x) => Code::name($x))->toArray()
                    ]);
                    $retryParams = $retryParams->append([
                        $paramsName, Vector::new([
                            ['initial_retry_delay_millis', is_null($policy) ? 0 : $durationToMillis($policy->getInitialBackoff())],
                            ['retry_delay_multiplier', is_null($policy) ? 0 : round($policy->getBackoffMultiplier(), 5)],
                            ['max_retry_delay_millis', is_null($policy) ? 0 : $durationToMillis($policy->getMaxBackoff())],
                            ['initial_rpc_timeout_millis', $timeout],
                            ['rpc_timeout_multiplier', 1.0],
                            ['max_rpc_timeout_millis', $timeout],
                            ['total_timeout_millis', $timeout],
                        ])->filter(fn ($x) => !is_null($x[1]))->toArray(fn ($x) => $x[0], fn ($x) => $x[1])
                    ]);
                    foreach ($method->getName() as $name) {
                        $fullName = "{$name->getService()}/{$name->getMethod()}";
                        $configsByMethodName = $configsByMethodName->set($fullName, [$codesName, $paramsName, $timeout]);
                    }
                }
            }
        } else {
            $retryCodes = Vector::new([
                ['idempotent', ['DEADLINE_EXCEEDED', 'UNAVAILABLE']],
                ['non_idempotent', []]
            ]);
            $retryParams = Vector::new([
                ['default', [
                    'initial_retry_delay_millis' => 100,
                    'retry_delay_multiplier' => 1.3,
                    'max_retry_delay_millis' => 60_000,
                    'initial_rpc_timeout_millis' => 20_000,
                    'rpc_timeout_multiplier' => 1.0,
                    'max_rpc_timeout_millis' => 20_000,
                    'total_timeout_millis' => 600_000,
            ]]]);
            $configsByMethodName = Map::new();
        }

        $retryCodes = $retryCodes->toArray(fn ($x) => $x[0], fn ($x) => $x[1]);
        $retryParams = $retryParams->toArray(fn ($x) => $x[0], fn ($x) => $x[1]);
        $methods = [];
        $methods = $serviceDetails->methods
            ->map(function ($method) use ($gapicYamlConfig, $grpcServiceConfig, $configsByMethodName, $serviceName) {
                [$codes, $params, $timeout] = $configsByMethodName->get("{$serviceName}/{$method->name}", null) ??
                    $configsByMethodName->get("{$serviceName}/", [null, null, null]);
                if (is_null($codes)) {
                    $timeoutMillis = 60_000;
                    if ($method->isStreaming()) {
                        $retryCodesName = null;
                        $retryParamsName = null;
                    } else {
                        $retryCodesName = $grpcServiceConfig->isPresent ? 'no_retry_codes' : ($method->restMethod === 'get' ? 'idempotent' : 'non_idempotent');
                        $retryParamsName = $grpcServiceConfig->isPresent ? 'no_retry_params' : 'default';
                    }
                } else {
                    $timeoutMillis = $timeout ?? 60_000;
                    if ($method->isStreaming()) {
                        $retryCodesName = null;
                        $retryParamsName = null;
                    } else {
                        $retryCodesName = $codes;
                        $retryParamsName = $params;
                    }
                }
                $methodClientConfig = Vector::new([
                    ['timeout_millis', $timeoutMillis],
                    ['retry_codes_name', $retryCodesName],
                    ['retry_params_name', $retryParamsName]
                ]);
                // Handles batching settings in gapic.yaml.
                if ($gapicYamlConfig !== null
                  && $gapicYamlConfig->configsByMethodName->get($method->name, null)) {
                    $methodGapicConfig = $gapicYamlConfig->configsByMethodName->get($method->name, null);
                    if (isset($methodGapicConfig['batching'])
                        && isset($methodGapicConfig['batching']['thresholds'])) {
                        $batchingGapicConfig = $methodGapicConfig['batching']['thresholds'];
                        $batchingGapicKeys = [
                            'element_count_threshold',
                            'request_byte_threshold',
                            'delay_threshold_millis',
                            'request_byte_threshold',
                            'request_byte_limit'
                        ];
                        $batchingConfig = [];
                        foreach ($batchingGapicKeys as $k) {
                            if (isset($batchingGapicConfig[$k])) {
                                $batchingConfig[$k] = $batchingGapicConfig[$k];
                            }
                        }
                        if (!empty($batchingConfig)) {
                            ksort($batchingConfig);
                            $methodClientConfig = $methodClientConfig->append(['bundling', $batchingConfig]);
                        }
                    }
                }

                return [
                  $method->name,
                  $methodClientConfig
                    ->filter(fn ($x) => !is_null($x[1]))
                    ->toArray(fn ($x) => $x[0], fn ($x) => $x[1])
                ];
            })
            ->toArray(fn ($x) => $x[0], fn ($x) => $x[1]);

        $json = [
            'interfaces' => [
                $serviceDetails->serviceName => [
                    'retry_codes' => $retryCodes,
                    'retry_params' => $retryParams,
                    'methods' => $methods,
                ]
            ]
        ];

        $json = json_encode($json, JSON_PRETTY_PRINT) . "\n";
        // TODO(vNext): Remove this post-processing.
        $json = static::jsonPostProcess($json);
        return $json;
    }

    private static function jsonPostProcess(string $json): string
    {
        // Force multplier values to have a ".0" if no decimal point present, required for monolith compatibility.
        return Vector::new(explode("\n", $json))
            ->map(function ($line) {
                if (strpos($line, 'multiplier') !== false) {
                    $parts = explode(':', $line);
                    if (count($parts) === 2 && strpos($parts[1], '.') === false) {
                        return substr($line, 0, -1) . '.0,';
                    }
                }
                return $line;
            })
            ->join("\n");
    }

    private static function getQueryParams(MethodDetails $method): Vector
    {
        $httpRule = $method->httpRule;
        $segments = Vector::new([]);
        if ($method->httpRule->getBody() === '*') {
            return $segments;
        }
        // Find all the query params - those that are not part of the REST path but are required
        // fields in the message request.
        $httpMethod = $httpRule->getPattern();
        $uriTemplateGetter = Helpers::toCamelCase("get_{$httpMethod}");
        $uriTemplate = $httpRule->$uriTemplateGetter();
        // Parse the uriTemplate. Just do a best-effort pass at this, since it doesn't hit
        // GCE / DIREGAPIC and OnePlatform GAPICs are fine without this.
        try {
            $rawSegments = Parser::parseSegments(substr($uriTemplate, 1));
            ;
        } catch (\Google\ApiCore\ValidationException $e) {
            // Ignore.
            return $segments;
        }
        $segments = Vector::new($rawSegments);
        $varSegments = $segments
            ->filter(fn ($x) => $x->getSegmentType() === Segment::VARIABLE_SEGMENT)
            ->map(fn ($x) => $x->getKey());
        // Handle singleton resources. Assumes the singleton always resides at the end of a pattern.
        $tokens = explode("/", $uriTemplate);
        $nameSegments = $varSegments;
        if (substr(end($tokens), 0, 1) !== "{" && substr($uriTemplate, strlen($uriTemplate) - 1) !== "}") {
            $nameSegments = $nameSegments->append(end($tokens));
        }
        // Match the name segments against the required fields in the method.
        // TODO(vNext): Handle oneofs, which isn't currently exercised in query params in GCE.
        $queryParams =
            $method->requiredFields
                ->filter(fn ($f) => $f->name !== $httpRule->getBody() && !$nameSegments->contains($f->name))
                ->map(fn ($f) => $f->name);
        return $queryParams;
    }
}
