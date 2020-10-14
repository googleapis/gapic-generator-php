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

namespace Google\Generator\Tests;

use Google\Protobuf\Internal\Message;

/**
 * Fake message base class for tests, that emulates any proto message well enough for testing.
 */
abstract class FakeMessage extends Message
{
    public function __construct() { }

    public function __call(string $name, array $arguments)
    {
        // Implement all getters and setters; to emulate a proto message class.
        // Note: This is fine if $name is shorter than 3 chars, as subtr returns a truncated or empty string.
        $prefix = substr($name, 0, 3);
        $suffix = substr($name, 3);
        switch ($prefix) {
            case 'set':
                $this->$suffix = $arguments[0];
                return;
            case 'get':
                return $this->$suffix;
            default:
                throw new \Exception("FakeMessage cannot handle method call: '{$name}'");
        }
    }
}
