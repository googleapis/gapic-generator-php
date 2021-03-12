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

namespace Google\Generator\Tests\Tools;

class JsonComparer
{
  /**
   * Same as compareJson, but with some exceptions to allow for monolith versus microgenerator differences.
   */
    public static function compareMonoMicroClientConfig(string $mono, string $micro, bool $printDiffs = true): bool
    {
        return static::compareHelper($mono, $micro, true, $printDiffs);
    }

    public static function compare(string $mono, string $micro, bool $printDiffs = true): bool
    {
        return static::compareHelper($mono, $micro, false, $printDiffs);
    }

    private static function compareHelper(string $mono, string $micro, bool $doMonoMicroProcessing, bool $printDiffs = true): bool
    {
        $monoJson = json_decode($mono);
        $microJson = json_decode($micro);

        $sort = null;
        $sort = function ($a) use (&$sort) {
            $a = (array)$a;
            ksort($a);
            return array_map(fn ($x) => is_array($x) || is_object($x) ? $sort($x) : $x, $a);
        };

        $mono = $sort($monoJson);
        $micro = $sort($microJson);
        $monoJsonString = json_encode($mono, JSON_PRETTY_PRINT);
        $microJsonString = json_encode($micro, JSON_PRETTY_PRINT);
        // Do string processing because the decoded JSON has inaccessible objects.
        if ($doMonoMicroProcessing) {
          // Remove any timeout_setting strings.
          // This is needed to decouple timeout settings from service.yaml.
          $unwantedWords = '"timeout_millis"';
          $replaceMatch = '/^.*' . $unwantedWords . '.*$(?:\r\n|\n)?/m';
          $monoJsonString = preg_replace($replaceMatch, '', $monoJsonString);
          $microJsonString = preg_replace($replaceMatch, '', $microJsonString);

          // This is needed to decouple the generation of unused code definitions from gapic.yaml.
          // Remove retry_codes, no_retry_{1,2}_params, and retry_policy_2_params.
          // It just happens that any collisions occur on these definitions.
          $matchingBraceRegex = '\{(?:[^}{]+|(?R))*+\}'; // Matches everything within a pair of brackets.
          foreach (['retry_codes', 'no_retry_(1|2)_params', 'retry_policy_\d_params'] as $paramName) {
            $replaceMatch = '/"' . $paramName . '": ' . $matchingBraceRegex . '/m';
          $monoJsonString = preg_replace($replaceMatch, '', $monoJsonString);
          $microJsonString = preg_replace($replaceMatch, '', $microJsonString);
          }
        }

        return SourceComparer::compare($monoJsonString, $microJsonString, $printDiffs);
    }
}
