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

use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ProtoCatalog;
use Google\Gapic\Metadata\GapicMetadata;

class GapicMetadataGenerator
{
    /**
     *  Generates the contents of a gapic_metadata.json file.
     */
    public static function generate(ProtoCatalog $catalog, Vector $fileDescriptors, string $namespace): string
    {
        return (new GapicMetadataGenerator($catalog, $fileDescriptors, $namespace))->generateImpl();
    }

    private ProtoCatalog $catalog;
    private Vector $fileDescriptors;
    private string $namespace;
    private GapicMetadata $gapicMetadata;

    private function __construct(ProtoCatalog $catalog, Vector $fileDescriptors, string $namespace)
    {
        $this->catalog = $catalog;
        $this->fileDescriptors = $fileDescriptors;
        $this->namespace = $namespace;
        $this->gapicMetadata = new GapicMetadata();
    }

    private function generateImpl(): string
    {
        // Initialization.
        $this->gapicMetadata->setSchema("1.0");
        $this->gapicMetadata->setComment("This file maps proto services/RPCs to the corresponding library clients/methods");
        $this->gapicMetadata->setLanguage("php");
        $this->gapicMetadata->setLibraryPackage($this->namespace);

        $gapicMetadataServices = [];
        // TODO: Filter out mixed-in services.
        foreach ($this->fileDescriptors as $fileDesc) {
            // Only need to set this once.
            if (empty($this->gapicMetadata->getProtoPackage)) {
                $this->gapicMetadata->setProtoPackage($fileDesc->getPackage());
            }
            foreach ($fileDesc->getService() as $index => $service) {
                $serviceName = "{$fileDesc->getPackage()}.{$service->getName()}";
                $serviceDetails =
          new ServiceDetails($this->catalog, $this->namespace, $fileDesc->getPackage(), $service, $fileDesc);
                $gapicMetadataClients = [];
                $rpcs = $serviceDetails->methods->toArray(
                    fn ($method) => $method->name,
                    fn ($method) => [$method->methodName]
                );

                $libraryClient = new GapicMetadata\ServiceAsClient();
                $libraryClient->setLibraryClient($serviceDetails->gapicClientType->name);
                $libraryClient->setRpcs(
                    $serviceDetails->methods->toArray(
                        fn ($method) => $method->name,
                        fn ($method) => (new GapicMetadata\MethodList())->setMethods([$method->methodName])));
                $transport = new GapicMetadata\ServiceForTransport();
                $transport->setClients(['grpc' => $libraryClient]);
                $gapicMetadataServices[$serviceDetails->serviceName] = $transport;
            }
        }
        $this->gapicMetadata->setServices($gapicMetadataServices);
        return json_encode(json_decode($this->gapicMetadata->serializeToJsonString()), JSON_PRETTY_PRINT);
    }
}
