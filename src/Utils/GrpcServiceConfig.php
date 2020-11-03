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

namespace Google\Generator\Utils;

use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Grpc\Service_config\ServiceConfig;

class GrpcServiceConfig
{
    public function __construct(string $serviceName, ?string $json)
    {
        if (is_null($json)) {
            $this->configsByName = Map::new();
            $this->retryPolicies = Vector::new([]);
            $this->timeouts = Vector::new([]);
        } else {
            $config = new ServiceConfig();
            $config->mergeFromJsonString($json);
            $methods = Vector::new($config->getMethodConfig())
                ->filter(fn($x) => Vector::new($x->getName())->any(fn($x) => $x->getService() === $serviceName));
            $this->configsByName = $methods
                ->flatMap(fn($conf, $index) => Vector::new($conf->getName())->map(fn($name) => [$name, $index]))
                ->toMap(fn($x) => "{$x[0]->getService()}/{$x[0]->getMethod()}", fn($x) => $x[1]);
            $this->retryPolicies = $methods
                ->map(fn($x) => $x->getRetryPolicy());
            $this->timeouts = $methods
                ->map(fn($x) => $x->getTimeout());
        }
    }

    /** @var Map *Readonly* Map<string, \Grpc\Service_config\MethodConfig>; name to indexes. */
    public Map $configsByName;

    /** @var Vector *Readonly* Vector<\Grpc\Service_config\MethodConfig\RetryPolicy> */
    public Vector $retryPolicies;

    /** @var Vector *Readonly* Vector<\Google\Protobuf\Duration> */
    public Vector $timeouts;
}
