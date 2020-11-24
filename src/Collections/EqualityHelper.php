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

namespace Google\Generator\Collections;

trait EqualityHelper
{
    private static function hash($k): int
    {
        if (is_int($k)) {
            return $k;
        } elseif (is_string($k)) {
            return crc32($k);
        } elseif (is_object($k)) {
            if ($k instanceof Equality) {
                return $k->getHash();
            } else {
                return spl_object_id($k);
            }
        }
        throw new \Exception("Cannot use a map key of type: '" . gettype($k) . "'");
    }

    private static function equal($a, $b): bool
    {
        if ($a === $b) {
            // Handles int, string
            return true;
        } elseif (is_object($a) && is_object($b) && get_class($a) === get_class($b) && $a instanceof Equality) {
            return $a->isEqualTo($b);
        }
        return false;
    }

    private static function compare($a, $b): int
    {
        // Returns -ve if $a < $b; +ve if $a > $b; 0 if equal.
        if (is_string($a) && is_string($b)) {
            return $a <=> $b;
        } elseif(is_int($a) && is_int($b)) {
            return $a <=> $b;
        }
        throw new \Exception('Cannot handle input types in compare()');
    }
}
