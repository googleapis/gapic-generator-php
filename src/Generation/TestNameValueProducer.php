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
        // Handle test request value generation.
        $perField = static::filterFirstOneOf($method->allFields)
            ->map(fn ($f) => new class($this, $method, $f) {
                public function __construct($prod, $method, $f)
                {
                    // This code is arranged in this way, as a name must not be generated
                    // if this field ends up not being used.
                    // Name generation is stateful.
                    // TODO: Consider refactoring this.
                    $this->field = $f;
                    $this->name = ($f->useResourceTestValue ? 'formatted_' : '') . $prod->name($f->name);
                    $this->var = AST::var(Helpers::toCamelCase($this->name));
                    $astAcc = Vector::new([]);
                    $prod->fieldInit($method, $f, $this->var, $this->name, null, $astAcc);
                    $this->initCode = $astAcc;
                }

                public FieldDetails $field;
                public string $name;
                public Variable $var;
                public $initCode;
            })
            ->filter(fn ($x) => !is_null($x->initCode) && $x->field->isRequired);

        $callArgs = $perField
            ->flatten()
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
    // TODO: Handle nesting - see PublishSeries.
    public function fieldInit(MethodDetails $method, FieldDetails $field, &$fieldVar, string $fieldVarName, ?Variable $clientVar, Vector &$astAcc)
    {
        if (!$field->isRequired) {
            $astAcc = $astAcc->append(null);
            return;
        }

        if ($field->isOneOf) {
            $oneofWrapperType = $field->toOneofWrapperType($method->serviceDetails->namespace);
            // Initialize the oneof, e.g.
            //   $supplementaryData = new SupplementaryDataOneof();
            $oneofDescProto = $field->containingMessage->getOneofDecl()[$field->oneOfIndex];
            $fieldVar = AST::var(Helpers::toCamelCase($oneofDescProto->getName()));
            // TODO(v2): Enable recursion like the map source below. We don't do this yet
            // because nobody exercises this use case.
            $astAcc = $astAcc->append(AST::assign($fieldVar, AST::new($this->ctx->type($oneofWrapperType))()));
            // Set the oneof field, e.g.
            //    $supplementaryData->setExtraDescription('extraDescription-1352933811');
            $astAcc = $astAcc->append(
                AST::call(
                    $fieldVar,
                    AST::method("set" . Helpers::toUpperCamelCase($field->camelName))
                )(
                        // TODO(v2): Handle non-primitive types.
                        $this->value($field, $fieldVarName)
                )
            );
            return;
        }

        if ($field->isMessage && $field->isMap) {
            // A map descriptor looks something like this:
            //     message MapFieldEntry {
            //         option map_entry = true;
            //         optional KeyType key = 1;
            //         optional ValueType value = 2;
            //     }
            //     repeated MapFieldEntry map_field = 1;
            $mapMsg = $this->catalog->msgsByFullname[$field->desc->desc->getMessageType()];
            $kvSubfields = Vector::new($mapMsg->getField())->map(fn ($x) => new FieldDetails($this->catalog, $mapMsg, $x));
            $keyName = Helpers::toCamelCase($field->camelName . '_key');

            $valueFieldVarName = Helpers::toCamelCase($field->camelName . '_value');
            $valueField = $kvSubfields[1];
            // Hack to enable recursion.
            $valueField->isRequired = true;
            $valueFieldVar = AST::var($valueFieldVarName);
            $this->fieldInit($method, $valueField, $valueFieldVar, $valueFieldVarName, $valueFieldVar, $astAcc);

            $astAcc = $astAcc->append(AST::assign($fieldVar, AST::array([$keyName => $valueFieldVar])));
            return;
        }

        // This field is a non-primitive type. Descend into required sub-fields.
        // Does not yet handle repeated message fields.
        if ($field->isMessage && !$field->isEnum && !$field->isRepeated) {
            $astAcc = $astAcc->append(AST::assign($fieldVar, $this->value($field, $fieldVarName)));

            $msg = $this->catalog->msgsByFullname[$field->desc->desc->getMessageType()];
            $allSubFields = Vector::new($msg->getField())->map(fn ($x) => new FieldDetails($this->catalog, $msg, $x));
            $requiredSubFields = $allSubFields->filter(fn ($x) => $x->isRequired);
            if (empty($requiredSubFields) && !$field->useResourceTestValue) {
                $astAcc = $astAcc->append(AST::assign($fieldVar, $this->value($field, $fieldVarName)));
                return;
            }

            foreach ($requiredSubFields as $subField) {
                $subFieldVarName = Helpers::toCamelCase($field->name . "_" . $subField->name);
                $subFieldVar = AST::var($subFieldVarName);
                $this->fieldInit($method, $subField, $subFieldVar, $subFieldVarName, $clientVar, $astAcc);
                $astAcc = $astAcc->append(AST::call($fieldVar, $subField->setter)($subFieldVar));
            }
            return;
        }

        if (!$field->useResourceTestValue) {
            $astAcc = $astAcc->append(AST::assign($fieldVar, $this->value($field, $fieldVarName)));
            return;
        }

        // IMPORTANT: The template name getters are always generated with the first
        // pattern in the proto, so keep that behavior here.
        if (count($field->resourceDetails->patterns) > 0) {
            $patternDetails = $field->resourceDetails->patterns[0];
            $args = $field->resourceDetails->getParams()->map(fn ($x) => strtoupper("[{$x[0]}]"));
        } else {
            // TODO: Better handling of wild-card patterns.
            $args = $field->name . "-" . hash("md5", $field->name);
        }
        $clientVar = $clientVar ?? AST::var('client');
        // TODO: This should be better merged with FieldDetails.
        $varValue = $clientVar->instanceCall($field->resourceDetails->formatMethod)($args);
        if ($field->isRepeated) {
            $varValue = AST::array([$varValue]);
        }
        $astAcc = $astAcc->append(AST::assign($fieldVar, $varValue));
        return;
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
                if ($field->isMap) {
                    // A map descriptor looks something like this:
                    //     message MapFieldEntry {
                    //         option map_entry = true;
                    //         optional KeyType key = 1;
                    //         optional ValueType value = 2;
                    //     }
                    //     repeated MapFieldEntry map_field = 1;
                    $mapMsg = $this->catalog->msgsByFullname[$field->desc->desc->getMessageType()];
                    $kvSubfields = Vector::new($mapMsg->getField())->map(fn ($x) => new FieldDetails($this->catalog, $mapMsg, $x));
                    $keyName = Helpers::toCamelCase($field->camelName . '_key');
                    $valueField = $kvSubfields[1];
                    // Hacks to ensure this ends up in the test value initialization setters.
                    $valueField->isInTestResponse = true;
                    // TODO: More recursive logic for initializing the value field?
                    return AST::array([$keyName => $this->value($valueField)]);
                }

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
