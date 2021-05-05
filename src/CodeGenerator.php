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
use Google\Generator\Generation\GapicMetadataGenerator;
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
use Google\Generator\Utils\Transport;
use Google\Protobuf\Internal\FileDescriptorSet;

class CodeGenerator
{
    const MIXIN_SERVICES =  ["google.iam.v1.IAMPolicy", "google.longrunning.Operations", "google.cloud.location.Locations"];

    /**
     * Generate from a FileSet descriptor; used when evoked from the command-line.
     *
     * @param string $descBytes The raw bytes of the proto descriptor, as generated using `protoc -o ...`
     * @param string $package The package name to generate.
     * @param ?string $transport The type of transport to support, gRPC and REST by default (null).
     *     Valid options include "grpc+rest", "grpc", or "rest".
     * @param bool $generateGapicMetadata Whether to generate the gapic_metadata.json files.
     * @param bool $isPreview Whether this API is in preview.
     * @param ?string $grpcServiceConfigJson Optional grpc-serv-config json string.
     * @param ?string $gapicYaml Optional gapic configuration yaml string.
     * @param ?string $serviceYaml Optional service configuration yaml string.
     * @param int $licenseYear The year to use in license headers.
     *
     * @return string[]
     */
    public static function generateFromDescriptor(
        string $descBytes,
        string $package,
        ?string $transport,
        bool $generateGapicMetadata,
        bool $isPreview,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?string $serviceYaml,
        int $licenseYear = -1
    ) {
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $fileDescs = Vector::new($descSet->getFile());
        $filesToGenerate = $fileDescs
            ->filter(fn ($x) => substr($x->getPackage(), 0, strlen($package)) === $package)
            ->map(fn ($x) => $x->getName());
        yield from static::generate($fileDescs, $filesToGenerate, $transport, $generateGapicMetadata, $isPreview, $grpcServiceConfigJson, $gapicYaml, $serviceYaml, $licenseYear);
    }

    /**
     * Generate from a vector of proto file descriptors, only generating the files listed
     * in $filesToGenerate.
     *
     * @param Vector $fileDescs A vector of FileDescriptorProto, containing all proto source files.
     * @param Vector $filesToGenerate A vector of string, containing full names of all files to generate.
     * @param ?string $transport The type of transport to support, gRPC+REST by default (null).
     *     Valid options include "grpc+rest", "grpc", or "rest".
     * @param bool $generateGapicMetadata Whether to generate the gapic_metadata.json files.
     * @param bool $isPreview Whether this API is in preview.
     * @param ?string $grpcServiceConfigJson Optional grpc-serv-config json string.
     * @param ?string $gapicYaml Optional gapic configuration yaml string.
     * @param ?string $serviceYaml Optional service configuration yaml string.
     * @param int $licenseYear The year to use in license headers.
     *
     * @return array[] [0] (string) is relative path; [1] (string) is file content.
     */
    public static function generate(
        Vector $fileDescs,
        Vector $filesToGenerate,
        ?string $transport,
        bool $generateGapicMetadata,
        bool $isPreview,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?string $serviceYaml,
        int $licenseYear = -1
    ) {
        if ($licenseYear < 0) {
            $licenseYear = (int)date('Y');
        }
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
            ->filter(fn ($x) => $filesToGenerateSet[$x->getName()])
            ->groupBy(fn ($x) => $x->getPackage());
        if (count($byPackage) === 0) {
            throw new \Exception('No packages specified to build');
        }

        // Mix-in services.
        $servicesToGenerate = [];
        $definedRpcNames = [];
        $mixinServices = [];
        $mixinRpcNames = [];
        $serviceYamlConfig = new ServiceYamlConfig($serviceYaml);
        $transportType = Transport::parseTransport($transport);
        foreach ($byPackage as [$_, $singlePackageFileDescs]) {
            $namespaces = $singlePackageFileDescs
              ->map(fn ($x) => ProtoHelpers::getNamespace($x))
              ->distinct();
            if (count($namespaces) > 1) {
                throw new \Exception('All files in the same package must have the same PHP namespace');
            }

            // $fileDescs: Vector<FileDescriptorProto>
            foreach ($singlePackageFileDescs as $fileDesc) {
                foreach ($fileDesc->getService() as $index => $service) {
                    $serviceDetails = new ServiceDetails($catalog, $namespaces[0], $fileDesc->getPackage(), $service, $fileDesc, $transportType, $isPreview);
                    $serviceName = $serviceDetails->serviceName;
                    if (in_array($serviceName, self::MIXIN_SERVICES)) {
                        if ($serviceYamlConfig->apiNames->contains($serviceName)) {
                            $mixinServices[] = $serviceDetails;
                            array_merge($mixinRpcNames, $serviceDetails->methods->map(fn ($m) => $m->name)->toArray());
                        }
                    } else {
                        $servicesToGenerate[] = $serviceDetails;
                        array_merge($definedRpcNames, $serviceDetails->methods->map(fn ($m) => $m->name)->toArray());
                    }
                }
            }
        }

        if (empty($servicesToGenerate) && !empty($mixinServices)) {
            // TODO: Handle the case where a mixin-allowlisted API mixes in another one in that list.
            // For instance, IAM mixing-in Locations. We don't handle this because it currently does
            // not occur, so checking for non-empty is a sufficient proxy for identifying the case
            // where we generate a client for one of those services.
            $servicesToGenerate = $mixinServices;
        }

        $rpcNameBlocklist = array_diff($mixinRpcNames, $definedRpcNames);
        foreach ($servicesToGenerate as &$service) {
            foreach ($mixinServices as $mixinService) {
                $service->addMixins($mixinService, $rpcNameBlocklist);
            }
        }

        // Generate files for each package.
        $result = [];
        foreach (static::generateServices(
            $servicesToGenerate,
            $grpcServiceConfigJson,
            $gapicYaml,
            $serviceYamlConfig,
            $generateGapicMetadata,
            $isPreview,
            $licenseYear
        ) as $file) {
            $result[] = $file;
        }

        return $result;
    }

