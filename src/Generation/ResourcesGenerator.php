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

class ResourcesGenerator
{
    public static function generateDescriptorConfig(ServiceDetails $serviceDetails): string
    {
        $methods = $serviceDetails->methods
            ->filter(fn($x) => $x->methodType === MethodDetails::LRO)
            ->toMap(
                fn($x) => $x->name,
                fn($x) => AST::array([
                    'longRunning' => AST::array([
                        'operationReturnType' => $x->lroResponseType->getFullname(),
                        'metadataReturnType' => $x->lroMetadataType->getFullname(),
                        'initialPollDelayMillis' => '60000', // TODO: Check these are the correct values.
                        'pollDelayMultiplier' => '1.0',
                        'maxPollDelayMillis' => '60000',
                        'totalPollTimeoutMillis' => '86400000',
                    ])
                ])
            );

        $return = AST::return(
            AST::array([
                'interfaces' => AST::array([
                    $serviceDetails->serviceName => AST::array($methods)
                ])
            ])
        );

        return "<?php\n\n{$return->toCode()};";
    }
}
