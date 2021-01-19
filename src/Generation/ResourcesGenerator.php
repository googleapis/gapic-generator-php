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
    public static function generateDescriptorConfig(ServiceDetails $serviceDetails, GapicYamlConfig $gapicYamlConfig): string
    {
        $perMethod = function($method) use ($gapicYamlConfig) {
            switch ($method->methodType) {
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
                        'pollDelayMultiplier' => $pollDelayMultiplier,
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
                            ->map(fn($x) => [$x->name, $perMethod($x)])
                            ->filter(fn($x) => count($x[1]) > 0)
                            ->orderBy(fn($x) => isset($x[1]['longRunning']) ? 0 : 1) // LRO come first
                            ->toArray(fn($x) => $x[0], fn($x) => AST::array($x[1]))
                    )
                ])
            ])
        );

        return "<?php\n\n{$return->toCode()};";
    }

    private static function restMethodDetails(ProtoCatalog $catalog, HttpRule $httpRule, bool $topLevel, ?string $defaultBody): Expression
    {
        $httpMethod = $httpRule->getPattern();
        $uriTemplateGetter = Helpers::toCamelCase("get_{$httpMethod}");
        $uriTemplate = $httpRule->$uriTemplateGetter();
        $body = $httpRule->getBody();
        $restBody = $body === '' ? $defaultBody : $body;
        $additionalBindings = Vector::new($httpRule->getAdditionalBindings());
        if ($topLevel) {
            // Merges plcaeholders for all bindings; ie includes additional bindings.
            $placeholders = $additionalBindings
                ->map(fn($x) => ProtoHelpers::restPlaceholders($catalog, $x, null))
                ->append(ProtoHelpers::restPlaceholders($catalog, $httpRule, null))
                ->flatMap(fn($x) => $x->mapValues(fn($k, $v) => [$k, $v])->values())
                ->groupBy(fn($x) => $x[0])
                ->mapValues(fn($k, $v) => $v[0][1]);
        } else {
            $placeholders = Map::new();
        }
        return AST::array([
            'method' => $httpMethod,
            'uriTemplate' => $uriTemplate,
            'body' => $restBody,
            'additionalBindings' => !$additionalBindings->any() ? null :
                AST::array($additionalBindings->map(fn($x) => static::restMethodDetails($catalog, $x, false, $restBody))->toArray()),
            'placeholders' => count($placeholders) === 0 ? null : AST::array(
                $placeholders
                    ->mapValues(fn($k, $v) => [$k, AST::array(['getters' => AST::array($v->toArray())])])
                    ->values()
                    ->orderBy(fn($x) => $x[0])
                    ->toArray(fn($x) => $x[0], fn($x) => $x[1])
            )
        ]);
    }

    public static function generateRestConfig(ServiceDetails $serviceDetails, ServiceYamlConfig $serviceYamlConfig): string
    {
        $allInterfaces = $serviceDetails->methods
            ->filter(fn($method) => !is_null($method->httpRule))
            ->map(fn($method) => [$serviceDetails->serviceName, $method->name, $method->httpRule])
            ->concat($serviceYamlConfig->httpRules->map(fn($x) => [
                Vector::new(explode('.', $x->getSelector()))->skipLast(1)->join('.'),
                Vector::new(explode('.', $x->getSelector()))->last(),
                $x
            ])) // [service name, method name, httpRule]
            ->groupBy(fn($x) => $x[0])
            ->mapValues(fn($k, $v) => [$k, $v])
            ->values()
            ->orderBy(fn($x) => $x[0]) // order by service name
            ->toArray(fn($x) => $x[0], fn($x) => AST::array($x[1]->toArray(
                fn($y) => $y[1],
                fn($y) => static::restMethodDetails($serviceDetails->catalog, $y[2], true, null),
            )));
        $return = AST::return(
            AST::array([
                'interfaces' => AST::array($allInterfaces)
            ])
        );
        return "<?php\n\n{$return->toCode()};";
    }

    public static function generateClientConfig(
        ServiceDetails $serviceDetails,
        GrpcServiceConfig $grpcServiceConfig,
        ServiceYamlConfig $serviceYamlConfig,
        GapicYamlConfig $gapicYamlConfig
    ): string {
        $serviceName = $serviceDetails->serviceName;
        $durationToMillis = fn($d) => (int)($d->getSeconds() * 1000 + $d->getNanos() / 1e6);

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
                // TODO(vNext): This way to check if this specific policy needs to be included is not quite right,
                // but reproduces monolith behaviour. This code will incorrectly include services which are named with a common
                // prefix with the current service.
                // It also unnessecarily includes irrelevant information if the service is not listed in the gapic config.
                if (!isset($gapicYamlConfig->interfaces[$serviceName]) ||
                        Vector::new($method->getName())->any(fn($x) => substr($x->getService(), 0, strlen($serviceName)) === $serviceName)) {
                    $codesName = "{$policyName}_codes";
                    $paramsName = "{$policyName}_params";
                    $policy = $method->getRetryPolicy();
                    $timeout = $method->hasTimeout() ? $durationToMillis($method->getTimeout()) : null;
                    $retryCodes = $retryCodes->append([
                        $codesName,
                        Vector::new(is_null($policy) ? [] : $policy->getRetryableStatusCodes())->map(fn($x) => Code::name($x))->toArray()
                    ]);
                    $retryParams = $retryParams->append([
                        $paramsName, Vector::new([
                            ['initial_retry_delay_millis', is_null($policy) ? 0 : $durationToMillis($policy->getInitialBackoff())],
                            ['retry_delay_multiplier', is_null($policy) ? 0 : $policy->getBackoffMultiplier()],
                            ['max_retry_delay_millis', is_null($policy) ? 0 : $durationToMillis($policy->getMaxBackoff())],
                            ['initial_rpc_timeout_millis', $timeout],
                            ['rpc_timeout_multiplier', 1.0],
                            ['max_rpc_timeout_millis', $timeout],
                            ['total_timeout_millis', $timeout],
                        ])->filter(fn($x) => !is_null($x[1]))->toArray(fn($x) => $x[0], fn($x) => $x[1])
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

        $retryCodes = $retryCodes->toArray(fn($x) => $x[0], fn($x) => $x[1]);
        $retryParams = $retryParams->toArray(fn($x) => $x[0], fn($x) => $x[1]);
        $methods = [];
        $serviceYamlBackendRules = $serviceYamlConfig->backendRules
            ->flatMap(fn($x) => Vector::new(explode(',', $x->getSelector()))->map(fn($y) => [trim($y), $x]))
            ->toMap(fn($x) => $x[0], fn($x) => $x[1]);
        $methods = $serviceDetails->methods
            ->map(function($method) use($grpcServiceConfig, $configsByMethodName, $serviceName, $serviceYamlBackendRules) {
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
                // A timeout in service config yaml overrides timeout in grpc service config.
                // Note that this monolith-compatible behaviour is broken, as it doesn't handle wildcard selectors at all.
                // TODO(vNext): Remove this override.
                $rule = $serviceYamlBackendRules->get("{$serviceName}.{$method->name}", null);
                if (!is_null($rule)){
                    $timeoutMillis = $rule->getDeadline() * 1000;
                }
                return [$method->name, Vector::new([
                    ['timeout_millis', $timeoutMillis],
                    ['retry_codes_name', $retryCodesName],
                    ['retry_params_name', $retryParamsName]
                ])->filter(fn($x) => !is_null($x[1]))->toArray(fn($x) => $x[0], fn($x) => $x[1])];
            })
            ->toArray(fn($x) => $x[0], fn($x) => $x[1]);

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
            ->map(function($line) {
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
}
