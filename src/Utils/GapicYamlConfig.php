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
use Symfony\Component\Yaml\Yaml;

class GapicYamlConfig
{
    public function __construct(string $serviceName, ?string $gapicYaml)
    {
        // TODO: Refactor to use proto, rather than directly reading yaml.
        $this->configsByMethodName = Map::new([]);
        $this->orderByMethodName = Map::new([]);
        if (!is_null($gapicYaml)) {
            $gapic = Yaml::parse($gapicYaml);
            if (isset($gapic['interfaces'])) {
                $methods = Vector::new($gapic['interfaces'])
                    ->filter(fn($x) => $x['name'] === $serviceName)
                    ->flatMap(fn($x) => Vector::new($x['methods']));
                $this->configsByMethodName = $methods
                    ->toMap(fn($x) => $x['name']);
                $this->orderByMethodName = $methods
                    ->map(fn($x, $i) => [$i, $x])
                    ->toMap(fn($x) => $x[1]['name'], fn($x) => $x[0]);
            }
        }
    }

    /** @var Map *Readonly* Map of method-name to gapic-yaml config for the method. */
    public Map $configsByMethodName;

    /** @var Map *Readonly* Map of method-name of ordering within the gapic-yaml config file. */
    public Map $orderByMethodName;
}
