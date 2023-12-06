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
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\GPBType;

class FieldDetails
{
    /** @var ProtoCatalog The proto catalog. */
    public ProtoCatalog $catalog;

    /** @var FieldDescriptorProto The proto definition of this field. */
    public FieldDescriptorProto $desc;

    /** @var int *Readonly* This field's number in its containing message. */
    public int $number;

    /** @var string *Readonly* The proto name of this field. */
    public string $name;

    /** @var string *Readonly* The name of this field in camelCase. */
    public string $camelName;

    /** @var Type *Readonly* The type of this field. */
    public Type $type;

    /** @var DescriptorProto *Readonly* The containing message of this field. */
    public DescriptorProto $containingMessage;

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

    /** @var bool *Readonly* Whether this field is a message. */
    public bool $isMessage;

    /** @var bool *Readonly* Whether this field is a map. */
    public bool $isMap;

    /** @var bool *Readonly* Whether this field is a oneof. */
    public bool $isOneOf;

    /** @var bool *Readonly* The full name of the field's type if this is a message, null otherwise. */
    public ?string $fullname;

    // TODO(vNext): Simplify unit-test response generation.
    /** @var bool *Readonly* Whether this field type is populated in a unit-test response. */
    public bool $isInTestResponse;

    /** @var bool *Readonly* Whether this field is repeated. */
    public bool $isRepeated;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    /** @var ?ResourceDetails The resource details, if this field is a resource; null otherwise. */
    public ?ResourceDetails $resourceDetails;

    /** @var bool Whether tests and examples should use a resource-type value. */
    public bool $useResourceTestValue;

    /** @var ?int null if not in a one-of; otherwise the index of the one-of - ie every field in a oneof has the same index. */
    public ?int $oneOfIndex;

    /**
     * @var Vector *Readonly* Vector of FieldDetails. Contains all required subfields on this field, given the field
     * is a message. Will be empty otherwise.
     */
    public Vector $requiredSubFields;

    /**
     * Reverts fields which were previously required, but were made optional
     * AFTER a package's 1.0 release, back to being required.
     */
    private static $requiredToOptionalFixes = [
        'google.bigtable.admin.v2.Cluster' => ['name', 'serve_nodes'],
        'google.bigtable.admin.v2.Instance' => ['name', 'type', 'labels'],
        'google.cloud.asset.v1.BatchGetAssetsHistoryRequest' => ['content_type', 'read_time_window'],
        'google.cloud.datacatalog.v1.SearchCatalogRequest' => ['query'],
        'google.cloud.scheduler.v1.UpdateJobRequest' => ['update_mask'],
        'google.datastore.v1.CommitRequest' => ['mode', 'mutations'],
        'google.datastore.v1.RunQueryRequest' => ['partition_id'],
        'google.firestore.v1.CommitRequest' => ['writes'],
        'google.firestore.v1.BatchGetDocumentsRequest' => ['documents'],
        'google.firestore.v1.CreateDocumentRequest' => ['document_id'],
        'google.firestore.v1.ListDocumentsRequest'  => ['collection_id'],
        'google.firestore.v1.UpdateDocumentRequest' => ['update_mask'],
        'google.cloud.kms.v1.AsymmetricSignRequest' => ['digest'],
        'google.cloud.recaptchaenterprise.v1.AnnotateAssessmentRequest' => ['annotation'],
        'google.longrunning.CancelOperationRequest' => ['name'],
        'google.longrunning.DeleteOperationRequest' => ['name'],
        'google.longrunning.GetOperationRequest'    => ['name'],
        'google.longrunning.ListOperationsRequest'  => ['name', 'filter'],
        'google.pubsub.v1.DeleteSchemaRevisionRequest' => ['revision_id'],
        'google.spanner.v1.CommitRequest' => ['mutations'],
    ];

    /**
     * Reverts fields which were previously optional, but were made required
     * AFTER a package's 1.0 release, back to being optional.
     */
    private static $optionalToRequiredFixes = [
        'google.cloud.clouddms.v1.DescribeDatabaseEntitiesRequest' => ['tree'],
        'google.cloud.clouddms.v1.ImportMappingRulesRequest' => ['rules_format', 'rules_files', 'auto_commit'],
        'google.cloud.texttospeech.v1.SynthesizeLongAudioRequest' => ['output_gcs_uri', 'voice'],
        'google.cloud.videointelligence.v1.AnnotateVideoRequest' => ['features'],
        'google.devtools.artifactregistry.v1beta2.ListFilesRequest' => ['parent'],
        'google.devtools.artifactregistry.v1beta2.GetFileRequest' => ['name'],
        'google.devtools.artifactregistry.v1.CreateRepositoryRequest' => ['repository', 'repository_id'],
        'google.firestore.v1.BatchWriteRequest' => ['database'],
        'google.firestore.v1.PartitionQueryRequest' => ['parent'],
        'google.logging.v2.UpdateCmekSettingsRequest' => ['name', 'cmek_settings'],
        'google.logging.v2.GetCmekSettingsRequest' => ['name'],
        'google.spanner.v1.CreateSessionRequest' => ['session'],
    ];

