<?php
/*
 * Copyright 2025 Google LLC
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

class PaginationExceptions
{
    /**
     * This is a list of exceptions for the pagination heuristics.
     * If there is a proto that has multiple repeated fields and we want to
     * manually override which of the repeated fields is the one should be paginated on
     * should be added to this list
     */
    private static array $exceptions = [
        'UsableSubnetworksAggregatedList' => 'items',
        'ExceptionResponse' => 'the_results'
    ];

    /**
     * Returns the entire array of exceptions for paginated fields.
     *
     * @return array
     */
    public static function getExceptions(): array
    {
        return self::$exceptions;
    }

    /**
     * Returns the corresponding pagination field if exists in the exceptions array.
     * Returns null otherwise.
     *
     * @param string $name
     * @return null|string
     */
    public static function getException(string $name): null|string
    {
        return self::$exceptions[$name] ?? null;
    }

    /**
     * Checks if the given field is contained in the exceptions array.
     *
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return isset(self::$exceptions[$name]);
    }
}