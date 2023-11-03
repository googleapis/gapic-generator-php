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

use Google\Generator\Collections\Map;
use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Generation\SnippetGenerator;
use Google\Generator\Generation\BuildMethodFragmentGenerator;
use Google\Generator\Generation\EmptyClientGenerator;
use Google\Generator\Generation\EnumConstantGenerator;
use Google\Generator\Generation\GapicMetadataGenerator;
use Google\Generator\Generation\GapicClientGenerator;
use Google\Generator\Generation\GapicClientV2Generator;
use Google\Generator\Generation\OneofWrapperGenerator;
use Google\Generator\Generation\ResourcesGenerator;
use Google\Generator\Generation\UnitTestsGenerator;
use Google\Generator\Generation\UnitTestsV2Generator;
use Google\Generator\Generation\ServiceDetails;
use Google\Generator\Generation\SourceFileContext;
use Google\Generator\Utils\Formatter;
use Google\Generator\Utils\GapicYamlConfig;
use Google\Generator\Utils\GrpcServiceConfig;
use Google\Generator\Utils\ServiceYamlConfig;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\MigrationMode;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Generator\Utils\Transport;
use Google\Generator\Utils\Type;
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
     * @param ?string $grpcServiceConfigJson Optional grpc-serv-config json string.
     * @param ?string $gapicYaml Optional gapic configuration yaml string.
     * @param ?string $serviceYaml Optional service configuration yaml string.
     * @param bool $numericEnums Whether to generate the numeric-enums JSON encoding system parameter.
     * @param int $licenseYear The year to use in license headers.
     * @param bool $generateSnippets Whether to generate snippets.
     * @param string $migrationMode MigrationMode to generate code with.
     *
     * @return string[]
     */
    public static function generateFromDescriptor(
        string $descBytes,
        string $package,
        ?string $transport,
        bool $generateGapicMetadata,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?string $serviceYaml,
        bool $numericEnums = false,
        int $licenseYear = -1,
        bool $generateSnippets = true,
        string $migrationMode = MigrationMode::PRE_MIGRATION_SURFACE_ONLY,
    ) {
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $fileDescs = Vector::new($descSet->getFile());
        $filesToGenerate = $fileDescs
            ->filter(fn ($x) => substr($x->getPackage(), 0, strlen($package)) === $package)
            ->map(fn ($x) => $x->getName());
        yield from static::generate(
            $fileDescs,
            $filesToGenerate,
            $transport,
            $generateGapicMetadata,
            $grpcServiceConfigJson,
            $gapicYaml,
            $serviceYaml,
            $numericEnums,
            $licenseYear,
            $generateSnippets,
            $migrationMode
        );
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
     * @param ?string $grpcServiceConfigJson Optional grpc-serv-config json string.
     * @param ?string $gapicYaml Optional gapic configuration yaml string.
     * @param ?string $serviceYaml Optional service configuration yaml string.
     * @param bool $numericEnums Optional whether to include in requests the system parameter enabling JSON-encoded
     *     responses to encode enum values as numbers.
     * @param int $licenseYear The year to use in license headers.
     * @param bool $generateSnippets Whether to generate snippets.
     *
     * @return array[] [0] (string) is relative path; [1] (string) is file content.
     */
    public static function generate(
        Vector $fileDescs,
        Vector $filesToGenerate,
        ?string $transport,
        bool $generateGapicMetadata,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?string $serviceYaml,
        bool $numericEnums = false,
        int $licenseYear = -1,
        bool $generateSnippets = true,
        string $migrationMode = MigrationMode::PRE_MIGRATION_SURFACE_ONLY
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

            // Full protobuf names, e.g. google.cloud.foo.Bar.
            // Assumes that the selectors in the service.yaml HTTP and documentation rules
            // will always use the fully-qualified protobuf name for RPCs.
            $serviceYamlMixinRpcNames = $serviceYamlConfig->httpRules->map(fn ($h) => $h->getSelector());
            $mixinRpcNamesToHttpRule = $serviceYamlConfig->httpRules->toMap(fn ($h) => $h->getSelector(), fn ($h) => $h);
            $mixinRpcNamesToDocs = $serviceYamlConfig->documentationRules->toMap(
                fn ($d) => $d->getSelector(),
                fn ($d) => Vector::new([$d->getDescription()])
            );

            // $fileDescs: Vector<FileDescriptorProto>
            foreach ($singlePackageFileDescs as $fileDesc) {
                foreach ($fileDesc->getService() as $index => $service) {
                    $serviceDetails =
                        new ServiceDetails($catalog, $namespaces[0], $fileDesc->getPackage(), $service, $fileDesc, $transportType, $migrationMode);
                    $serviceName = $serviceDetails->serviceName;
                    // Do not generate GAPICs for mixin services unless the mixin is the only service in the service.yaml
                    $generateNormalGapic = !in_array($serviceName, self::MIXIN_SERVICES)
                        || ($serviceYamlConfig->apiNames->contains($serviceName) && 1 === $serviceYamlConfig->apiNames->count());
                    if ($generateNormalGapic) {
                        $servicesToGenerate[] = $serviceDetails;
                        array_merge(
                            $definedRpcNames,
                            $serviceDetails->methods->map(fn ($m) => $m->name)->toArray()
                        );
                        continue;
                    }

                    if (!$serviceYamlConfig->apiNames->contains($serviceName)) {
                        continue;
                    }

                    // Filter based on the HTTP rule.
                    $mixinMethods = $serviceDetails->methods
                        ->filter(fn ($m) => $serviceYamlMixinRpcNames->contains($m->fullName));
                    // We use a for-loop since the notion of in-place mutation doesn't align with Vector.
                    $mixinMethodsTemp = $mixinMethods->toArray();
                    foreach ($mixinMethodsTemp as &$mixinMethod) {
                        // Docs and HTTP rules in service.yaml take precendence.
                        $mixinMethod->setDocLines(
                            $mixinRpcNamesToDocs->get($mixinMethod->fullName, $mixinMethod->docLines)
                        );
                        $mixinMethod->setHttpRule(
                            $mixinRpcNamesToHttpRule->get($mixinMethod->fullName, $mixinMethod->httpRule)
                        );
                    }
                    $mixinMethods = Vector::new($mixinMethodsTemp);

                    $serviceDetails->setMethods($mixinMethods);
                    $mixinServices[] = $serviceDetails;
                    array_merge($mixinRpcNames, $serviceDetails->methods->map(fn ($m) => $m->name)->toArray());
                }
            }
        }

        if (empty($servicesToGenerate) && !empty($mixinServices)) {
            // TODO: Handle the case where a mixin-allowlisted API mixes in another one in that list.
            // For instance, IAM mixing-in Locations. We don't handle this because it currently does
            // not occur, so checking for non-empty is a sufficient proxy for identifying the case
            // where we generate a client for one of those services.
            $servicesToGenerate = $mixinServices;
            $mixinServices = [];
            foreach ($servicesToGenerate as $serviceDetails) {
                array_merge($definedRpcNames, $serviceDetails->methods->map(fn ($m) => $m->name)->toArray());
            }
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
            $licenseYear,
            $numericEnums,
            $generateSnippets
        ) as $file) {
            $result[] = $file;
        }

        if ($transportType === Transport::REST) {
            foreach (static::generateEnumConstants(
                $byPackage,
                $catalog,
                $licenseYear
            ) as $file) {
                $result[] = $file;
            }
        }

        return $result;
    }

    private static function generateServices(
        array $servicesToGenerate,
        ?string $grpcServiceConfigJson,
        ?string $gapicYaml,
        ?ServiceYamlConfig $serviceYamlConfig,
        bool $generateGapicMetadata,
        int $licenseYear,
        bool $numericEnums = false,
        bool $generateSnippets = true
    ) {
        $versionToNamespace = [];
        $fragmentsGenerated = Set::new();
        foreach ($servicesToGenerate as $service) {
            $migrationMode = $service->migrationMode;

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

            // [Start V1 GAPIC surface generation]
            if ($migrationMode != MigrationMode::NEW_SURFACE_ONLY) {
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
                yield ["tests/Unit/{$version}{$service->unitTestsType->name}.php", $code];
            }
            // [End V1 GAPIC surface generation]

            // Snippet Generator.
            // Note: For some unknown reason this must appear before v2 code
            // generation. Need to investigate it as that is too brittle.
            if ($generateSnippets) {
                $snippetFiles = SnippetGenerator::generate($licenseYear, $service);

                $clientName = $migrationMode == MigrationMode::MIGRATION_MODE_UNSPECIFIED || $migrationMode == MigrationMode::PRE_MIGRATION_SURFACE_ONLY ?
                    $service->emptyClientType->name :
                    $service->gapicClientV2Type->name;

                foreach ($snippetFiles as $methodName => $snippetFile) {
                    $code = $snippetFile->toCode();
                    $code = Formatter::format($code, 100);
                    yield ["samples/{$version}{$clientName}/{$methodName}.php", $code];
                }
            }

            // [Start V2 GAPIC surface generation]
            if ($migrationMode != MigrationMode::PRE_MIGRATION_SURFACE_ONLY) {
                $ctx = new SourceFileContext($service->gapicClientType->getNamespace(), $licenseYear);
                $file = GapicClientV2Generator::generate($ctx, $service, $generateSnippets);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield ["src/{$version}Client/{$service->gapicClientV2Type->name}.php", $code];

                // Unit tests.
                $ctx = new SourceFileContext($service->unitTestsV2Type->getNamespace(), $licenseYear);
                $file = UnitTestsV2Generator::generate($ctx, $service);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield ["tests/Unit/{$version}Client/{$service->unitTestsV2Type->name}.php", $code];


                // Resource: build_method.txt
                $ctx = new SourceFileContext($service->gapicClientType->getNamespace(), $licenseYear);
                $buildMethodFragments = BuildMethodFragmentGenerator::generate($ctx, $service);
                foreach ($buildMethodFragments as [$fragmentName, $buildMethodFragment]) {
                    if ($fragmentsGenerated->offsetExists($fragmentName)) {
                        continue;
                    }
                    $buildMethodFragmentCode = BuildMethodFragmentGenerator::format(
                        $buildMethodFragment->reduce('', fn ($v, $i) => $v . $i->toCode())
                    );
                    $fragmentsGenerated = $fragmentsGenerated->add($fragmentName);
                    yield ["fragments/{$fragmentName}.build.txt", $buildMethodFragmentCode];
                }
            }
            // [End V2 GAPIC surface generation]

            // [Start surface version-agnostic code generation]

            // Oneof wrapper classes.
            if ($migrationMode != MigrationMode::NEW_SURFACE_ONLY) {
                $ctx = new SourceFileContext($service->gapicClientType->getNamespace(), $licenseYear);
                $oneofWrapperFiles = OneofWrapperGenerator::generate($ctx, $service);
                foreach ($oneofWrapperFiles as $oneofWrapperFile) {
                    $oneofClassNameComponents = explode('\\', $oneofWrapperFile->class->type->getFullname(/* omitLeadingBackslash = */ true));
                    $oneofContainingMessageName = $oneofClassNameComponents[sizeof($oneofClassNameComponents) - 2];
                    $oneofClassName = $oneofClassNameComponents[sizeof($oneofClassNameComponents) - 1];
                    $oneofCode = $oneofWrapperFile->toCode();
                    $oneofCode = Formatter::format($oneofCode);
                    yield ["src/{$version}$oneofContainingMessageName/$oneofClassName.php", $oneofCode];
                }
            }

            // Resource: descriptor_config.php
            $code = ResourcesGenerator::generateDescriptorConfig($service, $gapicYamlConfig);
            $code = Formatter::format($code);
            yield ["src/{$version}resources/{$service->descriptorConfigFilename}", $code];
            // Resource: rest_client_config.php
            $code = ResourcesGenerator::generateRestConfig($service, $serviceYamlConfig, $numericEnums);
            $code = Formatter::format($code);
            yield ["src/{$version}resources/{$service->restConfigFilename}", $code];
            // Resource: client_config.json
            $json = ResourcesGenerator::generateClientConfig($service, $gapicYamlConfig, $grpcServiceConfig);
            yield ["src/{$version}resources/{$service->clientConfigFilename}", $json];
        }
        if ($generateGapicMetadata) {
            foreach ($versionToNamespace as $ver => $ns) {
                $gapicMetadataJson = GapicMetadataGenerator::generate($servicesToGenerate, $ns);
                yield ["src/{$ver}gapic_metadata.json", $gapicMetadataJson];
            }
        }
        // [End surface version-agnostic code generation]
    }

    private static function generateEnumConstants(Map $inputFilesByPackage, ProtoCatalog $catalog, int $licenseYear)
    {
        $enumsToGenerate = $catalog->enumsToFile
            ->filter(fn ($e, $f) =>
                /* Ignore this specific annotation enum. */ !str_ends_with($e, '.OperationResponseMapping') &&
                /* Only include proto packages in input. */ !is_null($inputFilesByPackage->get($f->getPackage(), null)))
            ->keys()->map(fn ($e) => $catalog->enumsByFullname[$e]);

        foreach ($enumsToGenerate as $enum) {
            // Use the PHP namespace of the file that the enum belongs to and convert it
            // to the "in code" form using only single backslashes.
            $parent = $catalog->enumsToFile['.' . $enum->desc->getFullName()];
            $pkgNamespace = ProtoHelpers::getNamespace($parent);
            $pkgNamespace = str_replace('\\\\', '\\', $pkgNamespace);

            // Trim the package namespace from the enum's fullname to get the
            // relative path of the enum.
            $enumFullname = Type::fromEnum($enum->desc)->getFullname(/* omitLeadingBackslash */ true);
            $relativeNamespace = str_replace($pkgNamespace . "\\", '', $enumFullname);
            $filename = str_replace('\\', '/', $relativeNamespace);
            $namespace = $pkgNamespace . '\\Enums\\' . $relativeNamespace;

            // Extract the version, if present, from the enum namespace.
            $version = Helpers::nsVersionAndSuffixPath($pkgNamespace);
            if ($version !== '') {
                $version = explode('/', $version, /* limit */ 1)[0].'/';
            }
            $ctx = new SourceFileContext($namespace, $licenseYear);
            $file = EnumConstantGenerator::generate($ctx, $enum, $namespace, $parent);
            $code = $file->toCode();
            $code = Formatter::format($code);
            yield ["src/{$version}Enums/{$filename}.php", $code];
        }
    }
}
