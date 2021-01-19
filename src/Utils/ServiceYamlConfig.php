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

use Google\Api\Service;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Symfony\Component\Yaml\Yaml;

class ServiceYamlConfig
{
    public function __construct(?string $serviceYaml)
    {
        $this->httpRules = Vector::new([]);
        $this->backendRules = Vector::new([]);
        if (!is_null($serviceYaml)) {
            $service = new Service();
            $service->mergeFromJsonString(json_encode(Yaml::parse($serviceYaml)));
            $http = $service->getHttp();
            if (!is_null($http)) {
                $this->httpRules = Vector::new($http->getRules());
            }
            $backend = $service->getBackend();
            if (!is_null($backend)) {
                $rules = $backend->getRules();
                if (!is_null($rules)) {
                    $this->backendRules = Vector::new($rules);
                }
            }
        }
    }

    /** @var Vector *Readonly* Vector of \Google\Api\HttpRule */
    public Vector $httpRules;

    /** @var Vector *Readonly* Vector of \Google\Api\BackendRule */
    public Vector $backendRules;
}
