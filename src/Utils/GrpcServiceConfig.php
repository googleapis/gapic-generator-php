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

use Google\Generator\Collections\Vector;
use Grpc\Service_config\ServiceConfig;

class GrpcServiceConfig
{
    public function __construct(?string $json)
    {
        if (is_null($json)) {
            $this->isPresent = false;
            $this->methods = Vector::new([]);
        } else {
            $this->isPresent = true;
            $config = new ServiceConfig();
            $config->mergeFromJsonString($json, /* ignore_unknown */ true);
            $this->methods = Vector::new($config->getMethodConfig());
        }
    }

    /** @var bool *Readonly* Whether a grpc-service-config is present at all. */
    public bool $isPresent;

    /** @var Vector *Readonly* Vector<\Grpc\Service_config\MethodConfig>; all methods' service configs. */
    public Vector $methods;
}
