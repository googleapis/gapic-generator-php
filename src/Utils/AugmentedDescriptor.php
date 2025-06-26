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

use Google\Protobuf\Internal\Descriptor;
use Google\Protobuf\Internal\Message;

class AugmentedDescriptor extends Descriptor
{
    public function __construct(
        private Descriptor $desc,
        private Message $underlyingProto
    ) {
    }

    public function getUnderlyingProto(): Message
    {
        return $this->underlyingProto;
    }

    public function addOneofDecl($oneof)
    {
        $this->desc->addOneofDecl($oneof);
    }

    public function getOneofDecl()
    {
        return $this->desc->getOneofDecl();
    }

    public function setFullName($full_name)
    {
        $this->desc->setFullName($full_name);
    }

    public function getFullName()
    {
        return $this->desc->getFullName();
    }

    public function addField($field)
    {
        $this->desc->addField($field);
    }

    public function getField()
    {
        return $this->desc->getField();
    }

    public function addNestedType($desc)
    {
        $this->desc->addNestedType($desc);
    }

    public function getNestedType()
    {
        return $this->desc->getNestedType();
    }

    public function addEnumType($desc)
    {
        $this->desc->addEnumType($desc);
    }

    public function getEnumType()
    {
        return $this->desc->getEnumType();
    }

    public function getFieldByNumber($number)
    {
        return $this->desc->getFieldByNumber($number);
    }

    public function getFieldByJsonName($json_name)
    {
        return $this->desc->getFieldByJsonName($json_name);
    }

    public function getFieldByName($name)
    {
        return $this->desc->getFieldByName($name);
    }

    public function getFieldByIndex($index)
    {
        return $this->desc->getFieldByIndex($index);
    }

    public function setClass($klass)
    {
        $this->desc->setClass($klass);
    }

    public function getClass()
    {
        return $this->desc->getClass();
    }

    public function setLegacyClass($klass)
    {
        $this->desc->setLegacyClass($klass);
    }

    public function getLegacyClass()
    {
        return $this->desc->getLegacyClass();
    }

    public function setPreviouslyUnreservedClass($klass)
    {
        $this->desc->setPreviouslyUnreservedClass($klass);
    }

    public function getPreviouslyUnreservedClass()
    {
        return $this->desc->getPreviouslyUnreservedClass();
    }

    public function setOptions($options)
    {
        $this->desc->setOptions($options);
    }

    public function getOptions()
    {
        return $this->desc->getOptions();
    }

    public function getPublicDescriptor()
    {
        return $this->desc->getPublicDescriptor();
    }
}
