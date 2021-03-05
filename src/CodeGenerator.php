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

namespace Google\Generator;

use Google\Generator\Collections\Vector;
use Google\Generator\Generation\EmptyClientGenerator;
use Google\Generator\Generation\GapicClientGenerator;
use Google\Generator\Generation\ResourcesGenerator;
use Google\Generator\Generation\UnitTestsGenerator;
use Google\Generator\Generation\ServiceDetails;
use Google\Generator\Generation\SourceFileContext;
use Google\Generator\Utils\Formatter;
use Google\Generator\Utils\GapicYamlConfig;
use Google\Generator\Utils\GrpcServiceConfig;
use Google\Generator\Utils\ServiceYamlConfig;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Protobuf\Internal\FileDescriptorSet;

class CodeGenerator
{
    /**
     * Generate from a FileSet descriptor; used when evoked from the command-line.
     *
     * @param string $descBytes The raw bytes of the proto descriptor, as generated using `protoc -o ...`
     * @param string $package The package name to generate.
     * @param int $licenseYear The year to use in license headers.
     * @param ?string $grpcServiceConfigJson Optional grpc-serv-config json string.
     * @param ?string $gapicYaml Optional gapic configuration yaml string.
     * @param ?string $serviceYaml Optional service configuration yaml string.
     *
     * @return string[]
     */
    public static function generateFromDescriptor(
        string $descBytes,
        string $package,
        int $licenseYear,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?string $serviceYaml
    ) {
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $fileDescs = Vector::new($descSet->getFile());
        $filesToGenerate = $fileDescs
            ->filter(fn($x) => substr($x->getPackage(), 0, strlen($package)) === $package)
            ->map(fn($x) => $x->getName());
        yield from static::generate($fileDescs, $filesToGenerate, $licenseYear, $grpcServiceConfigJson, $gapicYaml, $serviceYaml);
    }

    /**
     * Generate from a vector of proto file descriptors, only generating the files listed
     * in $filesToGenerate.
     *
     * @param Vector $fileDescs A vector of FileDescriptorProto, containing all proto source files.
     * @param Vector $filesToGenerate A vector of string, containing full names of all files to generate.
     * @param int $licenseYear The year to use in license headers.
     * @param ?string $grpcServiceConfigJson Optional grpc-serv-config json string.
     * @param ?string $gapicYaml Optional gapic configuration yaml string.
     * @param ?string $serviceYaml Optional service configuration yaml string.
     *
     * @return array[] [0] (string) is relative path; [1] (string) is file content.
     */
    public static function generate(
        Vector $fileDescs,
        Vector $filesToGenerate,
        int $licenseYear,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?string $serviceYaml
    ) {
        // Augment descriptors; e.g. proto comments; higher-level descriptors; ...
        ProtoAugmenter::augment($fileDescs);
        // Create catalog of all protos and resources.
        // Note: This constructs proto-descriptors in a slightly non-standard way, not using the
        // DescriptorPool. Therefore the 'types' contained in descriptors are the proto full-name
        // strings of types, rather than direct links to the types.
        $catalog = new ProtoCatalog($fileDescs);
        // Create map of all files to generate, keyed by package name.
        $filesToGenerateSet = $filesToGenerate->toSet();
        $byPackage = $fileDescs
            ->filter(fn($x) => $filesToGenerateSet[$x->getName()])
            ->groupBy(fn($x) => $x->getPackage());
        if (count($byPackage) === 0) {
            throw new \Exception('No packages specified to build');
        }
        // Generate files for each package.
        $result = [];
        foreach ($byPackage as [$_, $singlePackageFileDescs]) {
            $namespaces = $singlePackageFileDescs
                ->map(fn($x) => ProtoHelpers::getNamespace($x))
                ->distinct();
            if (count($namespaces) > 1) {
                throw new \Exception('All files in the same package must have the same PHP namespace');
            }
            foreach (static::generatePackage($catalog, $namespaces[0], $singlePackageFileDescs, $licenseYear, $grpcServiceConfigJson, $gapicYaml, $serviceYaml) as $file) {
                $result[] = $file;
            }
        }
        return $result;
    }

    private static function generatePackage(
        ProtoCatalog $catalog,
        string $namespace,
        Vector $fileDescs,
        int $licenseYear,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?string $serviceYaml
    ) {
        $version = Helpers::nsVersionAndSuffixPath($namespace);
        if ($version !== '') {
            $version .= '/';
        }
        // $fileDescs: Vector<FileDescriptorProto>
        foreach ($fileDescs as $fileDesc)
        {
            foreach ($fileDesc->getService() as $index => $service)
            {
                $serviceName = "{$fileDesc->getPackage()}.{$service->getName()}";
                // Load various configs; if they're not provided then defaults will be used.
                $grpcServiceConfig = new GrpcServiceConfig($serviceName, $grpcServiceConfigJson);
                $gapicYamlConfig = new GapicYamlConfig($serviceName, $gapicYaml);
                $serviceYamlConfig = new ServiceYamlConfig($serviceYaml);
                // Load service details.
                $serviceDetails = new ServiceDetails($catalog, $namespace, $fileDesc->getPackage(), $service, $fileDesc, $gapicYamlConfig);
                // TODO: Refactor this code when it's clearer where the common elements are.
                // Service client.
                $ctx = new SourceFileContext($serviceDetails->gapicClientType->getNamespace(), $licenseYear);
                $file = GapicClientGenerator::generate($ctx, $serviceDetails, $gapicYamlConfig);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield ["src/{$version}Gapic/{$serviceDetails->gapicClientType->name}.php", $code];
                // Very thin service client wrapper, for manual code additions if required.
                $ctx = new SourceFileContext($serviceDetails->emptyClientType->getNamespace(), $licenseYear);
                $file = EmptyClientGenerator::generate($ctx, $serviceDetails);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield ["src/{$version}{$serviceDetails->emptyClientType->name}.php", $code];
                // Unit tests.
                $ctx = new SourceFileContext($serviceDetails->unitTestsType->getNamespace(), $licenseYear);
                $file = UnitTestsGenerator::generate($ctx, $serviceDetails, $gapicYamlConfig);
                $code = $file->toCode();
                $code = Formatter::format($code);
                // TODO(vNext): Remove these non-standard 'use' ordering.
                $code = Formatter::moveUseTo($code, $serviceDetails->emptyClientType->getFullname(true), 0);
                $code = Formatter::moveUseTo($code, 'stdClass', -1);
                yield ["tests/Unit/{$version}{$serviceDetails->unitTestsType->name}.php", $code];
                // Resource: descriptor_config.php
                $code = ResourcesGenerator::generateDescriptorConfig($serviceDetails, $gapicYamlConfig);
                $code = Formatter::format($code);
                yield ["src/{$version}resources/{$serviceDetails->descriptorConfigFilename}", $code];
                // Resource: rest_client_config.php
                $code = ResourcesGenerator::generateRestConfig($serviceDetails, $serviceYamlConfig);
                $code = Formatter::format($code);
                yield ["src/{$version}resources/{$serviceDetails->restConfigFilename}", $code];
                // Resource: client_config.json
                $json = ResourcesGenerator::generateClientConfig($serviceDetails, $grpcServiceConfig, $serviceYamlConfig, $gapicYamlConfig);
                yield ["src/{$version}resources/{$serviceDetails->clientConfigFilename}", $json];
            }
            // TODO: Further files, as required.
        }
    }
}
