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
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;

class EmptyClientGenerator
{
    public static function generate(SourceFileContext $ctx, ServiceDetails $serviceDetails): PhpFile
    {
        return (new EmptyClientGenerator($ctx, $serviceDetails))->generateImpl();
    }

    private SourceFileContext $ctx;
    private ServiceDetails $serviceDetails;

    private function __construct(SourceFileContext $ctx, ServiceDetails $serviceDetails)
    {
        $this->ctx = $ctx;
        $this->serviceDetails = $serviceDetails;
    }

    private function generateImpl(): PhpFile
    {
        // Generate file content
        $file = AST::file($this->generateClass())
            ->withApacheLicense($this->ctx->licenseYear)
            ->withGeneratedFromProtoCodeWarning($this->serviceDetails->filePath);
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function generateClass(): PhpClass
    {
        return AST::class($this->serviceDetails->emptyClientType, $this->ctx->type($this->serviceDetails->gapicClientType))
            ->withPhpDoc(PhpDoc::block(PhpDoc::inherit()))
            ->withMember(AST::comment(PhpDoc::text(
                'This class is intentionally empty, and is intended to hold manual additions to the generated',
                $this->ctx->type($this->serviceDetails->gapicClientType), 'class.'
            )));
    }
}
