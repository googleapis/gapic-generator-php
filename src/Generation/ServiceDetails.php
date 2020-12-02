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
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\ServiceDescriptorProto;

class ServiceDetails {
    /** @var ProtoCatalog *Readonly* The proto-catalog containing all source protos. */
    public ProtoCatalog $catalog;

    /** @var string *Readonly* The proto package name for this service. */
    public string $package;

    /** @var Type *Readonly* The type of the service client class. */
    public Type $gapicClientType;

    /** @var Type *Readonly* The type of the empty client class. */
    public Type $emptyClientType;

    /** @var Type *Readonly* The type of the gRPC client. */
    public Type $grpcClientType;

    /** @var Type *Readonly* The type of the unit-tests class. */
    public Type $unitTestsType;

    /** @var Vector *Readonly* Vector of strings; the documentation lines from the source proto. */
    public Vector $docLines;

    /** @var string *Readonly* The full name of the service. */
    public string $serviceName;

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

    /** @var string *Readonly* The group name used for grouping unit test. */
    public string $unitTestGroupName;

    /** @var bool *Readonly* This service contains at least one LRO method. */
    public bool $hasLro;

    /** @var Set *Readonly* Vector of ResourcePart; all unique resources and patterns, in alphabetical order. */
    public Vector $resourceParts;

    public function __construct(
        ProtoCatalog $catalog,
        string $namespace,
        string $package,
        ServiceDescriptorProto $desc,
        FileDescriptorProto $fileDesc
    ) {
        $this->catalog = $catalog;
        $this->package = $package;
        $this->gapicClientType = Type::fromName("{$namespace}\\Gapic\\{$desc->getName()}GapicClient");
        $this->emptyClientType = Type::fromName("{$namespace}\\{$desc->getName()}Client");
        $this->grpcClientType = Type::fromName("{$namespace}\\{$desc->getName()}GrpcClient");
        $nsVersion = Helpers::nsVersion($namespace);
        $unitTestNs = is_null($nsVersion) ? "{$namespace}\\Tests\\Unit" :
            substr($namespace, 0, strlen($namespace) - strlen($nsVersion) - 1) . '\\Tests\\Unit\\' . $nsVersion;
        $this->unitTestsType = Type::fromName("{$unitTestNs}\\{$desc->getName()}ClientTest");
        $this->docLines = $desc->leadingComments;
        $this->serviceName = "{$package}.{$desc->getName()}";
        $this->defaultHost = ProtoHelpers::getCustomOption($desc, CustomOptions::GOOGLE_API_DEFAULTHOST);
        $this->defaultPort = 443;
        $this->defaultScopes =
            Vector::new(explode(',', ProtoHelpers::getCustomOption($desc, CustomOptions::GOOGLE_API_OAUTHSCOPES) ?? ''))
                ->filter(fn($x) => $x != '')
                ->map(fn($x) => trim($x));
        $this->clientConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_client_config.json';
        $this->descriptorConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_descriptor_config.php';
        $this->grpcConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_grpc_config.json';
        $this->restConfigFilename = Helpers::toSnakeCase($desc->getName()) . '_rest_client_config.php';
        $this->methods = Vector::new($desc->getMethod())->map(fn($x) => MethodDetails::create($this, $x));
        $this->clientVarName = Helpers::toCamelCase("{$desc->getName()}Client");
        $this->filePath = $fileDesc->getName();
        $unitTestGroupName = strtolower($desc->getName());
        if (substr($unitTestGroupName, strlen($unitTestGroupName) - 7, 7) === 'service') {
            $unitTestGroupName = substr($unitTestGroupName, 0, strlen($unitTestGroupName) - 7);
        }
        $this->unitTestGroupName = $unitTestGroupName;
        $this->hasLro = $this->methods->any(fn($x) => $x->methodType === MethodDetails::LRO);
        // Resource-names
        // Wildcard patterns are ignored.
        // A resource-name which has just a single wild-card pattern is ignored.
        $messageResourceDefs = $this->methods
            ->map(fn($x) => ProtoHelpers::getCustomOption($x->inputMsg, CustomOptions::GOOGLE_API_RESOURCEDEFINITION, ResourceDescriptor::class))
            ->filter(fn($x) => !is_null($x));
        $resourceRefs = $this->methods
            ->flatMap(fn($x) => $x->allFields)
            ->map(fn($x) => ProtoHelpers::getCustomOption($x->desc, CustomOptions::GOOGLE_API_RESOURCEREFERENCE, ResourceReference::class))
            ->filter(fn($x) => !is_null($x));
        $typeRefResourceDefs = $resourceRefs
            ->filter(fn($x) => $x->getType() !== '')
            ->map(fn($x) => $catalog->resourcesByType[$x->getType()]);
        // Child-type refs: Ignore any that contain a wildcard resource; else find parent resource by pattern(s).
        $childTypeRefResourceDefs = $resourceRefs
            ->filter(fn($x) => $x->getChildType() !== '')
            ->map(fn($x) => $catalog->resourcesByType[$x->getChildType()])
            ->filter(fn($childResource) => !Vector::new($childResource->getPattern())->any(fn($pattern) => $pattern === '*'))
            ->flatMap(fn($childResource) => Vector::new($childResource->getPattern()))
            ->map(fn($childPattern) => $catalog->resourcesByPattern[$this->parentPattern($childPattern)]);
        $resourceDefs = $messageResourceDefs
            ->concat($typeRefResourceDefs)
            ->concat($childTypeRefResourceDefs)
            ->map(fn($res) => new ResourceDetails($res));
        $this->resourceParts = $resourceDefs
            ->filter(fn($x) => $x->patterns->any())
            ->concat($resourceDefs->flatMap(fn($res) => count($res->patterns) === 1 ? Vector::new([]) : $res->patterns))
            ->distinct(fn($x) => $x->getNameCamelCase())
            ->orderBy(fn($x) => $x->getNameCamelCase());
    }

    private function parentPattern(string $pattern): string
    {
        $parts = Vector::new(explode('/', $pattern));
        $skipCount = (strpos($parts[-1], '}') > 0) ? 2 : 1;
        $parts = $parts->skipLast($skipCount);
        if (count($parts) === 0) {
            throw new \Exception("Resource-name pattern '{$pattern}' has no parent.");
        }
        return $parts->join('/');
    }

    public function packageFullName(string $typeName): string
    {
        return
            strpos($typeName, '.') === false ? ".{$this->package}.{$typeName}" :
            (substr($typeName, 0, 1) === '.' ? $typeName : ".{$typeName}");
    }
}
