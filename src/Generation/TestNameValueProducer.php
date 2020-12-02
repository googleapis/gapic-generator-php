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
use Google\Generator\Ast\Variable;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\GPBType;

class TestNameValueProducer
{
    public function __construct(ProtoCatalog $catalog, SourceFileContext $ctx)
    {
        $this->catalog = $catalog;
        $this->ctx = $ctx;
        $this->names = Set::new();
        $this->values = Set::new();
        $this->valuesByName = Map::new();
    }

    private ProtoCatalog $catalog;
    private SourceFileContext $ctx;
    private Set $names;
    private Set $values;
    private Map $valuesByName;

    public function name(string $name): string
    {
        for ($i = 1; ; $i++) {
            $n = $name . ($i === 1 ? '' : '_' . $i);
            if (!$this->names[$n]) {
                $this->names = $this->names->add($n);
                return $n;
            }
        }
    }

    public function perField(Vector $fields): Vector
    {
        return $fields->map(fn($f) => new class($this, $f) {
            public function __construct($prod, $f)
            {
                $this->field = $f;
                $this->name = $prod->name($f->name);
                $this->var = AST::var(Helpers::toCamelCase($this->name));
                $this->value = $prod->value($f, $this->name);
            }

            public FieldDetails $field;
            public string $name;
            public Variable $var;
            public $value;
        });
    }

    // Reproduce exactly the Java test value generation:
    // https://github.com/googleapis/gapic-generator/blob/e3501faea84f61be2f59bd49a7740a486a02fa6b/src/main/java/com/google/api/codegen/util/testing/StandardValueProducer.java
    // https://github.com/googleapis/gapic-generator/blob/e3501faea84f61be2f59bd49a7740a486a02fa6b/src/main/java/com/google/api/codegen/util/testing/TestValueGenerator.java
    // TODO(vNext): Make these more PHPish.

    public function value(FieldDetails $field, ?string $nameOverride = null, ?bool $forceRepeated = null)
    {
        if ($forceRepeated === true || ($field->desc->desc->isRepeated() && $forceRepeated !== false)) {
            return AST::array([]);
        }

        $name = $nameOverride ?? $field->desc->getName();

        if (!isset($this->valuesByName[$name])) {
            $allowDuplicateValue = in_array($field->desc->getType(), [GPBType::ENUM, GPBType::BOOL, GPBType::MESSAGE], true);
            $value = $this->valueImpl($field, $name);
            while ($this->values[$value] && !$allowDuplicateValue) {
                $name = $name . '_1';
                $value = $this->valueImpl($field, $name);
            }
            if (is_string($value)) {
                $this->values = $this->values->add($value);
                $this->valuesByName = $this->valuesByName->set($name, $value);
            }
        } else {
            $value = $this->valuesByName[$name];
        }
        return is_string($value) ? AST::literal($this->valuesByName[$name]) : $value;
    }

    private function valueImpl(FieldDetails $field, string $name)
    {
        $javaHashCode = function() use($name) {
            $javaHash = 0;
            for ($i = 0; $i < strlen($name); $i++) {
                $javaHash = (((31 * $javaHash) & 0xffffffff) + ord($name[$i])) & 0xffffffff;
            }
            return $javaHash > 0x7fffffff ? $javaHash -= 0x100000000 : $javaHash;
        };

        switch ($field->desc->getType()) {
            case GPBType::DOUBLE: // 1
            case GPBType::FLOAT: // 2
                $v = (float)(int)($javaHashCode() / 10);
                // See: https://docs.oracle.com/javase/7/docs/api/java/lang/Double.html#toString(double)
                $vAbs = abs($v);
                if ($vAbs >= 1e-3  && $vAbs < 1e7) {
                    $s = sprintf('%.1F', $v);
                } else {
                    $s = sprintf('%.8E', $v);
                    $s = str_replace('+', '', $s);
                    while (strpos($s, '0E') !== false) {
                        $s = str_replace('0E', 'E', $s);
                    }
                }
                return strval($s);
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
                return strval($javaHash === -0x80000000 ? 0 : abs($javaHash));
            case GPBType::BOOL: // 8
                return $javaHashCode($name) % 2 === 0 ? 'true' : 'false';
            case GPBType::STRING: // 9
                $javaHash = $javaHashCode($name);
                $prefix = Helpers::toCamelCase($name);
                return "'" . $prefix . $javaHash . "'";
            case GPBType::MESSAGE: // 11
                return AST::new($this->ctx->type(Type::fromField($this->catalog, $field->desc->desc, false)))();
            case GPBType::BYTES: // 12
                $v = $javaHashCode($name) & 0xff;
                return "'" . strval($v <= 0x7f ? $v : $v - 0x100) . "'";
            case GPBType::ENUM: // 14
                $enumValueName = $this->catalog->enumsByFullname[$field->desc->desc->getEnumType()]->getValue()[0]->getName();
                return AST::access($this->ctx->type(Type::fromField($this->catalog, $field->desc->desc)), AST::property($enumValueName));
            default:
                throw new \Exception("No testValue for type: {$field->desc->getType()}");
        }
    }
}
