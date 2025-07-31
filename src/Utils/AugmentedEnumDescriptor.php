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

use Google\Protobuf\Internal\EnumDescriptor;
use Google\Protobuf\Internal\Message;

class AugmentedEnumDescriptor extends EnumDescriptor
{
    public function __construct(
        private EnumDescriptor $enumDesc,
        private Message $underlyingProto
    ) {
    }

    public function getUnderlyingProto(): Message
    {
        return $this->underlyingProto;
    }

    public function setFullName($full_name)
    {
        $this->enumDesc->setFullName($full_name);
    }

    public function getFullName()
    {
        return $this->enumDesc->getFullName();
    }

    public function addValue($number, $value)
    {
        $this->enumDesc->addValue($number, $value);
    }

    public function getValueByNumber($number)
    {
        return $this->enumDesc->getValueByNumber($number);
    }

    public function getValueByName($name)
    {
        return $this->enumDesc->getValueByName($name);
    }

    public function getValueDescriptorByIndex($index)
    {
        return $this->enumDesc->getValueDescriptorByIndex($index);
    }

    public function getValueCount()
    {
        return $this->enumDesc->getValueCount();
    }

    public function setClass($klass)
    {
        $this->enumDesc->setClass($klass);
    }

    public function getClass()
    {
        return $this->enumDesc->getClass();
    }

    public function setLegacyClass($klass)
    {
        $this->enumDesc->setLegacyClass($klass);
    }

    public function getLegacyClass()
    {
        return $this->enumDesc->getLegacyClass();
    }
}
