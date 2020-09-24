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

use Google\Generator\Collections\Vector;
use Google\Generator\Collections\Map;
use Google\Generator\Utils\ProtoHelpers;
use Google\Protobuf\Internal\Descriptor;

class ProtoCatalog
{
    /**
     * @var Map *Readonly* Map<string, Descriptor> of all proto msgs by full name.
     *     The Descriptors here have an extra `underlyingProto` property.
     */
    public Map $msgsByFullname;

    private static function MsgPlusNested(Descriptor $desc): Vector
    {
        return Vector::New($desc->getNestedType())
            ->flatMap(fn($x) => static::MsgPlusNested($x))
            ->append($desc);
    }

    /**
     * Construct a ProtoCatalog
     *
     * @param Vector $fileDescs Vector of FileDescriptorProto, for all proto files.
     */
    public function __construct(Vector $fileDescs)
    {
        // Add an extra `underlyingProto` property to all descriptors, as the descriptors
        // don't contain sufficient data themselves.
        $topLevelMsgs = $fileDescs->flatMap(fn($fileDesc) =>
            Vector::New($fileDesc->getMessageType())->map(fn($msgProto) =>
                ProtoHelpers::AddProto(Descriptor::buildFromProto($msgProto, $fileDesc, ''), $msgProto)));
        $allMsgs = $topLevelMsgs->flatMap(fn($x) => static::MsgPlusNested($x));
        $this->msgsByFullname = $allMsgs->toMap(fn($x) => $x->getFullName());
    }
}
