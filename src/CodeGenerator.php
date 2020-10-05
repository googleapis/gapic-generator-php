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
use Google\Generator\Generation\UnitTestsGenerator;
use Google\Generator\Generation\ServiceDetails;
use Google\Generator\Generation\SourceFileContext;
use Google\Generator\Utils\Formatter;
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
     *
     * @return string[]
     */
    public static function generateFromDescriptor(string $descBytes, string $package)
    {
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $fileDescs = Vector::new($descSet->getFile());
        $filesToGenerate = $fileDescs
            ->filter(fn($x) => $x->getPackage() === $package)
            ->map(fn($x) => $x->getName());
        yield from static::generate($fileDescs, $filesToGenerate);
    }

    /**
     * Generate from a vector of proto file descriptors, only generating the files listed
     * in $filesToGenerate.
     *
     * @param Vector $fileDescs A vector of FileDescriptorProto, containing all proto source files.
     * @param Vector $filesToGenerate A vector of string, containing full names of all files to generate.
     *
     * @return array[] [0] (string) is relative path; [1] (string) is file content.
     */
    public static function generate(Vector $fileDescs, Vector $filesToGenerate)
    {
        // Augment descriptors; e.g. proto comments; higher-level descriptors; ...
        ProtoAugmenter::augment($fileDescs);
        // Create map of all files to generate, keyed by package name.
        $filesToGenerateSet = $filesToGenerate->toSet();
        $byPackage = $fileDescs
            ->filter(fn($x) => $filesToGenerateSet[$x->getName()])
            ->groupBy(fn($x) => $x->getPackage());
        if (count($byPackage) === 0) {
            throw new \Exception('No packages specified to build');
        }
        $catalog = new ProtoCatalog($fileDescs);
        // Generate files for each package.
        foreach ($byPackage as [$_, $singlePackageFileDescs]) {
            $namespaces = $singlePackageFileDescs
                ->map(fn($x) => ProtoHelpers::GetNamespace($x))
                ->distinct();
            if (count($namespaces) > 1) {
                throw new \Exception('All files in the same package must have the same PHP namespace');
            }
            yield from static::generatePackage($catalog, $namespaces[0], $singlePackageFileDescs);
        }
    }

    private static function generatePackage(ProtoCatalog $catalog, string $namespace, Vector $fileDescs)
    {
        // $fileDescs: Vector<FileDescriptorProto>
        foreach ($fileDescs as $fileDesc)
        {
            foreach ($fileDesc->getService() as $index => $service)
            {
                $serviceDetails = new ServiceDetails($catalog, $namespace, $fileDesc->getPackage(), $service);
                // TODO: Refactor this code when it's clearer where the common elements are.
                // Service client.
                $ctx = new SourceFileContext($serviceDetails->gapicClientType->getNamespace());
                $file = GapicClientGenerator::generate($ctx, $serviceDetails);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield ["Gapic/{$serviceDetails->gapicClientType->name}.php", $code];
                // Very thin service client wrapper, for manual code additions if required.
                $ctx = new SourceFileContext($serviceDetails->emptyClientType->getNamespace());
                $file = EmptyClientGenerator::generate($ctx, $serviceDetails);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield ["{$serviceDetails->emptyClientType->name}.php", $code];
                // Unit tests.
                $ctx = new SourceFileContext($serviceDetails->unitTestsType->getNamespace());
                $file = UnitTestsGenerator::generate($ctx, $serviceDetails);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield ["Tests/{$serviceDetails->unitTestsType->name}.php", $code];
            }
            // TODO: Further files, as required.
        }
    }
}
