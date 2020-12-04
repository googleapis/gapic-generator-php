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

use Google\Generator\Ast\AST;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\GapicYamlConfig;
use Google\Generator\Utils\GrpcServiceConfig;
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
                            ->toArray(fn($x) => $x[0], fn($x) => AST::array($x[1]))
                    )
                ])
            ])
        );

        return "<?php\n\n{$return->toCode()};";
    }

    public static function generateRestConfig(ServiceDetails $serviceDetails): string
    {
        $serviceContent = $serviceDetails->methods
            ->filter(fn($method) => !is_null($method->restMethod))
            ->toMap(
                fn($method) => $method->name,
                fn($method) => AST::array([
                    'method' => $method->restMethod,
                    'uriTemplate' => $method->restUriTemplate,
                    'body' => $method->restBody,
                    'placeholders' => count($method->restRoutingHeaders) === 0 ? null : AST::array(
                        $method->restRoutingHeaders
                            ->mapValues(fn($k, $v) => [$k, AST::array(['getters' => AST::array($v->toArray())])])
                            ->values()
                            ->orderBy(fn($x) => $x[0])
                            ->toArray(fn($x) => $x[0], fn($x) => $x[1])
                    )
                ])
            );
        $return = AST::return(
            AST::array([
                'interfaces' => AST::array([
                    $serviceDetails->serviceName => count($serviceContent) === 0 ? null : AST::array($serviceContent)
                ])
            ])
        );
        return "<?php\n\n{$return->toCode()};";
    }

    public static function generateClientConfig(ServiceDetails $serviceDetails, GrpcServiceConfig $grpcServiceConfig): string
    {
        $inc = fn($i) => $i + 1;
        $durationToMillis = fn($d) => (int)($d->getSeconds() * 1000 + $d->getNanos() / 1e6);

        $retryCodes = $grpcServiceConfig->retryPolicies
            ->map(fn($x, $i) => ["retry_policy_{$inc($i)}_codes", is_null($x) ? null :
                Vector::new($x->getRetryableStatusCodes())->map(fn($x) => Code::name($x))->toArray()])
            ->filter(fn($x) => !is_null($x[1]))
            ->append(['idempotent', ['DEADLINE_EXCEEDED', 'UNAVAILABLE']])
            ->append(['non_idempotent', []])
            ->toArray(fn($x) => $x[0], fn($x) => $x[1]);

        $retryParams = Vector::zip($grpcServiceConfig->retryPolicies, $grpcServiceConfig->timeouts, fn($r, $t, $i) =>
            ["retry_policy_{$inc($i)}_params", is_null($r) && is_null($t) ? null :
                Vector::new([
                    ['initial_retry_delay_millis', is_null($r) ? null : $durationToMillis($r->getInitialBackoff())],
                    ['retry_delay_multiplier', is_null($r) ? null : $r->getBackoffMultiplier()],
                    ['max_retry_delay_millis', is_null($r) ? null : $durationToMillis($r->getMaxBackoff())],
                    ['initial_rpc_timeout_millis', is_null($t) ? null : $durationToMillis($t)],
                    ['rpc_timeout_multiplier', 1.0],
                    ['max_rpc_timeout_millis', is_null($t) ? null : $durationToMillis($t)],
                    ['total_timeout_millis', is_null($t) ? null : $durationToMillis($t)],
                ])->filter(fn($x) => !is_null($x[1]))->toArray(fn($x) => $x[0], fn($x) => $x[1])
            ])
            ->filter(fn($x) => !is_null($x[1]))
            ->append(['default', [
                'initial_retry_delay_millis' => 100,
                'retry_delay_multiplier' => 1.3,
                'max_retry_delay_millis' => 60_000,
                'initial_rpc_timeout_millis' => 20_000,
                'rpc_timeout_multiplier' => 1.0,
                'max_rpc_timeout_millis' => 20_000,
                'total_timeout_millis' => 600_000,
            ]])
            ->toArray(fn($x) => $x[0], fn($x) => $x[1]);

        $serviceName = $serviceDetails->serviceName;
        $methods = $serviceDetails->methods
            ->map(function($method) use($grpcServiceConfig, $serviceName, $durationToMillis, $inc) {
                $index = $grpcServiceConfig->configsByName->get("{$serviceName}/{$method->name}", null) ??
                    $grpcServiceConfig->configsByName->get("{$serviceName}/", null);
                if (is_null($index)) {
                    return [
                        $method->name,
                        ['timeout_millis' => 60_000,
                    ] + ($method->isStreaming() ? [] : [
                        'retry_codes_name' => $method->restMethod === 'get' ? 'idempotent' : 'non_idempotent',
                        'retry_params_name' => 'default',
                    ])];
                } else {
                    return [
                        $method->name,
                        ['timeout_millis' => $durationToMillis($grpcServiceConfig->timeouts[$index])
                    ] + ($method->isStreaming() ? [] : [
                        'retry_codes_name' => "retry_policy_{$inc($index)}_params",
                        'retry_params_name' => "retry_policy_{$inc($index)}_codes",
                    ])];
                }
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
