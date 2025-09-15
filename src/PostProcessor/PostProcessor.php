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

namespace Google\PostProcessor;

use Google\Generator\Collections\Vector;

class PostProcessor
{
    private string $inputDir;
    private Vector $processors;

    public static function process(array $opts): void
    {
        (new PostProcessor($opts))->execute();
    }

    private function __construct(array $opts)
    {
        $this->inputDir = ($opts['base_dir'] ?? '.') . '/' . $opts['input'];
        $this->processors = self::loadProcessors($opts);
    }

    private function execute(): void
    {
        foreach ($this->processors as $processor) {
            $processor::run($this->inputDir);
        }
    }

    private static function loadProcessors(array $opts): Vector
    {
        return Vector::new([FragmentInjectionProcessor::class]);
    }
}
