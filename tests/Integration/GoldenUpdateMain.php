<?php
/*
 * Copyright 2026 Google LLC
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

namespace Google\Generator\Tests\Integration;

use Google\Generator\Tests\Tools\GeneratorUtils;
use Google\Generator\Tests\Unit\ProtoTests\UnitGoldenUpdater;

class GoldenUpdateMain
{
    public static function updateAll(): int
    {
        return self::update(0);
    }

    public static function update(int|string $selection): int
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        error_reporting(E_ALL);

        $names = array_keys(IntegrationTest::TESTS);
        if (is_string($selection) && !is_numeric($selection)) {
            $index = array_search($selection, $names, true);
            $selection = $index !== false ? $index + 1 : -1;
        } else {
            $selection = (int) $selection;
        }

        $optionString = implode("\n", array_map(
            fn ($name, $idx) => sprintf("%d: '%s'", $idx + 1, $name),
            $names,
            array_keys($names)
        ));

        while ($selection < 0 || $selection > count($names)) {
            print("============ Integration tests ==========\n$optionString\n\nSelect golden to update (0 for all): ");
            fscanf(STDIN, "%d\n", $selection);
        }

        if ($selection !== 0) {
            self::updateGolden($names[$selection - 1]);
        } else {
            foreach ($names as $name) {
                self::updateGolden($name);
                print("\n");
            }
        }
        return 0;
    }

    private static function updateGolden(string $name): void
    {
        print("Updating integration goldens for {$name}\n");
        $codeIterator = GeneratorUtils::generateIntegration(IntegrationTest::TESTS[$name]);
        UnitGoldenUpdater::writeGoldens($codeIterator, __DIR__ . '/goldens/' . $name);
        print("\n");
    }
}

if (isset($argv) && realpath($argv[0]) === __FILE__) {
    $selection = $argv[1] ?? -1;
    GoldenUpdateMain::update($selection);
}
