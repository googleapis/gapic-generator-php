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

use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;

class EnumConstantGenerator
{
    public static function generate(SourceFileContext $ctx, EnumDescriptorProto $enumDesc, string $namespace, FileDescriptorProto $file): PhpFile
    {
        return (new EnumConstantGenerator($ctx, $enumDesc, $namespace, $file))->generateImpl();
    }

    private SourceFileContext $ctx;
    private EnumDescriptorProto $enumDesc;
    private FileDescriptorProto $fileDesc;
    private string $namespace;
    private Type $enumType;

    private function __construct(SourceFileContext $ctx, EnumDescriptorProto $enumDesc, string $namespace, FileDescriptorProto $file)
    {
        $this->ctx = $ctx;
        $this->enumDesc = $enumDesc;
        $this->fileDesc = $file;
        $this->namespace = $namespace;
        $this->enumType = Type::fromName($this->namespace);
    }

    private function generateImpl(): PhpFile
    {
        // Generate file content
        $file = AST::file($this->generateClass())
            ->withApacheLicense($this->ctx->licenseYear)
            // TODO(vNext): Consider if this header is sensible, as it ties this generator to Google cloud.
            ->withGeneratedFromProtoCodeWarning($this->fileDesc->getName(), /* isGa */ true);
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function generateClass(): PhpClass
    {
        return AST::class($this->enumType)
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text(
                    $this->enumDesc->getName() . ' contains string constants that'.
                    ' represent the names of each value in the ' .
                    $this->enumDesc->desc->getFullName() . ' descriptor.'
                )
            ))
            ->withMembers($this->nameConstants());
    }

    private function nameConstants(): Vector
    {
        return Vector::new($this->enumDesc->getValue())
            ->map(fn ($e) =>
                AST::constant($e->getName())
                ->withValue($e->getName()));
    }
}
