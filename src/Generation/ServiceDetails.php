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

use Google\Api\ResourceDescriptor;
use Google\Api\ResourceReference;
use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\CustomOptions;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\Transport;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\ServiceDescriptorProto;

class ServiceDetails
{
    public const DEPRECATED_MSG = "This class will be removed in the next major version update.";

    /** @var ProtoCatalog *Readonly* The proto-catalog containing all source protos. */
    public ProtoCatalog $catalog;

    /** @var string *Readonly* The proto package name for this service. */
    public string $package;

    /** @var string *Readonly* The PHP namespace for this service.  */
    public string $namespace;

    /** @var Type *Readonly* The type of the service client class. */
    public Type $gapicClientType;

    /** @var Type *Readonly* The type of the service client V2 class. */
    public Type $gapicClientV2Type;

    /** @var Type *Readonly* The type of the empty client class. */
    public Type $emptyClientType;

    /** @var Type *Readonly* The type of the empty client V2 class. */
    public Type $emptyClientV2Type;

    /** @var Type *Readonly* The type of the gRPC client. */
    public Type $grpcClientType;

    /** @var Type *Readonly* The type of the unit-tests class. */
    public Type $unitTestsType;

    /** @var Type *Readonly* The type of the unit-tests class for V2 clients. */
    public Type $unitTestsV2Type;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    /** @var string *Readonly* The full name of the service. */
    public string $serviceName;

    /** @var string *Readonly* The canonical, short name of the service (as it appears in the proto). */
    public string $shortName;

    /** @var string *Readonly* The default hostname of the service. */
    public string $defaultHost;

    /** @var int *Readonly* The default port of the service. */
    public int $defaultPort;

    /** @var Vector *Readonly* Vector of strings; the default auth scopes of the service. */
    public Vector $defaultScopes;

    /** @var string *Readonly* The client-config filename. */
    public string $clientConfigFilename;

    /** @var string *Readonly* The descriptor-config filename. */
    public string $descriptorConfigFilename;

    /** @var string *Readonly* The grpc-config filename. */
    public string $grpcConfigFilename;

    /** @var string *Readonly* The rest-config filename. */
    public string $restConfigFilename;

    /** @var Vector *Readonly* Vector of MethodDetails; one element per RPC method. */
    public Vector $methods;

    /** @var string *Readonly* Variable name for a client of this service. */
    public string $clientVarName;

    /** @var string *Readonly* The path to the source .proto file containing this service. */
    public string $filePath;

    /** @var ?string *Readonly* The group name used for grouping unit test. */
    public ?string $unitTestGroupName;

    /** @var bool *Readonly* This service contains at least one LRO method. */
    public bool $hasLro;

    /** @var bool *Readonly* This service contains at least one custom operation method. */
    public bool $hasCustomOp;

    /** @var ServiceDescriptorProto *Readonly* The service named as the custom operation service to use. */
    public ServiceDescriptorProto $customOperationService;

    /** @var Type *Readonly* The Type of the service's operations_service client. */
    public Type $customOperationServiceClientType;

    public bool $hasCustomOpDelete;

    public bool $hasCustomOpCancel;

    /** @var Vector *Readonly* Vector of ResourcePart; all unique resources and patterns, in alphabetical order. */
    public Vector $resourceParts;

    /**
     * @var Vector *Readonly* Vector of ResourceDetails; all resource definitions in this service's
     * RPC's request messages.
     */
    public Vector $resourceDefs;

    /** @var bool *Readonly* Whether the service is deprecated. */
    public bool $isDeprecated = false;

    /**
     * @var int *Readonly* The transport type supported in this client. Valid options are in Transport.php.
     */
    public int $transportType;

