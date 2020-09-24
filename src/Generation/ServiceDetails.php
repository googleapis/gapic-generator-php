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

use Google\Generator\Collections\Vector;
use Google\Generator\Utils\CustomOptions;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\ServiceDescriptorProto;

class ServiceDetails {
    /** @var ProtoCatalog *Readonly* The proto-catalog containing all source protos. */
    public ProtoCatalog $catalog;

    /** @var string *Readonly* The type of the service client class. */
    public Type $gapicClientType;

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

    public function __construct(ProtoCatalog $catalog, string $namespace, string $package, ServiceDescriptorProto $desc)
    {
        $this->catalog = $catalog;
        $this->gapicClientType = Type::fromName("{$namespace}\\Gapic\\{$desc->getName()}GapicClient");
        $this->docLines = $desc->leadingComments;
        $this->serviceName = "{$package}.{$desc->getName()}";
        $this->defaultHost = ProtoHelpers::GetCustomOption($desc, CustomOptions::GOOGLE_API_DEFAULTHOST);
        $this->defaultPort = 443;
        $this->defaultScopes =
            Vector::New(explode(',', ProtoHelpers::GetCustomOption($desc, CustomOptions::GOOGLE_API_OAUTHSCOPES) ?? ''))
                ->filter(fn($x) => $x != '')
                ->map(fn($x) => trim($x));
        // TODO: More details...
    }
}
