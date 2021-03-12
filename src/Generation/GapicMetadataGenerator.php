<?php
/*
 * Copyright 2021 Google LLC
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

use Google\Gapic\Metadata\GapicMetadata;

class GapicMetadataGenerator
{
    /**
     *  Generates the contents of a gapic_metadata.json file.
     */
    public static function generate(array $servicesToGenerate, string $namespace): string
    {
        $gapicMetadata = new GapicMetadata();
        // Initialization.
        $gapicMetadata->setSchema("1.0");
        $gapicMetadata->setComment("This file maps proto services/RPCs to the corresponding library clients/methods");
        $gapicMetadata->setLanguage("php");
        $gapicMetadata->setLibraryPackage($namespace);

        $gapicMetadataServices = [];
        foreach ($servicesToGenerate as $service) {
            // Only need to set this once.
            if (empty($gapicMetadata->getProtoPackage)) {
                $gapicMetadata->setProtoPackage($service->package);
            }
            $gapicMetadataClients = [];
            $rpcs = $service->methods->toArray(
                fn ($method) => $method->name,
                fn ($method) => [$method->methodName]
            );

            $libraryClient = new GapicMetadata\ServiceAsClient();
            $libraryClient->setLibraryClient($service->gapicClientType->name);
            $libraryClient->setRpcs(
                $service->methods->toArray(
                        fn ($method) => $method->name,
                        fn ($method) => (new GapicMetadata\MethodList())->setMethods([$method->methodName])
                    )
                );
            $transport = new GapicMetadata\ServiceForTransport();
            $transport->setClients(['grpc' => $libraryClient]);
            $gapicMetadataServices[$service->shortName] = $transport;
            }
        $gapicMetadata->setServices($gapicMetadataServices);
        return json_encode(json_decode($gapicMetadata->serializeToJsonString()), JSON_PRETTY_PRINT);
    }
}