    public function __construct(ProtoCatalog $catalog, DescriptorProto $containingMessage, FieldDescriptorProto $field, ?Vector $docLinesOverride = null)
    {
        $this->catalog = $catalog;
        $this->desc = $field;
        $this->containingMessage = $containingMessage;
        $desc = $field->desc;
        $this->number = $desc->getNumber();
        $this->name = $desc->getName();
        $this->camelName = Helpers::toCamelCase($this->name);
        $this->type = Type::fromField($catalog, $desc);
        $this->typeSingular = Type::fromField($catalog, $desc, false);
        $this->getter = new PhpMethod($desc->getGetter());
        $this->setter = new PhpMethod($desc->getSetter());
        $this->isRequired = $this->determineIsRequired($containingMessage, $field);
        $this->isMessage = $field->getType() == GPBType::MESSAGE;
        $this->fullname = $this->isMessage ? $field->getTypeName() : null;
        $this->isEnum = $field->getType() === GPBType::ENUM;
        $this->isMap = ProtoHelpers::isMap($catalog, $desc);
        $this->isInTestResponse = $field->getType() !== GPBType::MESSAGE && $field->getType() !== GPBType::ENUM && !$field->desc->isRepeated();
        $this->isRepeated = $field->desc->isRepeated();
        $this->docLines = $docLinesOverride ?? $field->leadingComments->concat($field->trailingComments);
        $this->requiredSubFields = Vector::new();
        // Load resource details, if relevant.
        $resRef = ProtoHelpers::resourceReference($field);
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
        // Use fancy fooName() methods only if this isn't a wildcard pattern.
        $this->useResourceTestValue = !is_null($this->resourceDetails) && count($this->resourceDetails->patterns) > 0;
        // Ignore synthetic oneofs created by proto3_optional fields.
        $this->isOneOf = $field->hasOneofIndex() && !$field->getProto3Optional();
        $this->oneOfIndex = $this->isOneOf ? $field->getOneofIndex() : null;
        if ($this->isMessage) {
            $fDesc = $this->catalog->msgsByFullname[$desc->getMessageType()];
            foreach ($fDesc->getField() as $f) {
                if (ProtoHelpers::isRequired($f)) {
                    $this->requiredSubFields = $this->requiredSubFields
                        ->append(new FieldDetails($this->catalog, $fDesc, $f));
                }
            }
        }
    }

    private function determineIsRequired(DescriptorProto $containingMessage, FieldDescriptorProto $field)
    {
        $isRequired = ProtoHelpers::isRequired($field);
        $fullName = $containingMessage->desc->getFullName();
        if ($isRequired) {
            if (isset(self::$optionalToRequiredFixes[$fullName])) {
                if (in_array($field->getName(), self::$optionalToRequiredFixes[$fullName])) {
                    // Force field to be optional (even though it's required) to preserve BC
                    return false;
                }
            }
        } else {
            if (isset(self::$requiredToOptionalFixes[$fullName])) {
                if (in_array($field->getName(), self::$requiredToOptionalFixes[$fullName])) {
                    // Force field to be required (even though it's optional) to preserve BC
                    return true;
                }
            }
        }
        return $isRequired;
    }

    public function toOneofWrapperType(string $serviceNamespace): ?Type
    {
        if (!$this->isOneOf) {
            return null;
        }

        // Mirror of the wrapper class typing logic in OneofWrapperGenerator::generateClass.
        $oneofDesc = $this->containingMessage->getOneofDecl()[$this->oneOfIndex];
        $oneofWrapperClassName = Helpers::toUpperCamelCase($oneofDesc->getName()) . "Oneof";
        $namespace = $serviceNamespace . "\\" . $this->containingMessage->getName();
        $generatedOneofWrapperType = Type::fromName("$namespace\\$oneofWrapperClassName");
        return $generatedOneofWrapperType;
    }

    /**
     * Returns true if $field is the first field encountered in the containing oneof.
     */
    public function isFirstFieldInOneof(): bool
    {
        if (!$this->isOneOf) {
            return false;
        }

        // Check if the containing message has another field in this oneof that precedes
        // $field. If so, this oneof has already been handled.
        // Oneof fields should appear in increasing field number order.
        $containingMessage = $this->containingMessage;
        foreach ($containingMessage->getField() as $containingMessageFieldDescProto) {
            if (!$containingMessageFieldDescProto->hasOneofIndex()
                || $containingMessageFieldDescProto->getOneofIndex() !== $this->oneOfIndex) {
                continue;
            }
            // This is the first field encountered in this oneof group.
            return $containingMessageFieldDescProto->getNumber() === $this->number;
        }
        return false;
    }

    /**
     * Returns the oneof descriptor that corresponds to this field, if it is part of a oneof.
     * Otherwise, returns null.
     *
     * @return ?OneofDescriptorProto
     */
    public function getOneofDesc()
    {
        return !$this->isOneOf ? null : $this->containingMessage->getOneofDecl()[$this->oneOfIndex];
    }

    /**
     * Create an example value based off the type of this field.
     *
     * @param SourceFileContext $ctx The context this field is attached to.
     * @param bool $formatStringWithBrackets Whether or not to format a string
     *             value as "'[VALUE]'" or "'value'".
     * @param bool $ignoredRepeated If the field is repeated, setting this value
     *             to true will return a value of the singular type (treats it as non-repeated).
     * @return mixed
     */
    public function exampleValue(
        SourceFileContext $ctx,
        bool $formatStringWithBrackets = false,
        bool $ignoreRepeated = false
    ) {
        if (!$ignoreRepeated && $this->desc->desc->isRepeated()) {
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
                if ($formatStringWithBrackets) {
                    return '[' . strtoupper($this->name) . ']';
                }

                return $this->name;
            case GPBType::MESSAGE: // 11
                return AST::new($ctx->type(Type::fromField($this->catalog, $this->desc->desc)))();
            case GPBType::BYTES: // 12
                return '...';
            case GPBType::ENUM: // 14
                $enumValueName = $this->catalog->enumsByFullname[$this->desc->desc->getEnumType()]->getValue()[0]->getName();
                return AST::access(
                    $ctx->type(
                        Type::fromField($this->catalog, $this->desc->desc, false)
                    ),
                    AST::property($enumValueName)
                );
            default:
                throw new \Exception("No exampleValue for type: {$this->desc->getType()}");
        }
    }
}
