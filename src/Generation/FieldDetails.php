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
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\GPBType;

class FieldDetails
{
    /** @var FieldDescriptorProto The proto definition of this field. */
    public FieldDescriptorProto $desc;

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

    public function __construct(ProtoCatalog $catalog, FieldDescriptorProto $field, ?Vector $docLinesOverride = null)
    {
        $this->desc = $field;
        $desc = $field->desc;
        $this->name = $desc->getName();
        $this->camelName = Helpers::toCamelCase($this->name);
        $this->type = Type::fromField($catalog, $desc);
        $this->getter = new PhpMethod($desc->getGetter());
        $this->setter = new PhpMethod($desc->getSetter());
        $this->isRequired = ProtoHelpers::getCustomOptionRepeated($desc, CustomOptions::GOOGLE_API_FIELDBEHAVIOR)
            ->contains(CustomOptions::GOOGLE_API_FIELDBEHAVIOR_REQUIRED);
        $this->docLines = $docLinesOverride ?? $field->leadingComments->concat($field->trailingComments);
    }

    public function testValue(?string $nameOverride = null)
    {
        // Reproduce exactly the Java test value generation:
        // https://github.com/googleapis/gapic-generator/blob/e3501faea84f61be2f59bd49a7740a486a02fa6b/src/main/java/com/google/api/codegen/util/testing/StandardValueProducer.java
        // TODO(vNext): Make these more PHPish.

        $javaHashCode = function(string $name) {
            $javaHash = 0;
            for ($i = 0; $i < strlen($name); $i++) {
                $javaHash = (((31 * $javaHash) & 0xffffffff) + ord($name[$i])) & 0xffffffff;
            }
            return $javaHash > 0x7fffffff ? $javaHash -= 0x100000000 : $javaHash;
        };

        $name = $nameOverride ?? $this->desc->getName();
        switch ($this->desc->getType()) {
            case GPBType::INT32: // 5
                $javaHash = $javaHashCode($name);
                return $javaHash === -0x80000000 ? 0 : abs($javaHash);
            case GPBType::STRING: // 9
                $javaHash = $javaHashCode($name);
                $prefix = is_null($nameOverride) ? $this->camelName : Helpers::toCamelCase($nameOverride);
                return $prefix . $javaHash;
            default:
                // TODO: Other types.
                return "NOT YET DONE! type={$this->desc->getType()}";
        }
    }
}
