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

use Google\Protobuf\Internal\MethodDescriptorProto;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\Type;

class MethodDetails
{
    public static function create(ServiceDetails $svc, MethodDescriptorProto $desc): MethodDetails
    {
        // TODO: Handle different method types; e.g. streaming, LRO, ...
        return new MethodDetails($svc, $desc);
    }

    /** @var string The name of the method, as named in the proto. */
    public string $name;

    /** @var string The name of this method, as required for PHP code. */
    public string $methodName;

    /** @var Type The type of the method request message. */
    public Type $requestType;

    /** @var Type The type of the method response message. */
    public Type $responseType;

    /** @var Vector Vector of FieldDetails; All required fields. */
    public Vector $requiredFields;

    /** @var Vector Vector of FieldDetails; All optional fields. */
    public Vector $optionalFields;

    private function __construct(ServiceDetails $svc, MethodDescriptorProto $desc)
    {
        $catalog = $svc->catalog;
        $inputMsg = $catalog->msgsByFullname[$desc->getInputType()];
        $outputMsg = $catalog->msgsByFullname[$desc->getOutputType()];
        $this->name = $desc->getName();
        $this->methodName = Helpers::toCamelCase($this->name);
        $this->requestType = Type::fromMessage($inputMsg->desc);
        $this->responseType = Type::fromMessage($outputMsg->desc);
        $allFields = Vector::new($inputMsg->getField())->map(fn($x) => new FieldDetails($x));
        $this->requiredFields = $allFields->filter(fn($x) => $x->isRequired);
        $this->optionalFields = $allFields->filter(fn($x) => !$x->isRequired);
    }
}
