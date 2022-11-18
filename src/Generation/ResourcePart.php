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

namespace Google\Generator\Generation;

use Google\Generator\Ast\PhpMethod;
use Google\Generator\Ast\PhpProperty;
use Google\Generator\Collections\Vector;

interface ResourcePart
{
    public function getNameCamelCase(): string;

    public function getNameSnakeCase(): string;

    public function getPattern(): string;

    public function getFormatMethod(): PhpMethod;

    public function getParams(): Vector;
}