    private static function generateServices(
        array $servicesToGenerate,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?ServiceYamlConfig $serviceYamlConfig,
        bool $generateGapicMetadata,
        bool $isPreview,
        int $licenseYear
    ) {
        $versionToNamespace = [];
        foreach ($servicesToGenerate as $service) {
            // Look for a version string, "Vn..." as a part of the namespace.
            // If found, then the output directories for src and tests use it,
            // as can be seen in the 'yield ...' code below.
            $version = Helpers::nsVersionAndSuffixPath($service->namespace);
            if ($version !== '') {
                $version .= '/';
            }
            if (!array_key_exists($version, $versionToNamespace)) {
                $versionToNamespace[$version] = $service->namespace;
            }


            $serviceName = $service->serviceName;
            // Load various configs; if they're not provided then defaults will be used.
            $grpcServiceConfig = new GrpcServiceConfig($serviceName, $grpcServiceConfigJson);
            $gapicYamlConfig = new GapicYamlConfig($serviceName, $gapicYaml);

            // TODO: Refactor this code when it's clearer where the common elements are.
            // Service client.
            $ctx = new SourceFileContext($service->gapicClientType->getNamespace(), $licenseYear);
            $file = GapicClientGenerator::generate($ctx, $service);
            $code = $file->toCode();
            $code = Formatter::format($code);
            yield ["src/{$version}Gapic/{$service->gapicClientType->name}.php", $code];
            // Very thin service client wrapper, for manual code additions if required.
            $ctx = new SourceFileContext($service->emptyClientType->getNamespace(), $licenseYear);
            $file = EmptyClientGenerator::generate($ctx, $service);
            $code = $file->toCode();
            $code = Formatter::format($code);
            yield ["src/{$version}{$service->emptyClientType->name}.php", $code];
            // Unit tests.
            $ctx = new SourceFileContext($service->unitTestsType->getNamespace(), $licenseYear);
            $file = UnitTestsGenerator::generate($ctx, $service);
            $code = $file->toCode();
            $code = Formatter::format($code);
            // TODO(vNext): Remove these non-standard 'use' ordering.
            $code = Formatter::moveUseTo($code, $service->emptyClientType->getFullname(true), 0);
            $code = Formatter::moveUseTo($code, 'stdClass', -1);
            yield ["tests/Unit/{$version}{$service->unitTestsType->name}.php", $code];
            // Resource: descriptor_config.php
            $code = ResourcesGenerator::generateDescriptorConfig($service, $gapicYamlConfig);
            $code = Formatter::format($code);
            yield ["src/{$version}resources/{$service->descriptorConfigFilename}", $code];
            // Resource: rest_client_config.php
            $code = ResourcesGenerator::generateRestConfig($service, $serviceYamlConfig);
            $code = Formatter::format($code);
            yield ["src/{$version}resources/{$service->restConfigFilename}", $code];
            // Resource: client_config.json
            $json = ResourcesGenerator::generateClientConfig($service, $gapicYamlConfig, $grpcServiceConfig);
            yield ["src/{$version}resources/{$service->clientConfigFilename}", $json];
            // TODO: Further files, as required.
        }
        if ($generateGapicMetadata) {
            foreach ($versionToNamespace as $ver => $ns) {
                $gapicMetadataJson = GapicMetadataGenerator::generate($servicesToGenerate, $ns);
                yield ["src/{$ver}gapic_metadata.json", $gapicMetadataJson];
            }
        }
    }
}
