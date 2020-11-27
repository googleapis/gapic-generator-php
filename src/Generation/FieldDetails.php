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

use Google\Generator\Ast\AST;
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
    /** @var ProtoCatalog The proto catalog. */
    public ProtoCatalog $catalog;

    /** @var FieldDescriptorProto The proto definition of this field. */
    public FieldDescriptorProto $desc;

    /** @var string *Readonly* The proto name of this field. */
    public string $name;

    /** @var string *Readonly* The name of this field in camelCase. */
    public string $camelName;

    /** @var Type *Readonly* The type of this field. */
    public Type $type;

    /** @var Type *Readonly* The type of this field, treating it as not repeated. */
    public Type $typeSingular;

    /** @var PhpMethod *Readonly* The method used to get this field. */
    public PhpMethod $getter;

    /** @var PhpMethod *Readonly* The method used to set this field. */
    public PhpMethod $setter;

    /** @var bool *Readonly* Whether this field is required. */
    public bool $isRequired;

    /** @var bool *Readonly* Whether this field is an enum. */
    public bool $isEnum;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    public function __construct(ProtoCatalog $catalog, FieldDescriptorProto $field, ?Vector $docLinesOverride = null)
    {
        $this->catalog = $catalog;
        $this->desc = $field;
        $desc = $field->desc;
        $this->name = $desc->getName();
        $this->camelName = Helpers::toCamelCase($this->name);
        $this->type = Type::fromField($catalog, $desc);
        $this->typeSingular = Type::fromField($catalog, $desc, false);
        $this->getter = new PhpMethod($desc->getGetter());
        $this->setter = new PhpMethod($desc->getSetter());
        $this->isRequired = ProtoHelpers::getCustomOptionRepeated($desc, CustomOptions::GOOGLE_API_FIELDBEHAVIOR)
            ->contains(CustomOptions::GOOGLE_API_FIELDBEHAVIOR_REQUIRED);
        $this->isEnum = $field->getType() === GPBType::ENUM;
        $this->docLines = $docLinesOverride ?? $field->leadingComments->concat($field->trailingComments);
    }

    public function exampleValue(SourceFileContext $ctx)
    {
        if ($this->desc->desc->isRepeated()) {
            return AST::array([]);
        }
        switch ($this->desc->getType()) {
            case GPBType::DOUBLE: // 1
            case GPBType::FLOAT: // 2
                return 0.0;
            case GPBType::INT64: // 3
            case GPBType::UINT64: // 4
            case GPBType::INT32: // 5
            case GPBType::FIXED64: // 6
            case GPBType::FIXED32: // 7
            case GPBType::UINT32: //13
            case GPBType::SFIXED32: // 15
            case GPBType::SFIXED64: // 16
            case GPBType::SINT32: // 17
            case GPBType::SINT64: // 18
                return 0;
            case GPBType::BOOL: // 8
                return false;
            case GPBType::STRING: // 9
                return '';
            case GPBType::MESSAGE: // 11
                return AST::new($ctx->type(Type::fromField($this->catalog, $this->desc->desc)))();
            case GPBType::BYTES: // 12
                return '';
            case GPBType::ENUM: // 14
                $enumValueName = $this->catalog->enumsByFullname[$this->desc->desc->getEnumType()]->getValue()[0]->getName();
                return AST::access($ctx->type(Type::fromField($this->catalog, $this->desc->desc)), AST::property($enumValueName));
            default:
                throw new \Exception("No exampleValue for type: {$this->desc->getType()}");
        }
    }

    public function testValue(SourceFileContext $ctx, ?string $nameOverride = null, ?bool $forceRepeated = null)
    {
        // Reproduce exactly the Java test value generation:
        // https://github.com/googleapis/gapic-generator/blob/e3501faea84f61be2f59bd49a7740a486a02fa6b/src/main/java/com/google/api/codegen/util/testing/StandardValueProducer.java
        // TODO(vNext): Make these more PHPish.

        if ($forceRepeated === true || ($this->desc->desc->isRepeated() && $forceRepeated !== false)) {
            return AST::array([]);
        }

        $javaHashCode = function(string $name) {
            $javaHash = 0;
            for ($i = 0; $i < strlen($name); $i++) {
                $javaHash = (((31 * $javaHash) & 0xffffffff) + ord($name[$i])) & 0xffffffff;
            }
            return $javaHash > 0x7fffffff ? $javaHash -= 0x100000000 : $javaHash;
        };

        $name = $nameOverride ?? $this->desc->getName();
        switch ($this->desc->getType()) {
            case GPBType::DOUBLE: // 1
            case GPBType::FLOAT: // 2
                $v = (float)(int)($javaHashCode($name) / 10);
                // See: https://docs.oracle.com/javase/7/docs/api/java/lang/Double.html#toString(double)
                $vAbs = abs($v);
                if ($vAbs >= 1e-3  && $vAbs < 1e7) {
                    $s = sprintf('%.1F', $v);
                } else {
                    $s = sprintf('%.8E', $v);
                    $s = str_replace('+', '', $s);
                }
                return AST::literal($s);
            case GPBType::INT64: // 3
            case GPBType::UINT64: // 4
            case GPBType::INT32: // 5
            case GPBType::FIXED64: // 6
            case GPBType::FIXED32: // 7
            case GPBType::UINT32: //13
            case GPBType::SFIXED32: // 15
            case GPBType::SFIXED64: // 16
            case GPBType::SINT32: // 17
            case GPBType::SINT64: // 18
                $javaHash = $javaHashCode($name);
                return $javaHash === -0x80000000 ? 0 : abs($javaHash);
            case GPBType::BOOL: // 8
                return $javaHashCode($name) % 2 === 0;
            case GPBType::STRING: // 9
                $javaHash = $javaHashCode($name);
                $prefix = is_null($nameOverride) ? $this->camelName : Helpers::toCamelCase($nameOverride);
                return $prefix . $javaHash;
            case GPBType::MESSAGE: // 11
                return AST::new($ctx->type(Type::fromField($this->catalog, $this->desc->desc, false)))();
            case GPBType::BYTES: // 12
                $v = $javaHashCode($name) & 0xff;
                return strval($v <= 0x7f ? $v : $v - 0x100);
            case GPBType::ENUM: // 14
                $enumValueName = $this->catalog->enumsByFullname[$this->desc->desc->getEnumType()]->getValue()[0]->getName();
                return AST::access($ctx->type(Type::fromField($this->catalog, $this->desc->desc)), AST::property($enumValueName));
            default:
                throw new \Exception("No testValue for type: {$this->desc->getType()}");
        }
    }
}