    public function __construct(
        ProtoCatalog $catalog,
        string $namespace,
        string $package,
        ServiceDescriptorProto $desc,
        FileDescriptorProto $fileDesc,
        int $transportType = Transport::GRPC_REST
    ) {
        $this->catalog = $catalog;
        $this->package = $package;
        $this->namespace = $namespace;
        $this->transportType = $transportType;
        $this->gapicClientType = Type::fromName("{$namespace}\\Gapic\\{$desc->getName()}GapicClient");
        $this->emptyClientType = Type::fromName("{$namespace}\\{$desc->getName()}Client");
        $this->gapicClientV2Type = Type::fromName("{$namespace}\\Client\\BaseClient\\{$desc->getName()}BaseClient");
        $this->emptyClientV2Type = Type::fromName("{$namespace}\\Client\\{$desc->getName()}Client");
        $this->grpcClientType = Type::fromName("{$namespace}\\{$desc->getName()}GrpcClient");
        $nsVersionAndSuffix = Helpers::nsVersionAndSuffixPath($namespace);
        $unitTestNs = $nsVersionAndSuffix === '' ?
            "{$namespace}\\Tests\\Unit" :
            substr($namespace, 0, -strlen($nsVersionAndSuffix)) . 'Tests\\Unit\\' . str_replace('/', '\\', $nsVersionAndSuffix);
        $this->unitTestsType = Type::fromName("{$unitTestNs}\\{$desc->getName()}ClientTest");
        $this->unitTestsV2Type = Type::fromName("{$unitTestNs}\\Client\\{$desc->getName()}ClientTest");
        $this->docLines = $desc->leadingComments;
        $this->serviceName = "{$package}.{$desc->getName()}";
        $this->shortName = $desc->getName();
        $this->defaultHost = ProtoHelpers::getCustomOption($desc, CustomOptions::GOOGLE_API_DEFAULTHOST);
        $this->defaultPort = 443;
        $this->defaultScopes =
            Vector::new(explode(',', ProtoHelpers::getCustomOption($desc, CustomOptions::GOOGLE_API_OAUTHSCOPES) ?? ''))
                ->filter(fn ($x) => $x != '')
                ->map(fn ($x) => trim($x));
        $this->clientConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_client_config.json';
        $this->descriptorConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_descriptor_config.php';
        $this->grpcConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_grpc_config.json';
        $this->restConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_rest_client_config.php';
        $this->methods = Vector::new($desc->getMethod())->map(fn ($x) => MethodDetails::create($this, $x))
                                                        ->orderBy(fn ($x) => $x->name);
        $customOperations = $this->methods->filter(fn ($x) => $x->methodType === MethodDetails::CUSTOM_OP);
        $this->hasCustomOp = $customOperations->count() > 0;
        if ($this->hasCustomOp) {
            // Technically there could be multiple different named operation services,
            // but for simplicity we will assume they are all the same and use the first.
            $this->customOperationService = $customOperations[0]->operationService;
            
            // Determine if the operation service implements the Cancel and/or the Delete RPCs.
            $this->hasCustomOpCancel = Vector::new($this->customOperationService->getMethod())->any(fn ($x) => $x->getName() === 'Cancel');
            $this->hasCustomOpDelete = Vector::new($this->customOperationService->getMethod())->any(fn ($x) => $x->getName() === 'Delete');
            
            // Assuming the custom operations service client is in the same namespace as the client to generate.
            $cname = $this->customOperationService->getName() . 'Client';
            $this->customOperationServiceClientType = Type::fromName("{$this->namespace}\\{$cname}");
        }
        if ($desc->hasOptions() && $desc->getOptions()->hasDeprecated()) {
            $this->isDeprecated = $desc->getOptions()->getDeprecated();
        }

        $this->clientVarName = Helpers::toCamelCase("{$desc->getName()}Client");
        $this->filePath = $fileDesc->getName();
        // This is a copy of the monolithic way of generating the test group name.
        $matches = [];
        if (preg_match('/(\\w+)(\\\\[Vv]\\d+\\w*)*$/', $namespace, $matches) === 1) {
            $this->unitTestGroupName = strtolower($matches[1]);
        } else {
            $this->unitTestGroupName = null;
        }
        $this->hasLro = $this->methods->any(fn ($x) => $x->methodType === MethodDetails::LRO);
        // Resource-names
        // Wildcard patterns are ignored.
        // A resource-name which has just a single wild-card pattern is ignored.
        $msgsSeen = Set::new();
        $gatherMsgResDefs = null;
        $gatherMsgResDefs = function (DescriptorProto $msg, int $level) use (&$gatherMsgResDefs, &$msgsSeen, $catalog): Vector {
            if ($msgsSeen[$msg->desc->getFullname()]) {
                return Vector::new([]);
            }
            $msgsSeen = $msgsSeen->add($msg->desc->getFullname());
            // Only top-level resource-defs are included; matches monolith behaviour.
            // TODO(vNext): Decide if this behaviour is correct, posibly modify.
            $messageResourceDef = $level === 0 ?
                ProtoHelpers::getCustomOption($msg, CustomOptions::GOOGLE_API_RESOURCEDEFINITION, ResourceDescriptor::class) :
                null;
            $fields = Vector::new($msg->getField());
            $resourceRefs = $fields
                ->map(fn ($x) => ProtoHelpers::getCustomOption($x->desc, CustomOptions::GOOGLE_API_RESOURCEREFERENCE, ResourceReference::class))
                ->filter(fn ($x) => !is_null($x));
            $typeRefResourceDefs = $resourceRefs
                ->filter(fn ($x) => $x->getType() !== '' && $x->getType() !== '*')
                ->map(fn ($x) => $catalog->resourcesByType[$x->getType()]);
            $childTypeRefResourceDefs = $resourceRefs
                ->filter(fn ($x) => $x->getChildType() !== '')
                ->flatMap(fn ($x) => $catalog->parentResourceByChildType->get($x->getChildType(), Vector::new([])));

            // Find all fields (only one level deep) that are resources defined elsewhere.
            $typeFieldRefResourceDefs = Vector::new([]);
            $childFieldTypeRefResourceDefs = Vector::new([]);
            if ($level == 0) {
                $fieldDetails = $fields
                ->filter(fn ($f) => !is_null($f))
                ->map(fn ($f) => new FieldDetails($catalog, $msg, $f));

                $fullnameFn = function ($fd) {
                    return substr($fd->fullname, 0, 1) === '.' ? substr($fd->fullname, 1) : $fd->fullname;
                };
                $fieldResourceRefs = $fieldDetails
                ->filter(fn ($x) => $x->isRequired
                  && $x->isMessage
                  && !is_null($x->fullname))
                  ->map(fn ($x) => $catalog->msgResourcesByFullname->get($fullnameFn($x), null))
                  ->filter(fn ($x) => !is_null($x));
                $typeFieldRefResourceDefs = $fieldResourceRefs
                ->filter(fn ($x) => $x->getType() !== '' && $x->getType() !== '*')
                ->map(fn ($x) => $catalog->resourcesByType[$x->getType()]);
                $childFieldTypeRefResourceDefs = $fieldResourceRefs
                ->filter(fn ($x) => $x->getType() !== '')
                ->flatMap(fn ($x) => $catalog->parentResourceByChildType->get($x->getType(), Vector::new([])));
            }

            // Recurse one level down into message fields; matches monolith behaviour.
            // TODO(vNext): Decide if this behaviour is correct, posibly modify.
            if ($level === 0) {
                $nestedDefs = $fields
                    ->filter(fn ($f) => $f->getType() === GPBType::MESSAGE)
                    ->map(fn ($f) => $catalog->msgsByFullname[$f->desc->getMessageType()])
                    ->flatMap(fn ($nestedMsg) => $gatherMsgResDefs($nestedMsg, $level + 1));
            } else {
                $nestedDefs = Vector::new([]);
            }
            return $typeRefResourceDefs
              ->concat($typeFieldRefResourceDefs)
              ->concat($childFieldTypeRefResourceDefs)
              ->concat($childTypeRefResourceDefs)
              ->append($messageResourceDef)
              ->concat($nestedDefs);
        };
        $this->resourceDefs = $this->methods
            ->flatMap(fn ($x) => $gatherMsgResDefs($x->inputMsg, 0))
            ->filter(fn ($x) => !is_null($x))
            ->map(fn ($res) => new ResourceDetails($res));
        $this->resourceParts = $this->resourceDefs
            ->filter(fn ($x) => $x->patterns->any())
            // CAREFUL! This is a mix of ResourceDetails and ResourcePatternDetails.
            ->concat($this->resourceDefs->map(fn ($res) => count($res->patterns) === 1 ? Vector::new([]) : $res->patterns)->flatten())
            ->distinct(fn ($x) => $x->getNameCamelCase())
            ->orderBy(fn ($x) => $x->getNameCamelCase());
    }

