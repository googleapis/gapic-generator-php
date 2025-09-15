<?php
/*
 * Copyright 2022 Google LLC
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
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\LongRunning\OperationsClient as LegacyOperationsClient;
use Google\ApiCore\OperationResponse;
use Google\ApiCore\PagedListResponse;
use Google\ApiCore\RequestParamsHeaderDescriptor;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\GrpcTransport;
use Google\ApiCore\Transport\RestTransport;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\Access;
use Google\Generator\Ast\TypeDeclaration;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpClassMember;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFile;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\MigrationMode;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Transport;
use Google\Generator\Utils\Type;
use Google\LongRunning\Client\OperationsClient;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

class GapicClientV2Generator
{
    private const CALL_OPTIONS_VAR = 'callOptions';

    public static function generate(SourceFileContext $ctx, ServiceDetails $serviceDetails, bool $generateSnippets): PhpFile
    {
        return (new GapicClientV2Generator($ctx, $serviceDetails, $generateSnippets))->generateImpl();
    }

    private SourceFileContext $ctx;
    private ServiceDetails $serviceDetails;
    // TODO(v2): This can be cleaned up after the v2 migration is complete.
    private bool $generateSnippets;

    private function __construct(SourceFileContext $ctx, ServiceDetails $serviceDetails, bool $generateSnippets)
    {
        $this->ctx = $ctx;
        $this->serviceDetails = $serviceDetails;
        $this->generateSnippets = $generateSnippets;
    }

    private function generateImpl(): PhpFile
    {
        // TODO(vNext): Remove the forced addition of these `use` clauses.
        $this->ctx->type(Type::fromName(\Google\ApiCore\PathTemplate::class));
        $this->ctx->type(Type::fromName(\Google\ApiCore\Options\ClientOptions::class));
        $this->ctx->type(Type::fromName(RequestParamsHeaderDescriptor::class));
        $this->ctx->type(Type::fromName(RetrySettings::class));
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
            ->withGeneratedFromProtoCodeWarning(
                $this->serviceDetails->filePath,
                $this->serviceDetails->isGa()
            );
        // Finalize as required by the source-context; e.g. add top-level 'use' statements.
        return $this->ctx->finalize($file);
    }

    private function generateClass(): PhpClass
    {
        return AST::class(
                $this->serviceDetails->gapicClientV2Type,
                final: true)
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::preFormattedText(
                    $this->serviceDetails->docLines->skip(1)
                        ->prepend(
                            'Service Description: ' . ($this->serviceDetails->docLines->firstOrNull() ?? '')
                        )
                ),
                PhpDoc::preFormattedText(
                    Vector::new([
                        'This class provides the ability to make remote calls to the backing service through method',
                        'calls that map to API methods.'
                    ])
                ),
                !$this->serviceDetails->hasResources
                    ? null
                    : PhpDoc::text(
                        'Many parameters require resource names to be formatted in a particular way. To assist ' .
                        'with these names, this class includes a format method for each type of name, and additionally ' .
                        'a parseName method to extract the individual identifiers contained within formatted names ' .
                        'that are returned by the API.'
                    ),
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental(),
                !$this->serviceDetails->isDeprecated ? null : PhpDoc::deprecated(ServiceDetails::DEPRECATED_MSG),
                $this->serviceDetails->streamingOnly ? null : $this->magicAsyncDocs(),
            ))
            ->withTrait($this->ctx->type(Type::fromName(\Google\ApiCore\GapicClientTrait::class)))
            ->withTrait(
                $this->serviceDetails->hasResources ? $this->ctx->type(Type::fromName(\Google\ApiCore\ResourceHelperTrait::class)): null)
            ->withMember($this->serviceName())
            ->withMember($this->serviceAddress())
            ->withMember($this->hasServiceAddressTemplate() ? $this->serviceAddressTemplate() : null)
            ->withMember($this->servicePort())
            ->withMember($this->codegenName())
            ->withMember($this->apiVersion())
            ->withMember($this->serviceScopes())
            ->withMember($this->operationsClient())
            ->withMember($this->getClientDefaults())
            ->withMember($this->defaultTransport())
            ->withMember($this->supportedTransports())
            ->withMembers($this->operationMethods())
            ->withMembers($this->resourceMethods())
            ->withMember($this->construct())
            ->withMember($this->magicMethod())
            ->withMembers($this->serviceDetails->methods->map(fn ($x) => $this->rpcMethod($x)))
            ->withMember(EmulatorSupportGenerator::generateEmulatorSupport($this->serviceDetails, $this->ctx));
    }

    private function serviceName(): PhpClassMember
    {
        return AST::constant('SERVICE_NAME')
            ->withPhpDocText('The name of the service.')
            ->withAccess(Access::PRIVATE)
            ->withValue($this->serviceDetails->serviceName);
    }

    private function apiVersion()
    {
        if (is_null($this->serviceDetails->apiVersion)) {
            return;
        }

        return AST::property('apiVersion')
            ->withPhpDocText('The api version of the service')
            ->withAccess(Access::PRIVATE)
            ->withType(ResolvedType::string())
            ->withValue(AST::literal("'" . $this->serviceDetails->apiVersion . "'"));
    }

    private function serviceAddress(): PhpClassMember
    {
        return AST::constant('SERVICE_ADDRESS')
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text("The default address of the service."),
                $this->hasServiceAddressTemplate()
                    ? PhpDoc::deprecated('SERVICE_ADDRESS_TEMPLATE should be used instead.')
                    : null
            ))
            ->withAccess(Access::PRIVATE)
            ->withValue($this->serviceDetails->defaultHost);
    }

    private function hasServiceAddressTemplate(): bool
    {
        return str_contains($this->serviceDetails->defaultHost, '.googleapis.com');
    }

    private function serviceAddressTemplate(): PhpClassMember
    {
        // Replace ".googleapis.com" with .UNIVERSE_DOMAIN to create a template
        // in the client libraries (e.x. "storage.googleapis.com" becomes "storage.UNIVERSE_DOMAIN")
        $template = str_replace('.googleapis.com', '.UNIVERSE_DOMAIN', $this->serviceDetails->defaultHost);
        return AST::constant('SERVICE_ADDRESS_TEMPLATE')
            ->withPhpDocText('The address template of the service.')
            ->withAccess(Access::PRIVATE)
            ->withValue($template);
    }

    private function servicePort(): PhpClassMember
    {
        return AST::constant('DEFAULT_SERVICE_PORT')
            ->withPhpDocText('The default port of the service.')
            ->withAccess(Access::PRIVATE)
            ->withValue($this->serviceDetails->defaultPort);
    }

    private function codegenName(): PhpClassMember
    {
        return AST::constant('CODEGEN_NAME')
            ->withPhpDocText('The name of the code generator, to be included in the agent header.')
            ->withAccess(Access::PRIVATE)
            ->withValue('gapic');
    }

    private function serviceScopes(): PhpClassMember
    {
        return AST::property('serviceScopes')
            ->withAccess(Access::PUBLIC, Access::STATIC)
            ->withPhpDocText('The default scopes required by the service.')
            ->withValue(AST::array($this->serviceDetails->defaultScopes->toArray()));
    }

    private function magicAsyncDocs(): PhpDoc
    {
        $methodDocs = $this->serviceDetails->methods
            ->filter(fn($m) => !$m->isStreaming())
            ->map(fn($m) => PhpDoc::method(
                $m->methodName . "Async",
                $this->asyncReturnType($m),
                $m->requestType->name, // the request type will already be imported for the sync variants
            ));
        return PhpDoc::block($methodDocs);
    }

    private function magicMethod(): ?PhpClassMember
    {
        // Only has streaming RPCs, so exclude __call from implementation, since
        // the magic method is only for async support at the moment.
        if ($this->serviceDetails->streamingOnly) {
            return null;
        }

        // params
        $methodVar = AST::var('method');
        $methodParam = AST::param(null, $methodVar);
        $argsVar = AST::var('args');
        $argsParam = AST::param(null, $argsVar);
        $triggerError = AST::call(AST::TRIGGER_ERROR)(AST::concat('Call to undefined method ', AST::__CLASS__, AST::interpolatedString('::$method()')), AST::E_USER_ERROR);

        return AST::method('__call')
            ->withAccess(Access::PUBLIC)
            ->withParams($methodParam, $argsParam)
            ->withBody(AST::block(
                AST::if(AST::binaryOp(AST::call(AST::SUBSTR)($methodVar, AST::literal('-5')), '!==', AST::literal("'Async'")))
                    ->then($triggerError),
                AST::call(AST::ARRAY_UNSHIFT)($argsVar, AST::call(AST::SUBSTR)($methodVar, AST::literal('0'), AST::literal('-5'))),
                AST::return(
                    AST::call(AST::CALL_USER_FUNC_ARRAY)(AST::array([AST::THIS, 'startAsyncCall'], true), $argsVar))))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text("Handles execution of the async variants for each documented method."),
            ));
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
        $ctype = $this->serviceDetails->hasCustomOp
            ? $this->serviceDetails->customOperationServiceClientType
            : Type::fromName($this->serviceDetails->migrationMode === MigrationMode::NEW_SURFACE_ONLY
                ? OperationsClient::class
                : LegacyOperationsClient::class
            );
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
                AST::assign($options, AST::nullCoalescing(
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
                    AST::param(ResolvedType::string(), $operationName),
                    PhpDoc::text('The name of the long running operation')
                ),
                PhpDoc::param(
                    AST::param(ResolvedType::string(), $methodName),
                    PhpDoc::text('The name of the method used to start the operation')
                ),
                PhpDoc::return($this->ctx->type(Type::fromName(OperationResponse::class))),
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental(),
            ));
        $methods = $methods->append($resumeOperation);

        if ($this->serviceDetails->migrationMode === MigrationMode::NEW_SURFACE_ONLY) {
            // write createOperationsClient method for new surface clients
            $operationsClientType = $this->serviceDetails->hasCustomOp
                ? $this->ctx->type($this->serviceDetails->customOperationServiceClientType)
                : $this->ctx->type(Type::fromName(OperationsClient::class));
            $createOperationsClient = AST::method('createOperationsClient')
                ->withAccess(Access::PRIVATE)
                ->withParams(AST::param(ResolvedType::array(), $options))
                ->withBody(AST::block(
                    '// Unset client-specific configuration options',
                    AST::call(AST::method('unset'))(
                        AST::index($options, 'serviceName'),
                        AST::index($options, 'clientConfig'),
                        AST::index($options, 'descriptorsConfigPath'),
                    ),
                    PHP_EOL,
                    AST::if(AST::call(AST::method('isset'))(AST::index($options, 'operationsClient'))
                        )->then(AST::return(AST::index($options, 'operationsClient'))),
                    PHP_EOL,
                    AST::return(AST::new($operationsClientType)($options))
                ))
                ->withPhpDoc(PhpDoc::block(
                    PhpDoc::text(
                        'Create the default operation client for the service.',
                    ),
                    PhpDoc::param(
                        AST::param(ResolvedType::array(), $options),
                        PhpDoc::text('ClientOptions for the client.')
                    ),
                    PhpDoc::return($operationsClientType)
                ));
            $methods = $methods->append($createOperationsClient);
        }

        return $methods;
    }

    private function resourceMethods(): Vector
    {
        if (!$this->serviceDetails->hasResources) {
            return Vector::new();
        }
        $formattedName = AST::param(ResolvedType::string(), AST::var('formattedName'));
        $template = AST::param(ResolvedType::string(true), AST::var('template'), AST::NULL);

        return $this->serviceDetails->resourceParts
            ->map(fn ($x) => $x->getFormatMethod()
                ->withAccess(Access::PUBLIC, Access::STATIC)
                // In order to avoid adding type hints to the v1 clients which has shared code for
                // Resource helper generation, we lazily add the param types here.
                ->withParams($x->getParams()->map(fn ($x) => AST::param(ResolvedType::string(), $x[1]->var)))
                ->withReturnType(ResolvedType::string())
                ->withBody(AST::block(
                    AST::return(AST::call(AST::SELF, AST::method('getPathTemplate'))($x->nameCamelCase)->render(
                        AST::array($x->getParams()->toArray(fn ($x) => $x[0], fn ($x) => $x[1]))
                    ))
                ))
                ->withPhpDoc(PhpDoc::block(
                    PhpDoc::text(
                        'Formats a string containing the fully-qualified path to represent a',
                        $x->getNameSnakeCase(),
                        'resource.'
                    ),
                    $x->getParams()->map(fn ($x) => PhpDoc::param($x[1], PhpDoc::text(), ResolvedType::string())),
                    PhpDoc::return(ResolvedType::string(), PhpDoc::text('The formatted', $x->getNameSnakeCase(), 'resource.')),
                    $this->serviceDetails->isGa() ? null : PhpDoc::experimental()
                )))
            ->append(AST::method('parseName')
                ->withAccess(Access::PUBLIC, Access::STATIC)
                ->withParams($formattedName, $template)
                ->withReturnType(ResolvedType::array())
                ->withBody(AST::block(
                    AST::return(AST::call(AST::SELF, AST::method('parseFormattedName'))($formattedName->var, $template->var))
                ))
                ->withPhpDoc(PhpDoc::block(
                    PhpDoc::preFormattedText(Vector::new([
                        'Parses a formatted name string and returns an associative array of the components in the name.',
                        'The following name formats are supported:',
                        'Template: Pattern',
                    ])->concat($this->serviceDetails->resourceParts->map(fn ($x) => "- {$x->getNameCamelCase()}: {$x->getPattern()}"))),
                    PhpDoc::text(
                        'The optional $template argument can be supplied to specify a particular pattern, and must',
                        'match one of the templates listed above. If no $template argument is provided, or if the',
                        '$template argument does not match one of the templates listed, then parseName will check',
                        'each of the supported templates, and return the first match.'
                    ),
                    PhpDoc::param($formattedName, PhpDoc::text('The formatted name string')),
                    PhpDoc::param($template, PhpDoc::text('Optional name of template to match')),
                    PhpDoc::return(ResolvedType::array(), PhpDoc::text('An associative array from name component IDs to component values.')),
                    PhpDoc::throws($this->ctx->type(Type::fromName(ValidationException::class)), PhpDoc::text('If $formattedName could not be matched.')),
                    $this->serviceDetails->isGa() ? null : PhpDoc::experimental()
                ))
            );
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
        if ($this->serviceDetails->transportType !== Transport::REST) {
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

        if ($this->serviceDetails->transportType !== Transport::GRPC) {
            $clientDefaultValues['transportConfig'] = AST::array([
                'rest' => AST::array([
                    'restClientConfigPath' => AST::concat(AST::__DIR__, "/../resources/{$this->serviceDetails->restConfigFilename}"),
                ])
            ]);
        }

        if ($this->serviceDetails->hasCustomOp && $this->serviceDetails->migrationMode !== MigrationMode::NEW_SURFACE_ONLY) {
            // This is only needed for legacy custom operations clients.
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

    private function supportedTransports()
    {
        if ($this->serviceDetails->transportType === Transport::REST) {
            return AST::method('supportedTransports')
                ->withPhpDocText('Implements ClientOptionsTrait::supportedTransports.')
                ->withAccess(Access::PRIVATE, Access::STATIC)
                ->withBody(AST::block(
                    AST::return(AST::array(['rest']))
                ));
        }

        if ($this->serviceDetails->transportType === Transport::GRPC) {
            return AST::method('supportedTransports')
                ->withPhpDocText('Implements ClientOptionsTrait::supportedTransports.')
                ->withAccess(Access::PRIVATE, Access::STATIC)
                ->withBody(AST::block(
                    AST::return(AST::array(['grpc', 'grpc-fallback']))
                ));
        }
    }

    private function construct(): PhpClassMember
    {
        $ctx = $this->ctx;
        // TODO: Likely to move these two method definitions into a common location.
        $buildClientOptions = AST::method('buildClientOptions');
        $setClientOptions = AST::method('setClientOptions');
        $options = AST::var('options');
        $optionsParam = AST::param(
            ResolvedType::union(
                Type::array(),
                Type::fromName(\Google\ApiCore\Options\ClientOptions::class)
            ),
            $options,
            AST::array([])
        );
        $clientOptions = AST::var('clientOptions');
        $transportType = $this->serviceDetails->transportType;

        $transportDocText =
            PhpDoc::text(
                'The transport used for executing network requests. ',
                $this->transportDocText($transportType),
                '*Advanced usage*: Additionally, it is possible to pass in an already instantiated',
                // TODO(vNext): Don't use a fully-qualified type here.
                $ctx->type(Type::fromName(TransportInterface::class), true),
                'object. Note that when this object is provided, any settings in $transportConfig, and any $apiEndpoint',
                'setting, will be ignored.'
            );

        $transportConfigSampleValues = [
            'grpc' => AST::arrayEllipsis(),
            'rest' => AST::arrayEllipsis()
        ];

        if (Transport::isGrpcOnly($transportType)) {
            unset($transportConfigSampleValues['rest']);
        } elseif (Transport::isRestOnly($transportType)) {
            unset($transportConfigSampleValues['grpc']);
        }

        $transportConfigDocText =
            PhpDoc::text(
                'Configuration options that will be used to construct the transport. Options for',
                'each supported transport type should be passed in a key for that transport. For example:',
                PhpDoc::example(
                    AST::block(
                        AST::assign(
                            AST::var('transportConfig'),
                            AST::array($transportConfigSampleValues)
                        )
                    ),
                    null,
                    true
                ),
                'See the',
                AST::call(
                    $ctx->type(
                        Type::fromName(
                            Transport::isRestOnly($transportType) ?
                                RestTransport::class :
                                GrpcTransport::class
                        ),
                        true
                    ),
                    AST::method('build')
                )(),
                Transport::isGrpcRest($transportType) ? 'and' : '',
                Transport::isGrpcRest($transportType) ?
                    AST::call(
                        $ctx->type(
                            Type::fromName(RestTransport::class),
                            true
                        ),
                        AST::method('build')
                    )()
                    : '',
                Transport::isGrpcRest($transportType) ? 'methods ' : 'method ',
                'for the supported options.'
            );
        return AST::method('__construct')
            ->withParams($optionsParam)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                EmulatorSupportGenerator::generateEmulatorOptions($this->serviceDetails, $options),
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
                EmulatorSupportGenerator::generateEmulatorPhpDoc($this->serviceDetails),
                PhpDoc::param($optionsParam, PhpDoc::block(
                    PhpDoc::text('Optional. Options for configuring the service API wrapper.'),
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
                            'object. Note that when one of these objects are provided, any settings in $credentialsConfig will be ignored.',
                            PhpDoc::newLine(),
                            '*Important*: If you accept a credential configuration (credential JSON/File/Stream)',
                            'from an external source for authentication to Google Cloud Platform, you must',
                            'validate it before providing it to any Google API or library. Providing an',
                            'unvalidated credential configuration to Google APIs can compromise the security of',
                            'your systems and data. For more information',
                            '{@see https://cloud.google.com/docs/authentication/external/externally-sourced-credentials}'
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
                    ),
                    PhpDoc::type(
                        Vector::new([
                            $ctx->type(Type::false()),
                            $ctx->type(Type::fromName(LoggerInterface::class))
                        ]),
                        'logger',
                        PhpDoc::text(
                            'A PSR-3 compliant logger. If set to false, logging is disabled,',
                            'ignoring the \'GOOGLE_SDK_PHP_LOGGING\' environment flag'
                        )
                    ),
                    PhpDoc::type(
                        Vector::new([
                            $ctx->type(Type::string()),
                        ]),
                        'universeDomain',
                        PhpDoc::text(
                            'The service domain for the client. Defaults to \'googleapis.com\'.'
                        )
                    )
                )),
                PhpDoc::throws($this->ctx->type(Type::fromName(ValidationException::class))),
                $this->serviceDetails->isGa() ? null : PhpDoc::experimental()
            ));
    }

    private function transportDocText(int $transportType): string
    {
        if (Transport::isRestOnly($transportType)) {
            return 'At the moment, supports only `rest`.';
        }

        if (Transport::isGrpcOnly($transportType)) {
            return 'At the moment, supports only `grpc`.';
        }

        return 'May be either the string `rest` or `grpc`. Defaults to `grpc` if gRPC support is detected on the system.';
    }

    private function rpcMethod(MethodDetails $method): PhpClassMember
    {
        $request = AST::var('request');
        $required = AST::param(
            $this->ctx->type($method->requestType),
            $request
        );
        $callOptions = AST::param(
            ResolvedType::array(),
            AST::var(self::CALL_OPTIONS_VAR),
            AST::array([])
        );
        $retrySettingsType = Type::fromName(RetrySettings::class);
        $usesRequest = !$method->isClientStreaming()
            && !$method->isBidiStreaming();
        $startCall = $this->startCall($method, $callOptions, $request);
        $phpDocReturnType = null;
        $returnType = $this->ctx->type(Type::void());
        if (!$method->hasEmptyResponse) {
            $returnType = $this->ctx->type($method->methodReturnType);
            $phpDocReturnType = PhpDoc::return($returnType);
            $startCall = AST::return($startCall);
            $genericType = match ($method->methodType) {
                MethodDetails::LRO => $method->hasEmptyLroResponse
                    ? Type::null()
                    : $method->lroResponseType,
                MethodDetails::SERVER_STREAMING => $method->responseType,
                default => null,
            };
            if ($genericType) {
                // ensure generic type is imported
                $this->ctx->type($genericType);
                $phpDocReturnType = PhpDoc::return(ResolvedType::generic(
                    $method->methodReturnType,
                    $genericType
                ));
            }
        }

        return AST::method($method->methodName)
            ->withAccess(Access::PUBLIC)
            ->withParams(
                $usesRequest ? $required : null,
                $callOptions
            )
            ->withBody(AST::block($startCall))
            ->withReturnType($returnType)
            ->withPhpDoc(
                PhpDoc::block(
                    count($method->docLines) > 0
                        ? PhpDoc::preFormattedText($method->docLines)
                        : null,
                    !$method->isStreaming()
                        ? PhpDoc::text(
                            'The async variant is',
                            AST::staticCall( // use staticCall for PHP Doc :: syntax
                                $this->ctx->type($this->serviceDetails->gapicClientV2Type),
                                AST::method($method->methodName . 'Async'))(),
                            '.')
                        : null,
                    $this->generateSnippets && in_array(
                        $this->serviceDetails->migrationMode,
                        [MigrationMode::MIGRATING, MigrationMode::NEW_SURFACE_ONLY]
                    )
                        ? PhpDoc::sample($this->snippetPathForMethod($method))
                        : null,
                    $usesRequest
                        ? PhpDoc::param($required, PhpDoc::text('A request to house fields associated with the call.'))
                        : null,
                    PhpDoc::param($callOptions, PhpDoc::block(
                        PhpDoc::Text('Optional.'),
                        $method->isStreaming()
                            ? PhpDoc::type(
                                Vector::new([$this->ctx->type(Type::int())]),
                                'timeoutMillis',
                                PhpDoc::text('Timeout to use for this call.')
                            )
                            : PhpDoc::type(
                                Vector::new([$this->ctx->type($retrySettingsType), ResolvedType::array()]),
                                'retrySettings',
                                PhpDoc::text(
                                    'Retry settings to use for this call. Can be a ',
                                    $this->ctx->type($retrySettingsType),
                                    ' object, or an associative array of retry settings parameters. See the documentation on ',
                                    $this->ctx->type($retrySettingsType),
                                    ' for example usage.'
                                )
                            )
                        )
                    ),
                    $phpDocReturnType,
                    PhpDoc::throws(
                        $this->ctx->type(Type::fromName(ApiException::class)),
                        PhpDoc::text('Thrown if the API call fails.')
                    ),
                    $this->serviceDetails->isGa()
                        ? null
                        : PhpDoc::experimental(),
                    $method->isDeprecated
                        ? PhpDoc::deprecated(MethodDetails::DEPRECATED_MSG)
                        : null
                )
            );
    }

    private function startCall($method, $callOptions, $request): AST
    {
        $startApiCallArgs = Map::new([
            'methodName' => $method->name,
            'request' => $request,
            self::CALL_OPTIONS_VAR => $callOptions->var
        ]);
        $wait = true;
        switch ($method->methodType) {
            case MethodDetails::BIDI_STREAMING:
                // Fall through to CLIENT_STREAMING.
            case MethodDetails::CLIENT_STREAMING:
                $startApiCallArgs = $startApiCallArgs->set('request', AST::NULL);
                // Fall through to SERVER_STREAMING.
            case MethodDetails::SERVER_STREAMING:
                // Fall through to PAGINATED.
            case MethodDetails::PAGINATED:
                $wait = false;
                break;
            default:
      }

      $call = AST::call(AST::THIS, AST::method('startApiCall'))(...$startApiCallArgs->values());
      if ($wait) {
        $call = $call->wait();
      }

      return $call;
    }

    private function snippetPathForMethod(MethodDetails $method): string
    {
        $methodName = Helpers::toSnakeCase($method->name);
        $version = Helpers::nsVersionAndSuffixPath($this->serviceDetails->namespace);
        if ($version !== '') {
            $version .= '/';
        }
        $emptyClientName = $this->serviceDetails->gapicClientV2Type->name;

        return "samples/{$version}{$emptyClientName}/{$methodName}.php";
    }

    private function asyncReturnType(MethodDetails $method): string
    {
        $returnType = $this->ctx->type(Type::fromName(PromiseInterface::class))->type->name;
        $nestedType = ($method->hasEmptyResponse)
            ? 'void'
            : $this->ctx->type($method->methodReturnType)->type->name;

        return sprintf('%s<%s>', $returnType, $nestedType);
    }
}
