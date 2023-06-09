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

use Google\ApiCore\ApiException;
use Google\ApiCore\Call;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\OperationResponse;
use Google\ApiCore\PathTemplate;
use Google\ApiCore\RequestParamsHeaderDescriptor;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\GrpcTransport;
use Google\ApiCore\Transport\RestTransport;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\Access;
use Google\Generator\Ast\Expression;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpClassMember;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;
use Google\Generator\Ast\PhpParam;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\MigrationMode;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Transport;
use Google\Generator\Utils\Type;

class GapicClientGenerator
{
    public static function generate(SourceFileContext $ctx, ServiceDetails $serviceDetails): PhpFile
    {
        return (new GapicClientGenerator($ctx, $serviceDetails))->generateImpl();
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
        // TODO(vNext): Remove the forced addition of these `use` clauses.
        $this->ctx->type(Type::fromName(\Google\ApiCore\PathTemplate::class));
        $this->ctx->type(Type::fromName(RequestParamsHeaderDescriptor::class));
        $this->ctx->type(Type::fromName(RetrySettings::class));
        $this->ctx->type($this->serviceDetails->grpcClientType);
        if ($this->serviceDetails->hasLro) {
            $this->ctx->type(Type::fromName(\Google\LongRunning\Operation::class));
            foreach ($this->serviceDetails->methods as $method) {
                if ($method->methodType === MethodDetails::LRO) {
                    $this->ctx->type($method->lroResponseType);
                    $this->ctx->type($method->lroMetadataType);
                }
            }
        }
        foreach ($this->serviceDetails->methods as $method) {
            $this->ctx->type($method->requestType);
            foreach ($method->allFields as $field) {
                if ($field->isRepeated && $field->typeSingular->isClass()) {
                    $this->ctx->type($field->typeSingular);
                }
            }
        }
        // Generate file content
        $file = AST::file($this->generateClass())
            ->withApacheLicense($this->ctx->licenseYear)
            // TODO(vNext): Consider if this header is sensible, as it ties this generator to Google cloud.
            ->withGeneratedFromProtoCodeWarning($this->serviceDetails->filePath, $this->serviceDetails->isGa());
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function examples(): GapicClientExamplesGenerator
    {
        return new GapicClientExamplesGenerator($this->serviceDetails);
    }

    private function generateClass(): PhpClass
    {
        return AST::class($this->serviceDetails->gapicClientType)
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::preFormattedText($this->serviceDetails->docLines->skip(1)
                    ->prepend('Service Description: ' . ($this->serviceDetails->docLines->firstOrNull() ?? ''))),
                PhpDoc::preFormattedText(Vector::new([
                    'This class provides the ability to make remote calls to the backing service through method',
                    'calls that map to API methods. Sample code to get started:'
                ])),
                count($this->serviceDetails->methods) === 0 ? null :
                    PhpDoc::example($this->examples()->rpcMethodExample($this->serviceDetails->methods[0])),
                count($this->serviceDetails->resourceParts) === 0 ? null :
                     PhpDoc::text(
                         'Many parameters require resource names to be formatted in a particular way. To assist ' .
                        'with these names, this class includes a format method for each type of name, and additionally ' .
                        'a parseName method to extract the individual identifiers contained within formatted names ' .
                        'that are returned by the API.'
                     ),
                // If this service contains both client surfaces, link to the new surface.
                in_array($this->serviceDetails->migrationMode, [MigrationMode::MIGRATING, MigrationMode::MIGRATION_MODE_UNSPECIFIED])
                    ? PhpDoc::text(
                        'This service has a new (beta) implementation. See {@see ' .
                        $this->serviceDetails->emptyClientV2Type->getFullname() . '} to use the new surface.'
                    ) : null,
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental(),
                !$this->serviceDetails->isDeprecated ? null : PhpDoc::deprecated(ServiceDetails::DEPRECATED_MSG)
            ))
            ->withTrait($this->ctx->type(Type::fromName(\Google\ApiCore\GapicClientTrait::class)))
            ->withMember($this->serviceName())
            ->withMember($this->serviceAddress())
            ->withMember($this->servicePort())
            ->withMember($this->codegenName())
            ->withMember($this->serviceScopes())
            ->withMembers($this->resourceProperties())
            ->withMember($this->operationsClient())
            ->withMember($this->getClientDefaults())
            ->withMember($this->defaultTransport())
            ->withMember($this->getSupportedTransports())
            ->withMembers($this->resourceMethods())
            ->withMembers($this->operationMethods())
            ->withMember($this->construct())
            ->withMembers($this->serviceDetails->methods->map(fn ($x) => $this->rpcMethod($x)));
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

    private function resourceProperties(): Vector
    {
        if (count($this->serviceDetails->resourceParts) > 0) {
            // Prevent duplicate properties. Vector's toMap currently does not support cloberring keys.
            // Sorts these properties alphabetically as a nice side effect.
            $templateMap = [];
            foreach ($this->serviceDetails->resourceParts as $r) {
                $templateMap[$r->nameCamelCase] =
                  $r->getTemplateProperty()->withAccess(Access::PRIVATE, Access::STATIC);
            }
            $templates = Vector::new(array_values($templateMap));
            $pathTemplateMap = AST::property('pathTemplateMap')
                ->withAccess(Access::PRIVATE, Access::STATIC);
            return Vector::new($templates->append($pathTemplateMap));
        } else {
            return Vector::new([]);
        }
    }