    /**
     * Adds the methods from $mixinService to this one.
     * @param ServiceDetails $mixinService the service to be mixed into this one.
     */
    public function addMixins(ServiceDetails $mixinService, array $rpcNameBlocklist): void
    {
        // Less elegant because  PHP 7.2 doesn't support multiline lambdas.
        $mixinMethods = $mixinService->methods
          ->filter(fn ($m) => !in_array($m->name, $rpcNameBlocklist))
          ->orderBy(fn ($m) => $m->name);
        foreach ($mixinMethods as $method) {
            $method->mixinServiceFullname = $mixinService->serviceName;
        }
        $originalMethods = $this->methods;
        $this->methods = $originalMethods->concat($mixinMethods);
    }

    public function packageFullName(string $typeName): string
    {
        return
            strpos($typeName, '.') === false ? ".{$this->package}.{$typeName}" :
            (substr($typeName, 0, 1) === '.' ? $typeName : ".{$typeName}");
    }

    public function isGa(): bool
    {
        $ns_components = explode("\\", $this->namespace);
        $version = strtolower($ns_components[array_key_last($ns_components)]);
        return strpos($version, 'alpha') === false && strpos($version, 'beta') === false;
    }

    public function setMethods(Vector $newMethods) : void
    {
        // Note: This seemingly-redundant method exists to enable a future refactoring
        // of all properties labelled as "readonly" to actually be private properties with getters.
        $this->methods = $newMethods;
    }
}
