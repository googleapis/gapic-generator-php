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
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\GPBType;

class FieldDetails
{
    private FieldDescriptorProto $desc;

    /** @var string *Readonly* The proto name of this field. */
    public string $name;

    /** @var string *Readonly* The name of this field in camelCase. */
    public string $camelName;

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
        $this->desc = $field;
        $desc = $field->desc;
        $this->name = $desc->getName();
        $this->camelName = Helpers::toCamelCase($this->name);
        $this->type = Type::fromField($desc);
        $this->getter = new PhpMethod($desc->getGetter());
        $this->setter = new PhpMethod($desc->getSetter());
        $this->isRequired = ProtoHelpers::getCustomOptionRepeated($desc, CustomOptions::GOOGLE_API_FIELDBEHAVIOR)
            ->contains(CustomOptions::GOOGLE_API_FIELDBEHAVIOR_REQUIRED);
        $this->docLines = $field->leadingComments;
    }

    public function testValue()
    {
        // Reproduce exactly the Java test value generation.
        // TODO(vNext): Make these more PHPish.
        switch ($this->desc->getType()) {
            case GPBType::STRING: // 9
                $name = $this->desc->getName();
                $javaHash = 0;
                for ($i = 0; $i < strlen($name); $i++) {
                    $javaHash = (((31 * $javaHash) & 0xffffffff) + ord($name[$i])) & 0xffffffff;
                }
                if ($javaHash > 0x7ffffff) $javaHash -= 0x100000000;
                return "{$this->camelName}{$javaHash}";
            default:
                // TODO: Other types.
                return "NOT YET DONE! type={$this->desc->getType()}";
        }
    }
}