    private function resourceMethods(): Vector
    {
        $resourceParts = $this->serviceDetails->resourceParts;
        if (count($resourceParts) > 0) {
            $templateGetters = $resourceParts
                ->map(fn ($x) => $x->getTemplateGetterMethod()
                    ->withAccess(Access::PRIVATE, Access::STATIC)
                    ->withBody(AST::block(
                        AST::if(AST::binaryOp(AST::access(AST::SELF, $x->getTemplateProperty()), '==', AST::NULL))->then(
                            AST::assign(
                                AST::access(AST::SELF, $x->getTemplateProperty()),
                                AST::new($this->ctx->type(Type::fromName(PathTemplate::class)))($x->getPattern())
                            )
                        ),
                        AST::return(AST::access(AST::SELF, $x->getTemplateProperty()))
                    )));
            $pathTemplateMap = AST::property('pathTemplateMap');
            $getPathTemplateMap = AST::method('getPathTemplateMap')
                ->withAccess(Access::PRIVATE, Access::STATIC)
                ->withBody(AST::block(
                    AST::if(AST::binaryOp(AST::access(AST::SELF, $pathTemplateMap), '==', AST::NULL))->then(
                        AST::assign(
                            AST::access(AST::SELF, $pathTemplateMap),
                            AST::array($resourceParts
                            ->toArray(fn ($x) => $x->getNameCamelCase(), fn ($x) => AST::call(AST::SELF, $x->getTemplateGetterMethod())()))
                        )
                    ),
                    AST::return(AST::access(AST::SELF, $pathTemplateMap))
                ));
            $formatMethods = $resourceParts
                ->map(fn ($x) => $x->getFormatMethod()
                    ->withAccess(Access::PUBLIC, Access::STATIC)
                    ->withParams($x->getParams()->map(fn ($x) => $x[1]))
                    ->withBody(AST::block(
                        AST::return(AST::call(AST::SELF, $x->getTemplateGetterMethod())()->render(
                            AST::array($x->getParams()->toArray(fn ($x) => $x[0], fn ($x) => $x[1]))
                        ))
                    ))
                    ->withPhpDoc(PhpDoc::block(
                        PhpDoc::text(
                            'Formats a string containing the fully-qualified path to represent a',
                            $x->getNameSnakeCase(),
                            'resource.'
                        ),
                        $x->getParams()->map(fn ($x) => PhpDoc::param($x[1], PhpDoc::text(), $this->ctx->type(Type::string()))),
                        PhpDoc::return($this->ctx->type(Type::string()), PhpDoc::text('The formatted', $x->getNameSnakeCase(), 'resource.')),
                        $this->serviceDetails->isGa() ? null : PhpDoc::experimental()
                    )));
            $formattedName = AST::param(null, AST::var('formattedName'));
            $template = AST::param(null, AST::var('template'), AST::NULL);
            $templateMap = AST::var('templateMap');
            $templateName = AST::var('templateName');
            $pathTemplate = AST::var('pathTemplate');
            $ex = AST::var('ex');
            $parseMethod = AST::method('parseName')
                ->withAccess(Access::PUBLIC, Access::STATIC)
                ->withParams($formattedName, $template)
                ->withBody(AST::block(
                    AST::assign($templateMap, AST::call(AST::SELF, $getPathTemplateMap)()),
                    AST::if($template->var)->then(
                        AST::if(AST::not(AST::call(AST::ISSET)($templateMap[$template->var])))->then(
                            AST::throw(AST::new($this->ctx->type(Type::fromName(ValidationException::class)))(
                                AST::interpolatedString('Template name $template does not exist')
                            ))
                        ),
                        AST::return($templateMap[$template->var]->match($formattedName->var))
                    ),
                    AST::foreach($templateMap, $pathTemplate, $templateName)(
                        AST::try(
                            AST::return($pathTemplate->match($formattedName))
                        )->catch($this->ctx->type(Type::fromName(ValidationException::class)), $ex)(
                            '// Swallow the exception to continue trying other path templates'
                        )
                    ),
                    AST::throw(AST::new($this->ctx->type(Type::fromName(ValidationException::class)))(
                        AST::interpolatedString('Input did not match any known format. Input: $formattedName')
                    ))
                ))
                ->withPhpDoc(PhpDoc::block(
                    PhpDoc::preFormattedText(Vector::new([
                        'Parses a formatted name string and returns an associative array of the components in the name.',
                        'The following name formats are supported:',
                        'Template: Pattern',
                    ])->concat($resourceParts->map(fn ($x) => "- {$x->getNameCamelCase()}: {$x->getPattern()}"))),
                    PhpDoc::text(
                        'The optional $template argument can be supplied to specify a particular pattern, and must',
                        'match one of the templates listed above. If no $template argument is provided, or if the',
                        '$template argument does not match one of the templates listed, then parseName will check',
                        'each of the supported templates, and return the first match.'
                    ),
                    PhpDoc::param($formattedName, PhpDoc::text('The formatted name string'), $this->ctx->type(Type::string())),
                    PhpDoc::param($template, PhpDoc::text('Optional name of template to match'), $this->ctx->type(Type::string())),
                    PhpDoc::return($this->ctx->type(Type::array()), PhpDoc::text('An associative array from name component IDs to component values.')),
                    PhpDoc::throws($this->ctx->type(Type::fromName(ValidationException::class)), PhpDoc::text('If $formattedName could not be matched.')),
                    $this->serviceDetails->isGa() ? null : PhpDoc::experimental()
                ));
            return $templateGetters->append($getPathTemplateMap)->concat($formatMethods)->append($parseMethod);
        } else {
            return Vector::new([]);
        }
    }

    private function operationsClient(): ?PhpClassMember
    {
        if ($this->serviceDetails->hasLro || $this->serviceDetails->hasCustomOp) {
            return AST::property('operationsClient')
                ->withAccess(Access::PRIVATE);
        } else {
            return null;
        }
    }

