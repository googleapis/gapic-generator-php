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
        if (is_null($gapicYaml)) {
            $this->configsByMethodName = Map::new([]);
        } else {
            $gapic = Yaml::parse($gapicYaml);
            if (isset($gapic['interfaces'])) {
                $this->configsByMethodName = Vector::new($gapic['interfaces'])
                    ->filter(fn($x) => $x['name'] === $serviceName)
                    ->flatMap(fn($x) => Vector::new($x['methods']))
                    ->toMap(fn($x) => $x['name']);
            } else {
                $this->configsByMethodName = Map::new([]);
            }
        }
    }

    /** @var Map *Readonly* Map of method-name to gapic-yaml config for the method. */
    public Map $configsByMethodName;
}
