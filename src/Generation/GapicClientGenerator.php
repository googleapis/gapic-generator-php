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
use Google\Generator\Ast\Access;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpClassMember;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Type;

class GapicClientGenerator
{
    public static function Generate(SourceFileContext $ctx, ServiceDetails $serviceDetails): PhpFile
    {
        return (new GapicClientGenerator($ctx, $serviceDetails))->GenerateImpl();
    }

    private SourceFileContext $ctx;
    private ServiceDetails $serviceDetails;

    private function __construct(SourceFileContext $ctx, ServiceDetails $serviceDetails)
    {
        $this->ctx = $ctx;
        $this->serviceDetails = $serviceDetails;
    }

    private function GenerateImpl(): PhpFile
    {
        // Generate file content
        $file = AST::file($this->GenerateClass());
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function GenerateClass(): PhpClass
    {
        return AST::class($this->serviceDetails->gapicClientType)
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::preFormattedText($this->serviceDetails->docLines->take(1)
                    ->map(fn($x) => "Service Description: {$x}")
                    ->concat($this->serviceDetails->docLines->skip(1))),
                PhpDoc::preFormattedText(Vector::new([
                    'This class provides the ability to make remote calls to the backing service through method',
                    'calls that map to API methods. Sample code to get started:'
                ])),
                // TODO: Include code example here.
                PhpDoc::experimental(),
            ))
            ->withTrait($this->ctx->type(Type::fromName(\Google\ApiCore\GapicClientTrait::class)))
            ->withMember($this->serviceName())
            ->withMember($this->serviceAddress())
            ->withMember($this->servicePort())
            ->withMember($this->codegenName())
            ->withMember($this->serviceScopes())
            ->withMember($this->getClientDefaults());
        // TODO: Generate further class content.
    }

    private function serviceName(): PhpClassMember
    {
        return AST::constant('SERVICE_NAME')
            ->withPhpDocText('The name of the service.')
            ->withValue($this->serviceDetails->serviceName);
    }

    private function serviceAddress(): PhpClassMember
    {
        return AST::constant('SERVICE_ADDRESS')
            ->withPhpDocText('The default address of the service.')
            ->withValue($this->serviceDetails->defaultHost);
    }

    private function servicePort(): PhpClassMember
    {
        return AST::constant('DEFAULT_SERVICE_PORT')
            ->withPhpDocText('The default port of the service.')
            ->withValue($this->serviceDetails->defaultPort);
    }

    private function codegenName(): PhpClassMember
    {
        return AST::constant('CODEGEN_NAME')
            ->withPhpDocText('The name of the code generator, to be included in the agent header.')
            ->withValue('gapic');
    }

    private function serviceScopes(): PhpClassMember
    {
        return AST::property('serviceScopes')
            ->withAccess(Access::PUBLIC, Access::STATIC)
            ->withPhpDocText('The default scopes required by the service.')
            ->withValue(AST::array($this->serviceDetails->defaultScopes->toArray()));
    }

    private function getClientDefaults(): PhpClassMember
    {
        return AST::method('getClientDefaults')
            ->withAccess(Access::PRIVATE, Access::STATIC)
            ->withBody(AST::block(
                AST::return(AST::array([
                    'serviceName' => AST::access(AST::SELF, $this->serviceName()),
                    'apiEndpoint' => AST::concat(AST::access(AST::SELF, $this->serviceAddress()), ':', AST::access(AST::SELF, $this->servicePort())),
                    'clientConfig' => AST::concat(AST::__DIR__, "/../resources/{$this->serviceDetails->clientConfigFilename}"),
                    'descriptorsConfigPath' => AST::concat(AST::__DIR__, "/../resources/{$this->serviceDetails->descriptorConfigFilename}"),
                    'gcpApiConfigPath' => AST::concat(AST::__DIR__, "/../resources/{$this->serviceDetails->grpcConfigFilename}"),
                    'credentialsConfig' => AST::array([
                        'scopes' => AST::access(AST::SELF, $this->serviceScopes()),
                    ]),
                    'transportConfig' => AST::array([
                        'rest' => AST::array([
                            'restClientConfigPath' => AST::concat(AST::__DIR__, "/../resources/{$this->serviceDetails->restConfigFilename}"),
                        ])
                    ])
                ]))
            ));
    }
}
