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

namespace Google\Generator\Utils;

use Google\Api\ResourceDescriptor;
use Google\Api\ResourceReference;
use Google\Generator\Collections\Vector;
use Google\Generator\Collections\Map;
use Google\Protobuf\Internal\DescriptorProto;

class ProtoCatalog
{
    /** @var Map *Readonly* Map<string, DescriptorProto> of all proto msgs by full name. */
    public Map $msgsByFullname;

    /** @var Map *Readonly* Map<string, EnumDescriptorProto> of all enums by full name. */
    public Map $enumsByFullname;

    /** @var Map *Readonly* Map<string, ResourceDescriptor> of all resources by type name (urn). */
    public Map $resourcesByType;

    /**
     * @var Map *Readonly* Map<string, ResourceDescriptor> of all message-defined resources
     * by full message name.
     */
    public Map $msgResourcesByFullname;

    /** @var Map *Readonly* Map<string, ResourceDescriptor> of all resources by pattern. First pattern wins, if duplicates. */
    public Map $resourcesByPattern;

    /** @var Map *Readonly* Map<string, Vector<ResourceDescriptor>> of all child/parent resources. */
    public Map $parentResourceByChildType;

    /** @var Map *Readonly* Map<string, ServiceDescriptorProto> of all services by full name. */
    public Map $servicesByFullname;

    /** @var Map *Readonly* Map<ServiceDescriptorProto, FileDescriptorProto> of all file descriptors by service descriptor. */
    public Map $filesByService;

    private static function msgPlusNested(DescriptorProto $desc): Vector
    {
        return Vector::new($desc->getNestedType())
            ->flatMap(fn ($x) => static::MsgPlusNested($x))
            ->append($desc);
    }

    /**
     * Construct a ProtoCatalog
     *
     * @param Vector $fileDescs Vector of FileDescriptorProto, for all proto files.
     */
    public function __construct(Vector $fileDescs)
    {
        $this->filesByService = $fileDescs
            ->flatMap(fn ($f) =>
                // Pair up each service with its file.
                Vector::new($f->getService())->map(fn ($s) => [$s, $f]))
            ->toMap(
                // Key: ServiceDescriptorProto
                fn($x) => $x[0],
                // Value: FileDescriptorProto
                fn($x) => $x[1]);
        
        // Flatten into pairs of [proto package, ServiceDescriptorProto], because each
        // FileDescriptorProto can contain multiple services, so each service must be
        // paired with the parent file proto package.
        $allServices = $fileDescs
            ->flatMap(fn ($x) =>
                Vector::new($x->getService())->map(fn ($s) => [$x->getPackage(), $s]));

        // Convert pairs into map of fully-qualified proto element name and ServiceDescriptorProto.
        $this->servicesByFullname = $allServices->toMap(
            // Key: fully-qualified service name.
            fn ($x) => ".{$x[0]}.{$x[1]->getName()}",
            // Value: ServiceDescriptorProto.
            fn ($x) => $x[1]);

        $allMsgs = $fileDescs
            ->flatMap(fn ($x) => Vector::new($x->getMessageType()))
            ->flatMap(fn ($x) => static::msgPlusNested($x));
        $this->msgsByFullname = $allMsgs->toMap(fn ($x) => '.' . $x->desc->getFullName());

        $allEnums = $fileDescs
            ->flatMap(fn ($x) => Vector::new($x->getEnumType()))
            ->concat($allMsgs->flatMap(fn ($x) => Vector::new($x->getEnumType())));
        $this->enumsByFullname = $allEnums->toMap(fn ($x) => '.' . $x->desc->getFullName());

        $messagesResourceDefs = $allMsgs
            ->map(fn ($x) => ProtoHelpers::getCustomOption($x, CustomOptions::GOOGLE_API_RESOURCEDEFINITION, ResourceDescriptor::class))
            ->filter(fn ($x) => !is_null($x));
        $this->msgResourcesByFullname = $allMsgs
          ->toMap(
              fn ($x) => $x->desc->getFullName(),
              fn ($x) =>  ProtoHelpers::getCustomOption($x, CustomOptions::GOOGLE_API_RESOURCEDEFINITION, ResourceDescriptor::class)
          )->filter(fn ($k, $v) => !is_null($v));
        $fileResourceDefs = $fileDescs
            ->flatMap(fn ($x) => ProtoHelpers::getCustomOptionRepeated($x, CustomOptions::GOOGLE_API_RESOURCEDEFINITION, ResourceDescriptor::class));
        $resourceDefs = $messagesResourceDefs->concat($fileResourceDefs);
        $this->resourcesByType = $resourceDefs->toMap(fn ($x) => $x->getType());
        // Use 'distinct()' here to keep just the first resource if there are multiple resources with the same pattern.
        $this->resourcesByPattern = $resourceDefs->flatMap(fn ($res) =>
                Vector::new($res->getPattern())->map(fn ($pattern) => [$pattern, $res]))
            ->distinct(fn ($x) => $x[0])
            ->toMap(fn ($x) => $x[0], fn ($x) => $x[1]);

        $order = $fileResourceDefs->concat($messagesResourceDefs)
            ->map(fn ($x, $i) => [$i, $x])
            ->toMap(fn ($x) => $x[1]->getType(), fn ($x) => $x[0]);
        $this->parentResourceByChildType = $allMsgs
            ->flatMap(fn ($x) => Vector::new($x->getField()))
            ->map(fn ($x) => ProtoHelpers::getCustomOption($x, CustomOptions::GOOGLE_API_RESOURCEREFERENCE, ResourceReference::class))
            ->filter(fn ($x) => !is_null($x) && $x->getChildType() !== '')
            ->map(fn ($x) => $x->getChildType())
            ->distinct()
            ->map(function ($childType) use ($order) {
                if ($childType === '*') {
                    return null;
                }
                $childPatterns = Vector::new($this->resourcesByType[$childType]->getPattern());
                if ($childPatterns->any(fn ($x) => $x === '*')) {
                    return null;
                }
                $parentPatterns = $childPatterns->map(fn ($childPattern) => static::parentPattern($childPattern));
                // Ordering matters, to exactly reproduce monolith behaviour.
                // parents must be in file order of option-defined resource, followed by file order of message-defined resource.
                // This is important when selecting a resource to use in test and example methods.
                return [$childType, $parentPatterns->map(fn ($x) => $this->resourcesByPattern[$x])->orderBy(fn ($x) => $order[$x->getType()])];
            })
            ->filter(fn ($x) => !is_null($x))
            ->toMap(fn ($x) => $x[0], fn ($x) => $x[1]);
    }

    private static function parentPattern(string $pattern): string
    {
        $parts = Vector::new(explode('/', $pattern));
        $skipCount = (strpos($parts[-1], '}') > 0) ? 2 : 1;
        $parts = $parts->skipLast($skipCount);
        if (count($parts) === 0) {
            throw new \Exception("Resource-name pattern '{$pattern}' has no parent.");
        }
        return $parts->join('/');
    }
}
