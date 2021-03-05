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

class Helpers
{
    public static function toSnakeCase(string $s)
    {
        // https://stackoverflow.com/questions/1993721/how-to-convert-pascalcase-to-pascal-case
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $s));
    }

    public static function toCamelCase(string $s)
    {
        // Using explode/implode is how it's done internally in /Google/Protobuf/Internal/FieldDescriptor.
        $s = implode('', array_map('ucwords', explode('_', $s)));
        return strtolower($s[0]) . substr($s, 1);
    }

    public static function nsVersionAndSuffixPath(string $namespace): string
    {
        // Extract version and suffix from the namespace by looking for a version in the namespace.
        // E.g. ".../V6/Services" returns "V6/Services".
        // A version is heuristically detected by [v|V] followed by at least one digit.
        // Return empty string if no version part found.
        return Vector::new(explode('\\', $namespace))
            ->skipWhile(function ($s){
                if (strlen($s) < 2 || strtoupper(substr($s, 0, 1)) != 'V') {
                    return true;
                }
                return !ctype_digit(substr($s, 1, 1));
            })
            ->join('/');
    }
}
