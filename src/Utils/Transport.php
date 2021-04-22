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

class Transport
{
    // Consts are used instead of booleans, because there more transport options may be
    // supported in the future (e.g. gRPC only).
    public const GRPC_REST = 1;
    public const REST = 2;

    /**
     *  Returns true if the given transport string indicates that grpc+rest transports
     *  should be supported, false otherwise.
     */
    public static function parseTransport(?string $transport): int
    {
        if (is_null($transport) || $transport === "grpc+rest") {
            return static::GRPC_REST;
        }
        if ($transport === "rest") {
            return static::REST;
        }
        if ($transport === "grpc") {
            throw new \Exception("gRPC-only PHP clients are not supported at this time");
        }

        throw new \Exception("Transport $transport not supported");
    }
}
