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
use Google\Generator\Utils\GapicYamlConfig;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\GPBType;

class TestNameValueProducer
{
    public function __construct(ProtoCatalog $catalog, SourceFileContext $ctx, GapicYamlConfig $gapicYamlConfig)
    {
        $this->catalog = $catalog;
        $this->ctx = $ctx;
        $this->gapicYamlConfig = $gapicYamlConfig;
        $this->names = Set::new();
        $this->values = Set::new();
        $this->valuesByName = Map::new();
    }

    private ProtoCatalog $catalog;
    private SourceFileContext $ctx;
    private GapicYamlConfig $gapicYamlConfig;
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

    private static function filterFirstOneOf(Vector $fields): Vector
    {
        $oneOfs = Set::new();
        return $fields
            ->filter(function ($f) use (&$oneOfs) {
                if (is_null($f->oneOfIndex)) {
                    return true;
                } elseif ($oneOfs[$f->oneOfIndex]) {
                    return false;
                } else {
                    $oneOfs = $oneOfs->add($f->oneOfIndex);
                    return true;
                }
            });
    }

    public function perField(Vector $fields, ?Map $forceValues = null): Vector
    {
        $oneOfs = Set::new();
        return static::filterFirstOneOf($fields)
            ->map(fn ($f) => new class($this, $f, $forceValues ?? Map::new()) {
                public function __construct($prod, $f, $forceValues)
                {
                    $this->field = $f;
                    $this->name = $prod->name($f->name);
                    $this->var = AST::var(Helpers::toCamelCase($this->name));
                    $this->value = $forceValues->get($f->name, $prod->value($f, $this->name));
                }

                public FieldDetails $field;
                public string $name;
                public Variable $var;
                public $value;
            });
    }

    public function perFieldRequest(MethodDetails $method): array
    {
        // Handle test request value generation, including using values
        // specified in the gapic-config if present.

        $perField = static::filterFirstOneOf($method->allFields)
            ->map(fn ($f) => new class($this, $method, $f) {
                public function __construct($prod, $method, $f)
                {
                    // This code is arranged in this way, as a name must not be generated
                    // if this field ends up not being used.
                    // Name generation is stateful.
                    // TODO: Consider refactoring this.
                    $this->field = $f;
                    $fnVarName = function () use ($prod, $f) {
                        $this->name = ($f->useResourceTestValue ? 'formatted_' : '') . $prod->name($f->name);
                        $this->var = AST::var(Helpers::toCamelCase($this->name));
                        return [$this->var, $this->name];
                    };
                    $this->initCode = $prod->fieldInit($method, $f, $fnVarName);
                }

                public FieldDetails $field;
                public string $name;
                public Variable $var;
                public $initCode;
            })
            ->filter(fn ($x) => !is_null($x->initCode))
            ->orderBy(fn ($x) => $x->field->isRequired ? 0 : 1);

        $callArgs = $perField
            ->filter(fn ($x) => $x->field->isRequired)
            ->map(fn ($x) => $x->var);
        if ($perField->any(fn ($x) => !$x->field->isRequired)) {
            $callArgs = $callArgs->append(
                AST::array(
                    $perField
                        ->filter(fn ($x) => !$x->field->isRequired)
                        ->toArray(fn ($x) => $x->var->name, fn ($x) => $x->var)
                )
            );
        }

        return [$perField, $callArgs];
    }

    // TODO: Refactor this to share code with very similar code in GapicClientExamplesGenerator.php
    public function fieldInit(MethodDetails $method, FieldDetails $field, callable $fnVarName, ?Variable $clientVar = null)
    {
        $fnInitValue = function (FieldDetails $f, string $value) {
            if ($f->isEnum) {
                return AST::access($this->ctx->type($f->typeSingular), AST::constant($value));
            } else {
                switch ($f->type->name) {
                    case 'string':
                        return $value;
                    default:
                        throw new \Exception("Cannot handle init-field of type: '{$f->type->name}'");
                }
            }
        };

        $config = $this->gapicYamlConfig->configsByMethodName->get($method->name, null);
        $inits = !is_null($config) && isset($config['sample_code_init_fields']) ?
            Map::fromPairs(array_map(fn ($x) => explode('=', $x, 2), $config['sample_code_init_fields'])) : Map::new();
        $value = $inits->get($field->name, null);
        $valueIndex = $inits->get($field->name . '[0]', null);
        if ($field->isRequired || !is_null($value) || !is_null($valueIndex)) {
            [$var, $name] = $fnVarName();
            if (!is_null($value)) {
                return AST::assign($var, $fnInitValue($field, $value));
            } elseif (!is_null($valueIndex)) {
                if (!$field->isRepeated) {
                    throw new \Exception('Only a repeated field may use indexed init fields');
                }
                $initElements = Vector::range(0, 9)
                    ->takeWhile(fn ($i) => isset($inits["{$field->name}[{$i}]"]))
                    ->map(fn ($i) => [AST::var("{$var->name}Element" . ($i === 0 ? '' : $i + 1)), $inits["{$field->name}[{$i}]"]]);
                return $initElements
                    ->map(fn ($x) => AST::assign($x[0], $fnInitValue($field, $x[1])))
                    ->append(AST::assign($var, AST::array($initElements->map(fn ($x) => $x[0])->toArray())));
            } elseif ($field->useResourceTestValue) {
                // TODO: What if it's just a wild-card pattern?
                $patternDetails = $field->resourceDetails->patterns[0];
                $args = $patternDetails->params->map(fn ($x) => strtoupper("[{$x[0]}]"));
                $clientVar = $clientVar ?? AST::var('client');
                return AST::assign($var, $clientVar->instanceCall($field->resourceDetails->formatMethod)($args));
            } else {
                return AST::assign($var, $this->value($field, $name));
            }
        } else {
            return null;
        }
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
        $javaHashCode = function () use ($name) {
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
