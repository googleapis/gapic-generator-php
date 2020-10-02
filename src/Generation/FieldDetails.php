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
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\CustomOptions;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\GPBType;

class FieldDetails
{
    /** @var string *Readonly* The name of this field. */
    public string $name;

    /** @var Type *Readonly* The type of this field. */
    public Type $type;

    /** @var PhpMethod *Readonly* The method used to get this field. */
    public PhpMethod $getter;

    /** @var PhpMethod *Readonly* The method used to set this field. */
    public PhpMethod $setter;

    /** @var bool *Readonly* Whether this field is required. */
    public bool $isRequired;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    public function __construct(FieldDescriptorProto $field)
    {
        $desc = $field->desc;
        $this->name = $desc->getName();
        switch ($field->getType()) {
            case GPBType::INT32: // 5
                $this->type = Type::int();
                break;
            case GPBType::BOOL: // 8
                $this->type = Type::bool();
                break;
            case GPBType::STRING: // 9
                $this->type = Type::string();
                break;
            default:
                throw new \Exception("Cannot handle field of type: {$field->getType()}");
        }
        $this->getter = new PhpMethod($desc->getGetter());
        $this->setter = new PhpMethod($desc->getSetter());
        $this->isRequired = ProtoHelpers::getCustomOptionRepeated($desc, CustomOptions::GOOGLE_API_FIELDBEHAVIOR)
            ->contains(CustomOptions::GOOGLE_API_FIELDBEHAVIOR_REQUIRED);
        $this->docLines = $field->leadingComments;
    }
}
