<?php
/*
 * Copyright 2021 Google LLC
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

namespace Google\Generator\Tests\Unit;

use Google\Generator\Collections\Vector;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorSet;

trait ConfigTrait
{
    /**
     * Load a config file from the specified path, which must be relative to the `tests/Unit` directory.
     *
     * @param string $filePath The config file path relative to the `tests/Unit` directory.
     *
     * @return string|null the file contents if the config file exists, null otherwise.
     */
    function loadConfig(string $filePath): ?string
    {
        $fullPath = "tests/Unit/$filePath";
        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }
}
