<?php
/*
 * Copyright 2023 Google LLC
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

class MigrationMode
{
    public const MIGRATION_MODE_UNSPECIFIED = "MIGRATION_MODE_UNSPECIFIED";
    public const NEW_SURFACE_ONLY = "NEW_SURFACE_ONLY";
    public const MIGRATING = "MIGRATING";

    /**
     * Validates the supplied migration mode.
     *
     * @param string $mode Migration mode to validate
     */
    public static function validateMode(string $mode): void
    {
        $invalid = $mode != self::MIGRATION_MODE_UNSPECIFIED
            && $mode != self::NEW_SURFACE_ONLY
            && $mode != self::MIGRATING;
        if ($invalid) {
            throw new \Exception("Invalid migration mode '{$mode}', allowed values are: 'MIGRATION_MODE_UNSPECIFIED', 'NEW_SURFACE_ONLY', 'MIGRATING'");
        }
    }
}
