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

use Google\Protobuf\Internal\FieldDescriptor;
use Google\Protobuf\Internal\Message;

class AugmentedFieldDescriptor extends FieldDescriptor
{
    public function __construct(
        private FieldDescriptor $fieldDesc,
        private Message $underlyingProto
    ) {
    }

    public function getUnderlyingProto(): Message
    {
        return $this->underlyingProto;
    }

    public function setOneofIndex($index)
    {
        $this->fieldDesc->setOneofIndex($index);
    }

    public function getOneofIndex()
    {
        return $this->fieldDesc->getOneofIndex();
    }

    public function setName($name)
    {
        $this->fieldDesc->setName($name);
    }

    public function getName()
    {
        return $this->fieldDesc->getName();
    }

    public function setJsonName($json_name)
    {
        $this->fieldDesc->setJsonName($json_name);
    }

    public function getJsonName()
    {
        return $this->fieldDesc->getJsonName();
    }

    public function setSetter($setter)
    {
        $this->fieldDesc->setSetter($setter);
    }

    public function getSetter()
    {
        return $this->fieldDesc->getSetter();
    }

    public function setGetter($getter)
    {
        $this->fieldDesc->setGetter($getter);
    }

    public function getGetter()
    {
        return $this->fieldDesc->getGetter();
    }

    public function setNumber($number)
    {
        $this->fieldDesc->setNumber($number);
    }

    public function getNumber()
    {
        return $this->fieldDesc->getNumber();
    }

    public function setLabel($label)
    {
        $this->fieldDesc->setLabel($label);
    }

    public function getLabel()
    {
        return $this->fieldDesc->getLabel();
    }

    public function isRepeated()
    {
        return $this->fieldDesc->isRepeated();
    }

    public function setType($type)
    {
        $this->fieldDesc->setType($type);
    }

    public function getType()
    {
        return $this->fieldDesc->getType();
    }

    public function setMessageType($message_type)
    {
        $this->fieldDesc->setMessageType($message_type);
    }

    public function getMessageType()
    {
        return $this->fieldDesc->getMessageType();
    }

    public function setEnumType($enum_type)
    {
        $this->fieldDesc->setEnumType($enum_type);
    }

    public function getEnumType()
    {
        return $this->fieldDesc->getEnumType();
    }

    public function setPacked($packed)
    {
        $this->fieldDesc->setPacked($packed);
    }

    public function getPacked()
    {
        return $this->fieldDesc->getPacked();
    }

    public function isPackable()
    {
        return $this->fieldDesc->isPackable();
    }

    public function isMap()
    {
        return $this->fieldDesc->isMap();
    }

    public function isTimestamp()
    {
        return $this->fieldDesc->isTimestamp();
    }

    public function isWrapperType()
    {
        return $this->fieldDesc->isWrapperType();
    }
}
