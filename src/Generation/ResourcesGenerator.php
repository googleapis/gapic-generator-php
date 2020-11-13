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
use Google\Generator\Utils\GrpcServiceConfig;
use Google\Rpc\Code;

class ResourcesGenerator
{
    public static function generateDescriptorConfig(ServiceDetails $serviceDetails): string
    {
        $perMethod = function($method) {
            switch ($method->methodType) {
                case MethodDetails::LRO:
                    return Map::new(['longRunning' => AST::array([
                        'operationReturnType' => $method->lroResponseType->getFullname(),
                        'metadataReturnType' => $method->lroMetadataType->getFullname(),
                        'initialPollDelayMillis' => '60000', // TODO: Check these are the correct values.
                        'pollDelayMultiplier' => '1.0',
                        'maxPollDelayMillis' => '60000',
                        'totalPollTimeoutMillis' => '86400000',
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
                            ->toMap(fn($x) => $x[0], fn($x) => AST::array($x[1]))
                    )
                ])
            ])
        );

        return "<?php\n\n{$return->toCode()};";
    }

    public static function generateRestConfig(ServiceDetails $serviceDetails): string
    {
        $return = AST::return(
            AST::array([
                'interfaces' => AST::array([
                    $serviceDetails->serviceName => AST::array(
                        $serviceDetails->methods
                            ->filter(fn($method) => !is_null($method->restMethod))
                            ->toMap(
                                fn($method) => $method->name,
                                fn($method) => AST::array([
                                    'method' => $method->restMethod,
                                    'uriTemplate' => $method->restUriTemplate,
                                    'body' => $method->restBody,
                                ])
                            )
                    )
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
                // TODO: Check the default 'timeoutMillis' if it's not specified; currently 0, but this may not be correct.
                return [$method->name, is_null($index) ? [
                    'timeout_millis' => 60_000,
                    'retry_codes_name' => 'non_idempotent',
                    'retry_params_name' => 'default',
                ] : [
                    'timeout_millis' => $durationToMillis($grpcServiceConfig->timeouts[$index]),
                    'retry_codes_name' => "retry_policy_{$inc($index)}_params",
                    'retry_params_name' => "retry_policy_{$inc($index)}_codes",
                ]];
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
