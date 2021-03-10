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

use Google\Api\ResourceReference;
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

    /** @var bool *Readonly* Whether this field is a map. */
    public bool $isMap;

    // TODO(vNext): Simplify unit-test response generation.
    /** @var bool *Readonly* Whether this field type is populated in a unit-test response. */
    public bool $isInTestResponse;

    /** @var bool *Readonly* Whether this field is repeated. */
    public bool $isRepeated;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    /** @var ?ResourceDetails The resource details, if this field is a resource; null otherwise. */
    public ?ResourceDetails $resourceDetails;

    /** @var bool Whether tests and examples should use use a resource-type value. */
    public bool $useResourceTestValue;

    /** @var ?int null if not in a one-of; otherwise the index of the one-of - ie every field in a oneof has the same index. */
    public ?int $oneOfIndex;

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
        $this->isMap = ProtoHelpers::isMap($catalog, $desc);
        $this->isInTestResponse = $field->getType() !== GPBType::MESSAGE && $field->getType() !== GPBType::ENUM && !$field->desc->isRepeated();
        $this->isRepeated = $field->desc->isRepeated();
        $this->docLines = $docLinesOverride ?? $field->leadingComments->concat($field->trailingComments);
        // Load resource details, if relevant.
        $resRef = ProtoHelpers::getCustomOption($field, CustomOptions::GOOGLE_API_RESOURCEREFERENCE, ResourceReference::class);
        if (!is_null($resRef)) {
            if ($resRef->getType() === '' && $resRef->getChildType() === '') {
                throw new \Exception('type of child_type must be set to a value.');
            }
            if ($resRef->getType() !== '' && $resRef->getType() !== '*') {
                $this->resourceDetails = new ResourceDetails($catalog->resourcesByType[$resRef->getType()]);
            } elseif ($resRef->getChildType() !== '' && $resRef->getChildType() !== '*') {
                // Get the first of possibly multiple resources.
                // This lookup can (correctly) fail if the parent resource contains a '*' pattern.
                $parentResources = $catalog->parentResourceByChildType->get($resRef->getChildType(), null);
                if (!is_null($parentResources) && $parentResources->any()) {
                    $this->resourceDetails = new ResourceDetails($parentResources[0]);
                } else {
                    $this->resourceDetails = null;
                }
            } else {
                $this->resourceDetails = null;
            }
        } else {
            // TODO: Check for resource-definition message.
            $this->resourceDetails = null;
        }
        $this->useResourceTestValue = !is_null($this->resourceDetails) && count($this->resourceDetails->patterns) === 1;
        $this->oneOfIndex = $field->hasOneOfIndex() ? $field->getOneofIndex() : null;
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
                return $this->name;
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
}
