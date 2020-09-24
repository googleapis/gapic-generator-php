<?php
declare(strict_types=1);

namespace Google\Generator;

use Google\Generator\Collections\Vector;
use Google\Generator\Generation\GapicClientGenerator;
use Google\Generator\Generation\ServiceDetails;
use Google\Generator\Generation\SourceFileContext;
use Google\Generator\Utils\Formatter;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\SourceCodeInfoHelper;
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
    public static function GenerateFromDescriptor(string $descBytes, string $package)
    {
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $fileDescs = Vector::new($descSet->getFile());
        $filesToGenerate = $fileDescs
            ->filter(fn($x) => $x->getPackage() === $package)
            ->map(fn($x) => $x->getName());
        yield from static::Generate($fileDescs, $filesToGenerate);
    }

    /**
     * Generate from a vector of proto file descriptors, only generating the files listed
     * in $filesToGenerate.
     * 
     * @param Vector $fileDescs A vector of FileDescriptorProto, containing all proto source files.
     * @param Vector $filesToGenerate A vector of string, containing full names of all files to generate.
     * 
     * @return string[]
     */
    public static function Generate(Vector $fileDescs, Vector $filesToGenerate)
    {
        $filesToGenerateSet = $filesToGenerate->toSet();
        // Create map of all files to generate, keyed by package name.
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
            yield from static::GeneratePackage($catalog, $namespaces[0], $singlePackageFileDescs);
        }
    }

    private static function GeneratePackage(ProtoCatalog $catalog, string $namespace, Vector $fileDescs)
    {
        // $fileDescs: Vector<FileDescriptorProto>
        foreach ($fileDescs as $fileDesc)
        {
            SourceCodeInfoHelper::Merge($fileDesc);
            foreach ($fileDesc->getService() as $index => $service)
            {
                $serviceDetails = new ServiceDetails($catalog, $namespace, $fileDesc->getPackage(), $service);
                $ctx = new SourceFileContext();
                $file = GapicClientGenerator::Generate($ctx, $serviceDetails);
                $code = $file->toCode();
                $code = Formatter::format($code);
                yield $code;
            }
            // TODO: Further files, as required.
        }
    }
}