    // operationMethods handles both standard google.longrunning and custom operations.
    private function operationMethods(): Vector
    {
        if (!$this->serviceDetails->hasLro && !$this->serviceDetails->hasCustomOp) {
            return Vector::new([]);
        }

        $ctype = $this->serviceDetails->hasCustomOp ?
            $this->serviceDetails->customOperationServiceClientType :
            Type::fromName(OperationsClient::class);
        $methods = Vector::new([]);

        // getOperationsClient returns the operation client instance.
        $getOperationsClient = AST::method('getOperationsClient')
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::return(AST::access(AST::THIS, $this->operationsClient()))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text("Return an {$ctype->name} object with the same endpoint as \$this."),
                PhpDoc::return($this->ctx->type($ctype)),
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental(),
            ));
        $methods = $methods->append($getOperationsClient);

        $default = AST::array([]);
        if ($this->serviceDetails->hasCustomOp) {
            // getDefaultOperationDescriptor method for services with Custom Operations.
            $firstCustomOp = $this->serviceDetails->methods
                ->filter(fn ($m) => $m->methodType === MethodDetails::CUSTOM_OP)
                ->firstOrNull();
            $defaultOperationDescriptor = !is_null($firstCustomOp)
                ? ResourcesGenerator::customOperationDescriptor($this->serviceDetails, $firstCustomOp)
                : AST::array([]);
            $getDefaultOperationDescriptor = AST::method('getDefaultOperationDescriptor')
                ->withAccess(Access::PRIVATE)
                ->withBody(AST::block(
                    AST::return($defaultOperationDescriptor)
                ))
                ->withPhpDoc(PhpDoc::block(
                    PhpDoc::text("Return the default longrunning operation descriptor config.")
                ));
            $methods = $methods->append($getDefaultOperationDescriptor);
            $default = AST::access(AST::THIS, AST::call($getDefaultOperationDescriptor)());
        }

        // resumeOperation for resuming an operation by name and method.
        $operationName = AST::var('operationName');
        $methodName = AST::var('methodName');
        $options = AST::var('options');
        $operation = AST::var('operation');
        $resumeOperation = AST::method('resumeOperation')
            ->withAccess(Access::PUBLIC)
            ->withParams(AST::param(null, $operationName), AST::param(null, $methodName, AST::NULL))
            ->withBody(AST::block(
                AST::assign($options, AST::ternary(
                    AST::call(AST::ISSET)(AST::access(AST::THIS, AST::property('descriptors'))[$methodName]['longRunning']),
                    AST::access(AST::THIS, AST::property('descriptors'))[$methodName]['longRunning'],
                    $default
                )),
                AST::assign($operation, AST::new($this->ctx->type(Type::fromName(OperationResponse::class)))(
                    $operationName,
                    AST::call(AST::THIS, $getOperationsClient)(),
                    $options
                )),
                $operation->instanceCall(AST::method('reload'))(),
                AST::return($operation)
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text(
                    'Resume an existing long running operation that was previously started',
                    'by a long running API method. If $methodName is not provided, or does',
                    'not match a long running API method, then the operation can still be',
                    'resumed, but the OperationResponse object will not deserialize the',
                    'final response.'
                ),
                PhpDoc::param(
                    AST::param($this->ctx->type(Type::string()), $operationName),
                    PhpDoc::text('The name of the long running operation')
                ),
                PhpDoc::param(
                    AST::param($this->ctx->type(Type::string()), $methodName),
                    PhpDoc::text('The name of the method used to start the operation')
                ),
                PhpDoc::return($this->ctx->type(Type::fromName(OperationResponse::class))),
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental(),
            ));
        $methods = $methods->append($resumeOperation);

        return $methods;
    }

    private function getClientDefaults(): PhpClassMember
    {
        $clientConfigFilename = $this->serviceDetails->clientConfigFilename;
        $descriptorConfigFilename = $this->serviceDetails->descriptorConfigFilename;
        $clientDefaultValues = [
            'serviceName' => AST::access(AST::SELF, $this->serviceName()),
            'apiEndpoint' => AST::concat(
                AST::access(AST::SELF, $this->serviceAddress()),
                ':',
                AST::access(AST::SELF, $this->servicePort())
            ),
            'clientConfig' => AST::concat(AST::__DIR__, "/../resources/$clientConfigFilename"),
            'descriptorsConfigPath' => AST::concat(AST::__DIR__, "/../resources/$descriptorConfigFilename"),
        ];

        // TODO: Consolidate setting all the known array values together.
        // We do this here to maintain the existing sensible ordering.
        if ($this->serviceDetails->transportType === Transport::GRPC_REST) {
            $clientDefaultValues['gcpApiConfigPath'] =
                AST::concat(AST::__DIR__, "/../resources/{$this->serviceDetails->grpcConfigFilename}");
        }

        $credentialsConfig = [
            'defaultScopes' => AST::access(AST::SELF, $this->serviceScopes()),
        ];
        // Set "useJwtAccessWithScope" for DIREGAPIC APIs
        if ($this->serviceDetails->transportType === Transport::REST) {
            $credentialsConfig['useJwtAccessWithScope'] = false;
        }
        $clientDefaultValues['credentialsConfig'] = AST::array($credentialsConfig);
        $clientDefaultValues['transportConfig'] = AST::array([
            'rest' => AST::array([
                'restClientConfigPath' => AST::concat(AST::__DIR__, "/../resources/{$this->serviceDetails->restConfigFilename}"),
            ])
        ]);
        if ($this->serviceDetails->hasCustomOp) {
            $clientDefaultValues['operationsClientClass'] = AST::access(
                $this->ctx->type($this->serviceDetails->customOperationServiceClientType),
                AST::CLS
            );
        }

        return AST::method('getClientDefaults')
            ->withAccess(Access::PRIVATE, Access::STATIC)
            ->withBody(AST::block(
                AST::return(AST::array($clientDefaultValues))
            ));
    }

    private function defaultTransport()
    {
        if ($this->serviceDetails->transportType !== Transport::REST) {
            return null;
        }
        return AST::method('defaultTransport')
            ->withPhpDocText('Implements GapicClientTrait::defaultTransport.')
            ->withAccess(Access::PRIVATE, Access::STATIC)
            ->withBody(AST::block(
                AST::return(AST::literal("'rest'"))
            ));
    }

    private function getSupportedTransports()
    {
        if ($this->serviceDetails->transportType !== Transport::REST) {
            return null;
        }
        return AST::method('getSupportedTransports')
            ->withPhpDocText('Implements GapicClientTrait::getSupportedTransports.')
            ->withAccess(Access::PRIVATE, Access::STATIC)
            ->withBody(AST::block(
                AST::return(AST::array(['rest']))
            ));
    }
    private function construct(): PhpClassMember
    {
        $ctx = $this->ctx;
        // TODO: Likely to move these two method definitions into a common location.
        $buildClientOptions = AST::method('buildClientOptions');
        $setClientOptions = AST::method('setClientOptions');
        $options = AST::var('options');
        $optionsParam = AST::param($this->ctx->type(Type::array()), $options, AST::array([]));
        $clientOptions = AST::var('clientOptions');

        // Assumes there are only two transport types.
        $isGrpcRest = $this->serviceDetails->transportType === Transport::GRPC_REST;

        $restTransportDocText = 'At the moment, supports only `rest`.';
        $grpcTransportDocText = 'May be either the string `rest` or `grpc`. Defaults to `grpc` if gRPC support is detected on the system.';
        $transportDocText =
            PhpDoc::text(
                'The transport used for executing network requests. ',
                $isGrpcRest ? $grpcTransportDocText : $restTransportDocText,
                '*Advanced usage*: Additionally, it is possible to pass in an already instantiated',
                // TODO(vNext): Don't use a fully-qualified type here.
                $ctx->type(Type::fromName(TransportInterface::class), true),
                'object. Note that when this object is provided, any settings in $transportConfig, and any $apiEndpoint',
                'setting, will be ignored.'
            );

        $transportConfigSampleValues = [];
        if ($isGrpcRest) {
            $transportConfigSampleValues['grpc'] = AST::arrayEllipsis();
        }
        // Set this value here, don't initialize it, so we can maintain alphabetical order
        // for the resulting printed doc.
        $transportConfigSampleValues['rest'] = AST::arrayEllipsis();
        $transportConfigDocText =
            PhpDoc::text(
                'Configuration options that will be used to construct the transport. Options for',
                'each supported transport type should be passed in a key for that transport. For example:',
                PhpDoc::example(AST::block(
                    AST::assign(AST::var('transportConfig'), AST::array($transportConfigSampleValues))
                ), null, true),
                'See the',
                AST::call(
                    $ctx->type(
                        Type::fromName($isGrpcRest ? GrpcTransport::class : RestTransport::class),
                        true
                    ),
                    AST::method('build')
                )(),
                $isGrpcRest ? 'and' : '',
                $isGrpcRest
                    ? AST::call($ctx->type(Type::fromName(RestTransport::class), true), AST::method('build'))()
                    : '',
                $isGrpcRest ? 'methods ' : 'method ',
                'for the supported options.'
            );

        return AST::method('__construct')
            ->withParams($optionsParam)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                Ast::assign($clientOptions, AST::call(AST::THIS, $buildClientOptions)($options)),
                Ast::call(AST::THIS, $setClientOptions)($clientOptions),
                $this->serviceDetails->hasLro || $this->serviceDetails->hasCustomOp
                    ? AST::assign(
                        AST::access(AST::THIS, $this->operationsClient()),
                        AST::call(AST::THIS, AST::method('createOperationsClient'))($clientOptions)
                    )
                    : null
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text('Constructor.'),
                PhpDoc::param($optionsParam, PhpDoc::block(
                    PhpDoc::text('Optional. Options for configuring the service API wrapper.'),
                    // TODO: Understand if this commented-out code is correct or not.
                    // PhpDoc::type(Vector::new([$ctx->type(Type::string())]), 'serviceAddress',
                    //     PhpDoc::text('**Deprecated**. This option will be removed in a future major release.',
                    //         'Please utilize the `$apiEndpoint` option instead.')),
                    // PhpDoc::type(Vector::new([$ctx->type(Type::string())]), 'apiEndpoint',
                    PhpDoc::type(
                        Vector::new([$ctx->type(Type::string())]),
                        'apiEndpoint',
                        PhpDoc::text(
                            'The address of the API remote host. May optionally include the port, formatted',
                            "as \"<uri>:<port>\". Default '{$this->serviceDetails->defaultHost}:{$this->serviceDetails->defaultPort}'."
                        )
                    ),
                    PhpDoc::type(
                        Vector::new([
                        $ctx->type(Type::string()),
                        $ctx->type(Type::array()),
                        $ctx->type(Type::fromName(FetchAuthTokenInterface::class)),
                        $ctx->type(Type::fromName(CredentialsWrapper::class))
                    ]),
                        'credentials',
                        PhpDoc::text(
                            'The credentials to be used by the client to authorize API calls. This option',
                            'accepts either a path to a credentials file, or a decoded credentials file as a PHP array.',
                            PhpDoc::newLine(),
                            '*Advanced usage*: In addition, this option can also accept a pre-constructed',
                            // TODO(vNext): Don't use a fully-qualified type here.
                            $ctx->type(Type::fromName(FetchAuthTokenInterface::class), true),
                            'object or',
                            // TODO(vNext): Don't use a fully-qualified type here.
                            $ctx->type(Type::fromName(CredentialsWrapper::class), true),
                            'object. Note that when one of these objects are provided, any settings in $credentialsConfig will be ignored.'
                        )
                    ),
                    PhpDoc::type(
                        Vector::new([$ctx->type(Type::array())]),
                        'credentialsConfig',
                        PhpDoc::text(
                            'Options used to configure credentials, including auth token caching, for the client.',
                            'For a full list of supporting configuration options, see',
                            // TODO(vNext): Don't use a fully-qualified type here.
                            AST::call($ctx->type(Type::fromName(CredentialsWrapper::class), true), AST::method('build'))(),
                            '.'
                        )
                    ),
                    PhpDoc::type(
                        Vector::new([$ctx->type(Type::bool())]),
                        'disableRetries',
                        PhpDoc::text(
                            'Determines whether or not retries defined by the client configuration should be',
                            'disabled. Defaults to `false`.'
                        )
                    ),
                    PhpDoc::type(
                        Vector::new([$ctx->type(Type::string()), $ctx->type(Type::array())]),
                        'clientConfig',
                        PhpDoc::text(
                            'Client method configuration, including retry settings. This option can be either a',
                            'path to a JSON file, or a PHP array containing the decoded JSON data.',
                            'By default this settings points to the default client config file, which is provided',
                            'in the resources folder.'
                        )
                    ),
                    PhpDoc::type(
                        Vector::new([
                            $ctx->type(Type::string()),
                            $ctx->type(Type::fromName(TransportInterface::class))
                        ]),
                        'transport',
                        $transportDocText,
                    ),
                    PhpDoc::type(
                        Vector::new([$ctx->type(Type::array())]),
                        'transportConfig',
                        $transportConfigDocText
                    ),
                    PhpDoc::type(
                        Vector::new([$ctx->type(Type::callable())]),
                        'clientCertSource',
                        PhpDoc::text(
                            'A callable which returns the client cert as a string. This can be used to provide',
                            'a certificate and private key to the transport layer for mTLS.'
                        )
                    )
                )),
                PhpDoc::throws($this->ctx->type(Type::fromName(ValidationException::class))),
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental()
            ));
    }

    private function rpcMethod(MethodDetails $method): PhpClassMember
    {
        $docType = function ($field): ResolvedType {
            if ($field->desc->desc->isRepeated()) {
                if ($field->isEnum) {
                    // TODO(vNext): Remove this unnecessary import.
                    $this->ctx->type($field->typeSingular);
                    return $this->ctx->type(Type::arrayOf(Type::int()), false, true);
                } elseif ($field->isMap) {
                    return $this->ctx->type(Type::array());
                } elseif ($field->isOneOf) {
                    // Also adds a corresponding 'use' import.
                    return $this->ctx->type($field->toOneofWrapperType($this->serviceDetails->namespace));
                } else {
                    return $this->ctx->type(Type::arrayOf(Type::fromField($this->serviceDetails->catalog, $field->desc->desc, false)), false, true);
                }
            } else {
                // Affects type hinting for required oneofs.
                // TODO(vNext) Handle optional oneofs here.
                if ($field->isOneOf && $field->isRequired) {
                    return $this->ctx->type($field->toOneofWrapperType($this->serviceDetails->namespace));
                } elseif ($field->isEnum) {
                    // TODO(vNext): Remove this unnecessary import.
                    $this->ctx->type($field->type);
                    return $this->ctx->type(Type::int());
                } else {
                    return $this->ctx->type($field->type);
                }
            }
        };
        $docExtra = function ($field): Vector {
            if ($field->isEnum) {
                // TODO(vNext): Don't use a fully-qualified name here; and import correctly.
                $enumType = $field->typeSingular->getFullname();
                return Vector::new([
                    "For allowed values, use constants defined on {@see {$enumType}}"
                ]);
            } else {
                return Vector::new([]);
            }
        };
        $request = AST::var('request');
        $requestParamHeaders = AST::var('requestParamHeaders');
        $required = $method->requiredFields
                           ->filter(fn ($f) => !$f->isOneOf || $f->isFirstFieldInOneof())
                           ->map(fn ($f) => $this->toParam($f));
        $optionalArgs = AST::param($this->ctx->type(Type::array()), AST::var('optionalArgs'), AST::array([]));
        $retrySettingsType = Type::fromName(RetrySettings::class);
        $requestParams = AST::var('requestParams');
        $isStreamedRequest =
            $method->methodType === MethodDetails::BIDI_STREAMING
            || $method->methodType === MethodDetails::CLIENT_STREAMING;
        // Request parameter handling.
        $restRoutingHeaders =
            is_null($method->restRoutingHeaders) || count($method->restRoutingHeaders) === 0
            ? Map::new([])
            : $method->restRoutingHeaders;
        // The presence of google.api.routing explicit headers overrides google.api.http-based implicit headers.
        $hasRoutingParams = !is_null($method->routingParameters);
        if ($hasRoutingParams) {
            $restRoutingHeaders = $method->routingParameters;
        }

        // An associative array containing 'required' and 'optional' keys for statements that inject required
        // and optional fields in request headers.
        $requestHeaderAssignments = $hasRoutingParams
            ? static::explicitRequestParams($method, $restRoutingHeaders, $requestParamHeaders)
            : static::implicitRequestParams($method, $restRoutingHeaders, $requestParamHeaders);

        $hasRequestParams = count($restRoutingHeaders) > 0;
        return AST::method($method->methodName)
            ->withAccess(Access::PUBLIC)
            ->withParams(
                $isStreamedRequest ? null : $required,
                $optionalArgs
            )
            ->withBody(AST::block(
                $isStreamedRequest ? null : Vector::new([
                    AST::assign($request, AST::new($this->ctx->type($method->requestType))()),
                    !$hasRequestParams ? null : AST::assign($requestParamHeaders, AST::array([])),
                    Vector::zip(
                        $method->requiredFields->filter(fn ($f) => !$f->isOneOf || $f->isFirstFieldInOneof()),
                        $required,
                        fn ($field, $param) => $this->toRequestFieldSetter($request, $field, $param)
                    ),
                    // Request header assignments for required fields.
                    $requestHeaderAssignments['required'],
                    $method->optionalFields->map(
                        fn ($x) =>
                        AST::if(AST::call(AST::ISSET)(AST::index($optionalArgs->var, $x->camelName)))
                            ->then(
                                AST::call($request, $x->setter)(AST::index($optionalArgs->var, $x->camelName)),
                                // Request header assignment/parsing for optional fields.
                                !is_null($requestHeaderAssignments['optional'])
                                    ? $requestHeaderAssignments['optional']->get($x->name, null)
                                    : null
                            ),
                    ),
                    !$hasRequestParams ? null : AST::assign(
                        $requestParams,
                        AST::new($this->ctx->type(
                            Type::fromName(RequestParamsHeaderDescriptor::class)
                        ))($requestParamHeaders)
                    ),
                    !$hasRequestParams ? null : AST::assign(
                        $optionalArgs->var['headers'],
                        AST::ternary(
                            AST::call(AST::ISSET)($optionalArgs->var['headers']),
                            AST::call(AST::ARRAY_MERGE)($requestParams->getHeader(), $optionalArgs->var['headers']),
                            $requestParams->getHeader()
                        )
                    )
                ]),
                AST::return($this->startCall($method, $optionalArgs, $request))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::preFormattedText($method->docLines),
                PhpDoc::example($this->examples()->rpcMethodExample($method), PhpDoc::text('Sample code:')),
                $isStreamedRequest
                    ? null
                    : Vector::zip(
                        $method->requiredFields->filter(fn ($f) => !$f->isOneOf || $f->isFirstFieldInOneof()),
                        $required,
                        fn ($field, $param) =>
                        PhpDoc::param(
                            $param,
                            PhpDoc::preFormattedText(
                                !$field->isOneOf
                                    ? $field->docLines->concat($docExtra($field))
                                    : Vector::new([
                                        'An instance of the wrapper class for the required proto oneof '
                                        . $field->getOneofDesc()->getName() . '.'
                                      ])->concat($docExtra($field))
                            ),
                            $docType($field)
                        )
                    ),
                $isStreamedRequest ?
                    PhpDoc::param($optionalArgs, PhpDoc::block(
                        PhpDoc::Text('Optional.'),
                        PhpDoc::type(
                            Vector::new([$this->ctx->type(Type::int())]),
                            'timeoutMillis',
                            PhpDoc::text('Timeout to use for this call.')
                        )
                    )) :
                    PhpDoc::param($optionalArgs, PhpDoc::block(
                        PhpDoc::Text('Optional.'),
                        $method->optionalFields->map(
                            fn ($field) =>
                            PhpDoc::type(
                                Vector::new([$docType($field)]),
                                $field->camelName,
                                PhpDoc::preFormattedText($field->docLines->concat($docExtra($field)))
                            )
                        ),
                        $method->methodType === MethodDetails::SERVER_STREAMING ?
                            PhpDoc::type(
                                Vector::new([$this->ctx->type(Type::int())]),
                                'timeoutMillis',
                                PhpDoc::text('Timeout to use for this call.')
                            ) :
                            PhpDoc::type(
                                Vector::new([$this->ctx->type($retrySettingsType), $this->ctx->type(Type::array())]),
                                'retrySettings',
                                PhpDoc::text(
                                    // TODO(vNext): Don't use a fully-qualified type here.
                                    'Retry settings to use for this call. Can be a ',
                                    $this->ctx->Type($retrySettingsType),
                                    ' object, or an associative array of retry settings parameters. See the documentation on ',
                                    // TODO(vNext): Don't use a fully-qualified type here.
                                    $this->ctx->Type($retrySettingsType),
                                    ' for example usage.'
                                )
                            )
                    )),
                // TODO(vNext): Don't use a fully-qualified type here.
                $method->hasEmptyResponse ? null : PhpDoc::return($this->ctx->type($method->methodReturnType, true)),
                PhpDoc::throws(
                    $this->ctx->type(Type::fromName(ApiException::class)),
                    PhpDoc::text('if the remote call fails')
                ),
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental(),
                !$method->isDeprecated ? null : PhpDoc::deprecated(MethodDetails::DEPRECATED_MSG)
            ));
    }

    private function startCall($method, $optionalArgs, $request): AST
    {
        $startCallArgs = [
        $method->name,
        AST::access($this->ctx->type($method->responseType), AST::CLS),
        $optionalArgs->var
      ];
        switch ($method->methodType) {
      case MethodDetails::CUSTOM_OP:
        $startCallArgs = [
            $method->name,
            $optionalArgs->var,
            $request,
            AST::call(AST::THIS, AST::method('getOperationsClient'))(),
            AST::NULL,
            AST::access($this->ctx->type($method->responseType), AST::CLS),
        ];
        return AST::call(AST::THIS, AST::method('startOperationsCall'))(...$startCallArgs)->wait();
      case MethodDetails::NORMAL:
        $startCallArgs[] = $request;
        if ($method->isMixin()) {
            $startCallArgs[] =
                AST::access($this->ctx->type(Type::fromName(Call::class)), AST::constant('UNARY_CALL'));
            $startCallArgs[] = $method->mixinServiceFullname;
        }
        return AST::call(AST::THIS, AST::method('startCall'))(...$startCallArgs)->wait();
      case MethodDetails::LRO:
        $startCallArgs = [
          $method->name,
          $optionalArgs->var,
          $request,
          AST::call(AST::THIS, AST::method('getOperationsClient'))()
        ];
        if ($method->isMixin()) {
            $startCallArgs[] = $method->mixinServiceFullname;
        }
        return AST::call(AST::THIS, AST::method('startOperationsCall'))(...$startCallArgs)->wait();
      case MethodDetails::PAGINATED:
        $startCallArgs = [
          $method->name,
          $optionalArgs->var,
          AST::access($this->ctx->type($method->responseType), AST::CLS),
          $request
        ];
        if ($method->isMixin()) {
            $startCallArgs[] = $method->mixinServiceFullname;
        }
        return AST::call(AST::THIS, AST::method('getPagedListResponse'))(...$startCallArgs);
      case MethodDetails::BIDI_STREAMING:
        $startCallArgs[] = AST::NULL;
        $startCallArgs[] =
          AST::access($this->ctx->type(Type::fromName(Call::class)), AST::constant('BIDI_STREAMING_CALL'));
        if ($method->isMixin()) {
            $startCallArgs[] = $method->mixinServiceFullname;
        }
        return AST::call(AST::THIS, AST::method('startCall'))(...$startCallArgs);
      case MethodDetails::SERVER_STREAMING:
        $startCallArgs[] = $request;
        $startCallArgs[] =
          AST::access($this->ctx->type(Type::fromName(Call::class)), AST::constant('SERVER_STREAMING_CALL'));
        if ($method->isMixin()) {
            $startCallArgs[] = $method->mixinServiceFullname;
        }
        return AST::call(AST::THIS, AST::method('startCall'))(...$startCallArgs);
      case MethodDetails::CLIENT_STREAMING:
        $startCallArgs[] = AST::NULL;
        $startCallArgs[] =
          AST::access($this->ctx->type(Type::fromName(Call::class)), AST::constant('CLIENT_STREAMING_CALL'));
        if ($method->isMixin()) {
            $startCallArgs[] = $method->mixinServiceFullname;
        }
        return AST::call(AST::THIS, AST::method('startCall'))(...$startCallArgs);
      default:
        throw new \Exception("Cannot handle method type: '{$method->methodType}'");
      }
    }

    /**
     * Turns a field into a parameter for RPC methods.
     *
     * The caller will be responsible for preventing duplicates by ensuring that this method
     * is called only on the first field in a oneof group.
     */
    private function toParam(FieldDetails $field): PhpParam
    {
        if (!$field->isOneOf) {
            return AST::param(null, AST::var($field->camelName));
        }

        return AST::param(null, AST::var(Helpers::toCamelCase($field->getOneofDesc()->getName())));
    }

    /**
     * Returns an expression that assigns a required field to a request.
     * If this field is not part of a oneof, returns a plain assignment expression.
     * If the field is a oneof, this method returns an if-else block that passes the chosen
     * oneof field to the corresponding setter in the oneof.
     *
     * The caller will be responsible for preventing duplicates by ensuring that this method
     * is called only on the first field in a oneof group.
     *
     *  @param $requestVarExpr The AST variable that represents the request.
     *  @param $field A required proto field.
     *  @param $param The input parameter into the RPC method that corresponnds to $field.
     *    If $field is part of a oneof, $param should be a wrapper class generated by
     *    OneofWrapperGenerator (and generated by $this->toParam()).
     *  @returns An assignment Expression or an if-else block of type AST.
     */
    private function toRequestFieldSetter(Expression $requestVarExpr, FieldDetails $field, PhpParam $param)
    {
        if (!$field->isOneOf) {
            return AST::call($requestVarExpr, $field->setter)($param);
        }

        if (!$field->isFirstFieldInOneof()) {
            return null;
        }

        $containingMessage = $field->containingMessage;
        $oneofFieldDescProtos = $containingMessage->getField();

        $toMethodNameFn = function ($prefix, $fieldDescProto) {
            return $prefix . Helpers::toUpperCamelCase($fieldDescProto->getName());
        };

        $ifBlock = null;
        foreach ($containingMessage->getField() as $currFieldDescProto) {
            if (!$currFieldDescProto->hasOneofIndex()
                || $currFieldDescProto->getOneofIndex() !== $field->oneOfIndex) {
                continue;
            }

            // Code: $fooOneof->isBar()
            $condition = AST::call($param, AST::method($toMethodNameFn("is", $currFieldDescProto)))();
            // Code: $request->setBar($fooOneof->getBar())
            $then = AST::call(
                $requestVarExpr,
                AST::method($toMethodNameFn("set", $currFieldDescProto))
            )(
                AST::call($param, AST::method($toMethodNameFn("get", $currFieldDescProto)))()
            );
            // First field.
            if ($ifBlock === null) {
                $ifBlock = AST::if($condition)->then($then);
            } else {
                $ifBlock = $ifBlock->elseif($condition, $then);
            }
        }

        // Add the throw-exception block, in case a oneof field is not set.
        if ($ifBlock !== null) {
            $ifBlock = $ifBlock->else(
                AST::throw(AST::new($this->ctx->type(Type::fromName(ValidationException::class)))(
                    AST::interpolatedString('A field for the oneof ' . $field->getOneofDesc()->getName()
                    . ' must be set in param ' . $param->toCode())
                ))
            );
        }

        return AST::block($ifBlock);
    }

    /**
     * Assembles the code for matching and injecting the explicitly configured request routing headers.
     * A Vector containing code for required fields is keyed to 'required'. A Map containing code
     * for each optional field is keyed to 'optional'. If there are no headers configured to be set, both
     * are set to null.
     *
     * @param MethodDetails $method The method with the RoutingRule.
     * @param Map $routingHeaders A mapping of RoutingParameter.field to all of the processed versions of it.
     * @param Expression $paramsVar The PHP variable used to collect the header key-value-pairs.
     *
     * @return array Associative array with two keys: 'required' (Vector value) and 'optional' (Map value).
     */
    private static function explicitRequestParams(MethodDetails $method, Map $routingHeaders, Expression $paramsVar)
    {
        // Has no request parameter headers.
        if (count($routingHeaders) === 0) {
            return ['required' => null, 'optional' => null];
        }

        // Map those root fields that are required by name to the routing header config.
        $requiredRoutingHeadersByRoot = $routingHeaders
            ->filter(fn ($k, $v) => $method->requiredFields->any(fn ($f) => $f->name === $v[0]['root']))
            ->values()
            ->toMap(fn ($v) => $v[0]['root']);
        // Map those required fields that appear as routing headers by name to their own FieldDetails.
        $requiredFieldsInHeaders = $method->requiredFields
            ->filter(fn ($f) => isset($requiredRoutingHeadersByRoot[$f->name]))
            ->toMap(fn ($f) => $f->name);
        $requiredAssignments = static::explicitRequestParamsForFields(
            $requiredRoutingHeadersByRoot,
            $requiredFieldsInHeaders,
            $paramsVar
        );

        // Map those root fields that are optional by name to the routing header config.
        $optionalRoutingHeadersByRoot = $routingHeaders
            ->filter(fn ($k, $v) => $method->optionalFields->any(fn ($f) => $f->name === $v[0]['root']))
            ->values()
            ->toMap(fn ($v) => $v[0]['root']);
        // Map those optional fields that appear as routing headers by name to their own FieldDetails.
        $optionalFieldsInHeaders = $method->optionalFields
            ->filter(fn ($f) => isset($optionalRoutingHeadersByRoot[$f->name]))
            ->toMap(fn ($f) => $f->name);
        $optionalAssignments = static::explicitRequestParamsForFields(
            $optionalRoutingHeadersByRoot,
            $optionalFieldsInHeaders,
            $paramsVar
        );

        return [
            'required' => $requiredAssignments->values(),
            'optional' => $optionalAssignments
        ];
    }

    /**
     * Given the header-to-field mappings, compiles the code for the value matching and/or header injection.
     *
     * @param Map $headersByRootField Mapping of routing parameter configs keyed by the root field name.
     * @param Map $fieldDetailsByRootField Mapping of FieldDetails keyed by the root field name.
     * @param Expression $paramsVar The PHP variable used to collect the header key-value-pairs.
     *
     * @return Map The mapping of root field to header parsing/injection code related to it.
     */
    private static function explicitRequestParamsForFields(Map $headersByRootField, Map $fieldDetailsByRootField, Expression $paramsVar)
    {
        // $assignments maps a Vector of AST statements to the root field name.
        $assignments = Map::new([]);
        foreach ($headersByRootField as [$root, $routingConfigs]) {
            // $keyToMatcher maps the field key strings to the matching chain that might set it.
            $keyToMatcher = Map::new([]);
            // Collect all of the statements for a $root field, including
            // any regex matcher conditionals.
            foreach ($routingConfigs as $routing) {
                $field = $fieldDetailsByRootField[$root];
                $param = $field->isRequired
                    ? AST::param(null, AST::var($field->camelName))
                    : AST::index(AST::var('optionalArgs'), $field->camelName);
                $assignValue = $param;

                // Construct the getter chain if the routing header uses a nested field.
                $chain = $routing['getter'];
                if (count($chain) > 1) {
                    $assignValue = $chain->skip(1)->reduce($param, fn ($acc, $g) => AST::call($acc, AST::method($g))());
                }
                // Basic case, no regex matcher, just assign the required param to the header key.
                if (is_null($routing['regex'])) {
                    $assignments = $assignments->set($root, $assignments->get($root, Vector::new([]))->append(AST::assign(
                        AST::index($paramsVar, $routing['key']),
                        $assignValue
                    )));
                    continue;
                }

                // Construct the preg_match expression using the routing header config's capture group regular expression.
                $key = $routing['key'];
                $matchesName = Helpers::toCamelCase($key) . "Matches";
                $matches = AST::var($matchesName);
                $matcher = null;

                // Extend the if-elseif chain.
                if (isset($keyToMatcher[$key])) {
                    $if = $keyToMatcher[$key];
                    $if = $if->elseif(
                        /* condition */
                        AST::call(AST::PREG_MATCH)($routing['regex'], $assignValue, $matches),
                        /* then */
                        AST::assign(
                            AST::index($paramsVar, $routing['key']),
                            AST::index($matches, $routing['key'])
                        )
                    );
                    $matcher = $if;
                } else {
                    // Create the conditional chain that sets the header key-value pair using the capture group if
                    // the preg_match finds a match.
                    $matcher = AST::if(AST::call(AST::PREG_MATCH)($routing['regex'], $assignValue, $matches))->then(
                        AST::assign(
                            AST::index($paramsVar, $routing['key']),
                            AST::index($matches, $routing['key'])
                        )
                    );
                }
                // Upsert the matcher chain for a header key.
                $keyToMatcher = $keyToMatcher->set($key, $matcher);
            }
            $assignments = $assignments
                ->set($root, $assignments
                    ->get($root, Vector::new([]))
                    ->concat($keyToMatcher
                        // Initialize the a matching results array for the (root) field named in the routing header config.
                        ->mapValues(
                            fn ($key, $matcher) =>
                            AST::block(
                                // $fooMatches = []
                                AST::assign(AST::var(Helpers::toCamelCase($key) . "Matches"), AST::array([])),
                                // if (preg_match(..., $fooMatches))
                                $matcher
                            )
                        )->values()));
        }

        return $assignments;
    }

    /**
     * Compiles the code for implicit request header injection based on the configuration from
     * google.api.http annotations for both required and optional fields. A Vector containing code
     * for required fields is keyed to 'required'. A Map containing code for each optional field is
     * keyed to 'optional' (does not support nested fields). If there are no headers configured to
     * be set, both are set to null.
     *
     * @param MethodDetails $method The method with the HttpRule.
     * @param Map $restRoutingHeaders Mapping of full header key name to getter/chain.
     * @param Expression $requestParamHeaders The PHP variable used to collect the header key-value-pairs.
     *
     * @return Map Associative array with two keys: 'required' (Vector value) and 'optional' (Map value).
     */
    private static function implicitRequestParams(MethodDetails $method, Map $restRoutingHeaders, Expression $requestParamHeaders)
    {
        // Needed because a required field name like "foo" may map to a nested header name like "foo.bar".
        $requiredFieldNames =
            $method->requiredFields->map(fn ($f) => $f instanceof FieldDetails ? $f->name : $f);
        // Contains full field names with parents, e.g. foo.bar.car.
        $requiredRestRoutingKeys =
            $restRoutingHeaders->keys()
                 ->filter(fn ($x) => !empty($x) && $requiredFieldNames->contains(explode('.', $x)[0]));
        $requiredFieldNamesInRoutingHeaders =
            $requiredFieldNames->filter(
                fn ($x) => !empty($x)
                    && in_array(
                        trim($x),
                        array_map(fn ($k) => explode('.', $k)[0], $requiredRestRoutingKeys->toArray())
                    )
            )
                ->toArray();
        // Maps field names to a set of the relevant field in the URL pattern.
        // e.g. $requiredFieldToHeaderName['foo'] = ['foo.bar', 'foo.car'].
        // This is needed for RPCs that may have multiple subfields under the same field in their
        // HTTP bindings.
        $requiredFieldToHeaderName = [];
        foreach ($requiredFieldNamesInRoutingHeaders as $header) {
            $requiredFieldToHeaderName[$header] =
                $requiredRestRoutingKeys->filter(
                    fn ($k) => strpos($k, '.') !== 0 ? $header === explode(".", $k)[0] : $header === $k
                );
        }

        // Has no request parameter headers.
        if (count($restRoutingHeaders) === 0) {
            return ['required' => null, 'optional' => null];
        }
        $requiredRequestHeaders = Vector::new([]);
        // TODO(v2): Handle request params for oneofs - this currently isn't used by anyone.
        foreach ($method->requiredFields as $field) {
            if (!isset($requiredFieldToHeaderName[$field->name])) {
                continue;
            }
            $requiredParam = AST::param(null, AST::var($field->camelName));
            foreach ($requiredFieldToHeaderName[$field->name] as $urlPatternHeaderName) {
                $assignValue = $requiredParam;
                if ($restRoutingHeaders->get($urlPatternHeaderName, Vector::new([]))->count() >= 2) {
                    $assignValue =
                        $restRoutingHeaders->get($urlPatternHeaderName, Vector::new([]))
                            ->skip(1)
                            // Chains getter methods together for nested names like foo.bar.car, which
                            // becomes $foo->getBar()->getCar().
                            ->reduce($requiredParam, fn ($acc, $g) => AST::call($acc, AST::method($g))());
                }
                $requiredRequestHeaders = $requiredRequestHeaders->append(
                    AST::assign(
                        AST::index($requestParamHeaders, $urlPatternHeaderName),
                        $assignValue
                    )
                );
            }
        }

        // TODO(noahdietz): Consider assigning nested fields on optional params,
        // at the risk of errors if they're not set on the message itself.
        $optionalAssignments = $method->optionalFields
        ->filter(fn ($f) => isset($restRoutingHeaders[$f->name]))
        ->toMap(
            fn ($f) => $f->name,
            fn ($f) => AST::assign(
                AST::index($requestParamHeaders, $f->name),
                AST::index(AST::var('optionalArgs'), $f->camelName)
            )
        );

        return ['required' => $requiredRequestHeaders, 'optional' => $optionalAssignments];
    }
}
