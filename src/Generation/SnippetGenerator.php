<?php
/*
 * Copyright 2022 Google LLC
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
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Set;
use Google\Generator\Utils\Helpers;

class SnippetGenerator
{
    public static function generate(int $licenseYear, ServiceDetails $serviceDetails): Map
    {
        return (new SnippetGenerator($licenseYear, $serviceDetails))->generateImpl();
    }

    private int $licenseYear;
    private ServiceDetails $serviceDetails;

    private function __construct(int $licenseYear, ServiceDetails $serviceDetails)
    {
        $this->licenseYear = $licenseYear;
        $this->serviceDetails = $serviceDetails;
    }

    private function generateImpl(): Map
    {
        $files = Map::new();
        $uses = Set::new();

        foreach ($this->serviceDetails->methods as $method) {
            $uses = $uses->add($this->serviceDetails->emptyClientType->getFullname(true));
            $uses = $uses->add($method->requestType->getFullname(true));
            foreach ($method->allFields as $field) {
                var_dump($field->name);
                if ($field->isMessage) {
                    $uses = $uses->add($field->typeSingular->getFullname(true));
                }
            }

            $regionTag = $this->generateRegionTag($method->name);
            $files = $files->set(
                $method->name,
                AST::file()
                    ->withApacheLicense($this->licenseYear)
                    ->withGeneratedFromProtoCodeWarning($this->serviceDetails->filePath, $this->serviceDetails->isGa())
                    ->withBlock(
                        AST::block(
                            AST::literal("require_once '../../../vendor/autoload.php';" . PHP_EOL),
                            AST::literal("// [START $regionTag]"),
                            AST::literal(
                                $uses->toVector()->map(fn ($use) => "use {$use};")->join()
                            ),
                            ExamplesGenerator::build($this->serviceDetails)->rpcMethodExample($method),
                            AST::literal("// [END $regionTag]")
                        )
                    )
                );
        }

        return $files;
    }

    private function generateRegionTag($methodName)
    {
        $version = strtolower(Helpers::nsVersionAndSuffixPath($this->serviceDetails->namespace)) ?: '_';
        if ($version !== '_') {
            $version = '_' . $version . '_';
        }
        $serviceParts = explode('.', $this->serviceDetails->serviceName);
        $hostNameParts = explode('.', $this->serviceDetails->defaultHost);
        $serviceName = end($serviceParts);
        $shortName = $hostNameParts[0];

        return $shortName . $version . 'generated_' . $serviceName . '_' . $methodName . '_sync';
    }
}
