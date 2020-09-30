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

use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\ApiCore\Transport\TransportInterface;
use Google\Generator\Ast\Access;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpClassMember;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Utils\Type;

class UnitTestsGenerator
{
    public static function generate(SourceFileContext $ctx, ServiceDetails $serviceDetails): PhpFile
    {
        return (new UnitTestsGenerator($ctx, $serviceDetails))->generateImpl();
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
        $file = AST::file($this->generateClass());
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function generateClass(): PhpClass
    {
        return AST::class($this->serviceDetails->unitTestsType, $this->ctx->type(Type::fromName(GeneratedTest::class)))
            ->withMember($this->createTransport())
            ->withMember($this->createCredentials())
            ->withMember($this->createClient());
    }

    private function createTransport(): PhpClassMember
    {
        $deserialize = AST::param(null, AST::var('deserialize'), AST::NULL);
        return AST::method('createTransport')
            ->withAccess(Access::PRIVATE)
            ->withParams($deserialize)
            ->withBody(AST::block(
                AST::return(AST::new($this->ctx->type(Type::fromName(MockTransport::class)))($deserialize->var))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::return($this->ctx->type(Type::fromName(TransportInterface::class)))
            ));
    }

    private function createCredentials(): PhpClassMember
    {
        return AST::method('createCredentials')
            ->withAccess(Access::PRIVATE)
            ->withBody(AST::block(
                AST::return(
                    AST::call(
                        AST::call(
                            AST::call(AST::THIS, AST::method('getMockBuilder'))(
                                AST::access($this->ctx->type(Type::fromName(CredentialsWrapper::class)), AST::CLS)),
                            AST::method('disableOriginalConstructor'))(),
                        AST::method('getMock'))()
                )
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::return($this->ctx->type(Type::fromName(CredentialsWrapper::class)))
            ));
    }

    private function createClient(): PhpClassMember
    {
        $options = AST::param($this->ctx->type(Type::array()), AST::var('options'), AST::array([]));
        return AST::method('createClient')
            ->withAccess(Access::PRIVATE)
            ->withParams($options)
            ->withBody(AST::block(
                AST::binaryOp($options->var, '+=', AST::array([
                    'credentials' => AST::call(AST::THIS, $this->createCredentials())()
                ])),
                AST::return(AST::new($this->ctx->type($this->serviceDetails->emptyClientType))($options->var))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::return($this->ctx->type($this->serviceDetails->emptyClientType))
            ));
    }
}
