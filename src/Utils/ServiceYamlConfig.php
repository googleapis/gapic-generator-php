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
use Google\Generator\Collections\Vector;
use Symfony\Component\Yaml\Yaml;

class ServiceYamlConfig
{
    private static function isWhitespace(string $c): bool
    {
        return $c === ' ' || $c === "\t";
    }

    private static function fixYaml(string $yaml): string
    {
        // The PHP YAML parser fails on a multi-line string which is not indented.
        // This is valid YAML, so fix it up here.
        // This is currently required for the cloud-functions API.
        $result = '';
        $fixNextLine = false;
        foreach (explode("\n", $yaml) as $line) {
            if ($fixNextLine) {
                if (strlen($line) > 0 && !static::isWhitespace(substr($line, 0, 1))) {
                    $result .= '  ' . $line . "\n";
                    continue;
                }
            }
            $fixNextLine = false;
            $result .= $line . "\n";
            if (strlen($line) > 0 && static::isWhitespace(substr($line, 0, 1))) {
                $fixNextLine = true;
            }
        }
        return $result;
    }

    public function __construct(?string $serviceYaml)
    {
        $this->httpRules = Vector::new([]);
        $this->documentationRules = Vector::new([]);
        $this->backendRules = Vector::new([]);
        $this->apiNames = Vector::new([]);
        if (!is_null($serviceYaml)) {
            $service = new Service();
            $serviceYaml = static::fixYaml($serviceYaml);
            $service->mergeFromJsonString(json_encode(Yaml::parse($serviceYaml)));
            $http = $service->getHttp();
            if (!is_null($http)) {
                $this->httpRules = Vector::new($http->getRules());
            }
            $documentation = $service->getDocumentation();
            if (!is_null($documentation)) {
                $this->documentationRules = Vector::new($documentation->getRules());
            }
            $backend = $service->getBackend();
            if (!is_null($backend)) {
                $rules = $backend->getRules();
                if (!is_null($rules)) {
                    $this->backendRules = Vector::new($rules);
                }
            }
            $apis = $service->getApis();
            if (!is_null($apis)) {
                $this->apiNames = Vector::new($apis)->map(fn ($a) => $a->getName());
            }
        }
    }

    /** @var Vector *Readonly* Vector of \Google\Api\HttpRule */
    public Vector $httpRules;

    /** @var Vector *Readonly* Vector of \Google\Api\DocumentationRule */
    public Vector $documentationRules;

    /** @var Vector *Readonly* Vector of \Google\Api\BackendRule */
    public Vector $backendRules;

    /** @var Vector *Readonly* Vector of API names. */
    public Vector $apiNames;
}
