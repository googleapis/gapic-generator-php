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
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpFunction;
use Google\Generator\Ast\Variable;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\MigrationMode;
use Google\Generator\Utils\Transport;
use Google\Generator\Utils\Type;
use Google\Rpc\Status;

class SnippetGenerator
{
    /** @var int The license year. */
    private int $licenseYear;

    /** @var ServiceDetails The service details. */
    private ServiceDetails $serviceDetails;

    /**
     * @param int $licenseYear
     * @param ServiceDetails $serviceDetails
     */
    public function __construct(int $licenseYear, ServiceDetails $serviceDetails)
    {
        $this->licenseYear = $licenseYear;
        $this->serviceDetails = $serviceDetails;
    }

    /**
     * @param int $licenseYear
     * @param ServiceDetails $serviceDetails
     * @return array
     */
    public static function generate(int $licenseYear, ServiceDetails $serviceDetails): array
    {
        return (new SnippetGenerator($licenseYear, $serviceDetails))->generateImpl();
    }

    /**
     * @return array
     */
    private function generateImpl(): array
    {
        $files = [];

        foreach ($this->serviceDetails->methods as $method) {
            $regionTag = $this->generateRegionTag($method->name);
            $snippetDetails = $this->serviceDetails->migrationMode == MigrationMode::MIGRATION_MODE_UNSPECIFIED || $this->serviceDetails->migrationMode == MigrationMode::PRE_MIGRATION_SURFACE_ONLY ?
                new SnippetDetails($method, $this->serviceDetails) :
                new SnippetDetailsV2($method, $this->serviceDetails);
            $rpcMethodExample = $this->rpcMethodExample($snippetDetails);
            $files[Helpers::toSnakeCase($method->name)] = AST::file(null)
                ->withApacheLicense($this->licenseYear)
                ->withGeneratedCodeWarning()
                ->withBlock(
                    AST::block(
                        AST::literal("require_once __DIR__ . '/../../../vendor/autoload.php'"),
                        PHP_EOL,
                        "// [START $regionTag]",
                        $snippetDetails
                            ->context
                            ->usesByShortName
                            ->values()
                            ->map(fn ($use) => AST::literal("use {$use}")),
                        PHP_EOL,
                        $rpcMethodExample,
                        "// [END $regionTag]"
                    )
                );
        }

        return $files;
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @return AST
     * @throws \Exception
     */
    private function rpcMethodExample(SnippetDetails $snippetDetails): AST
    {
        switch ($snippetDetails->methodDetails->methodType) {
            case MethodDetails::NORMAL:
                $code = $this->rpcMethodExampleNormal($snippetDetails);
                break;
            case MethodDetails::CUSTOM_OP:
                // Fallthrough - rpcMethodExampleOperation handles custom operations as well.
            case MethodDetails::LRO:
                $code = $this->rpcMethodExampleOperation($snippetDetails);
                break;
            case MethodDetails::PAGINATED:
                $code = $this->rpcMethodExamplePaginated($snippetDetails);
                break;
            case MethodDetails::BIDI_STREAMING:
                $code = $this->rpcMethodExampleBidiStreaming($snippetDetails);
                break;
            case MethodDetails::SERVER_STREAMING:
                $code = $this->rpcMethodExampleServerStreaming($snippetDetails);
                break;
            case MethodDetails::CLIENT_STREAMING:
                $code = $this->rpcMethodExampleClientStreaming($snippetDetails);
                break;
            default:
                throw new \Exception("Cannot handle method-type: '{$snippetDetails->methodDetails->methodType}'");
        }
        $snippetDetails->context->finalize(null);
        return $code;
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @return AST
     */
    private function rpcMethodExampleNormal(SnippetDetails $snippetDetails): AST
    {
        $responseVar = AST::var('response');

        return $this->buildSnippetFunctions(
            $snippetDetails,
            [
                $this->buildClientMethodCall($snippetDetails, $responseVar),
                $snippetDetails->methodDetails->hasEmptyResponse
                    ? $this->buildPrintFCall('Call completed successfully.')
                    : $this->buildPrintFCall('Response data: %s', "{$responseVar->toCode()}->serializeToJsonString()")
            ]
        );
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @return AST
     */
    private function rpcMethodExampleOperation(SnippetDetails $snippetDetails): AST
    {
        $responseVar = AST::var('response');
        $resultVar = AST::var('result');
        $errorVar = AST::var('error');
        $isCustomOp = $snippetDetails->methodDetails->methodType === MethodDetails::CUSTOM_OP;
        $context = $snippetDetails->context;

        return $this->buildSnippetFunctions(
            $snippetDetails,
            [
                $this->buildClientMethodCall($snippetDetails, $responseVar),
                $responseVar->pollUntilComplete(),
                PHP_EOL,
                AST::if($responseVar->operationSucceeded(), false)
                    ->then(
                        // Custom operations and google.protobuf.Empty responses have no result.
                        $isCustomOp || $snippetDetails->methodDetails->hasEmptyLroResponse
                            ? $this->buildPrintFCall('Operation completed successfully.')
                            : Vector::new([
                                AST::inlineVarDoc(
                                    $context->type($snippetDetails->methodDetails->lroResponseType),
                                    $resultVar
                                ),
                                AST::assign($resultVar, $responseVar->getResult()),
                                $this->buildPrintFCall(
                                    'Operation successful with response data: %s',
                                    "{$resultVar->toCode()}->serializeToJsonString()"
                                )
                            ])
                    )->else(
                        AST::inlineVarDoc(
                            $context->type(Type::fromName(Status::class)),
                            $errorVar
                        ),
                        AST::assign($errorVar, $responseVar->getError()),
                        $this->buildPrintFCall(
                            'Operation failed with error data: %s',
                            "{$errorVar->toCode()}->serializeToJsonString()"
                        )
                    )
            ]
        );
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @return AST
     */
    private function rpcMethodExamplePaginated(SnippetDetails $snippetDetails): AST
    {
        $responseVar = AST::var('response');
        $elementVar = AST::var('element');
        $context = $snippetDetails->context;
        $resourceType = $snippetDetails->methodDetails->resourceType;

        return $this->buildSnippetFunctions(
            $snippetDetails,
            [
                $this->buildClientMethodCall($snippetDetails, $responseVar),
                PHP_EOL,
                // When transport is REST only, disabling this for now.
                // Need to further investigate an issue causing the resourceType
                // to render as ItemsEntry with a mapped entry, despite a
                // different value being outlined in the proto.
                $this->serviceDetails->transportType === Transport::REST
                    ? null
                    : AST::inlineVarDoc(
                        $context->type($resourceType),
                        $elementVar
                    ),
                AST::foreach($responseVar, $elementVar)(
                    $this->buildPrintFCall(
                        'Element data: %s',
                        $resourceType->isClass()
                            ? "{$elementVar->toCode()}->serializeToJsonString()"
                            : $elementVar->toCode()
                    )
                )
            ]
        );
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @return AST
     */
    private function rpcMethodExampleBidiStreaming(SnippetDetails $snippetDetails): AST
    {
        $streamVar = AST::var('stream');
        $elementVar = AST::var('element');
        $context = $snippetDetails->context;
        $responseType = $snippetDetails->methodDetails->responseType;

        return $this->buildSnippetFunctions(
            $snippetDetails,
            [
                $this->buildClientMethodCall($snippetDetails, $streamVar),
                $streamVar->writeAll($snippetDetails->rpcArguments),
                PHP_EOL,
                AST::inlineVarDoc(
                    $context->type($responseType),
                    $elementVar
                ),
                AST::foreach($streamVar->closeWriteAndReadAll(), $elementVar)(
                    $this->buildPrintFCall(
                        'Element data: %s',
                        $responseType->isClass()
                            ? "{$elementVar->toCode()}->serializeToJsonString()"
                            : $elementVar->toCode()
                    )
                )
            ]
        );
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @return AST
     */
    private function rpcMethodExampleServerStreaming(SnippetDetails $snippetDetails): AST
    {
        $streamVar = AST::var('stream');
        $elementVar = AST::var('element');
        $context = $snippetDetails->context;
        $responseType = $snippetDetails->methodDetails->responseType;

        return $this->buildSnippetFunctions(
            $snippetDetails,
            [
                $this->buildClientMethodCall($snippetDetails, $streamVar),
                PHP_EOL,
                AST::inlineVarDoc(
                    $context->type($responseType),
                    $elementVar
                ),
                AST::foreach($streamVar->readAll(), $elementVar)(
                    $this->buildPrintFCall(
                        'Element data: %s',
                        $responseType->isClass()
                            ? "{$elementVar->toCode()}->serializeToJsonString()"
                            : $elementVar->toCode()
                    )
                )
            ]
        );
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @return AST
     */
    private function rpcMethodExampleClientStreaming(SnippetDetails $snippetDetails): AST
    {
        $streamVar = AST::var('stream');
        $responseVar = AST::var('response');
        $context = $snippetDetails->context;
        $responseType = $snippetDetails->methodDetails->responseType;

        return $this->buildSnippetFunctions(
            $snippetDetails,
            [
                $this->buildClientMethodCall($snippetDetails, $streamVar),
                PHP_EOL,
                AST::inlineVarDoc(
                    $context->type($responseType),
                    $responseVar
                ),
                AST::assign(
                    $responseVar,
                    $streamVar->writeAllAndReadResponse($snippetDetails->rpcArguments)
                ),
                $this->buildPrintFCall(
                    'Response data: %s',
                    $responseType->isClass()
                        ? "{$responseVar->toCode()}->serializeToJsonString()"
                        : $responseVar->toCode()
                )
            ]
        );
    }

    /**
     * Defines the try/catch statement used to wrap every RPC.
     *
     * @param array $tryStatements
     * @param SnippetDetails $snippetDetails
     * @return AST
     */
    private function buildTryCatchStatement(array $tryStatements, SnippetDetails $snippetDetails): AST
    {
        $exceptionVar = AST::var('ex');

        return AST::try(...$tryStatements)
            ->catch(
                $snippetDetails
                    ->context
                    ->type(Type::fromName(ApiException::class)),
                $exceptionVar
            )(
                $this->buildPrintFCall(
                    'Call failed with message: %s',
                    "{$exceptionVar->toCode()}->getMessage()"
                )
            );
    }

    /**
     * Defines the basic outline of the main sample and the "callSample" function if one is needed.
     *
     * @param SnippetDetails $snippetDetails
     * @param array $tryStatements
     * @return AST
     */
    private function buildSnippetFunctions(SnippetDetails $snippetDetails, array $tryStatements): AST
    {
        $sampleName = Helpers::toSnakeCase($snippetDetails->methodDetails->methodName) . '_sample';
        $hasSampleParams = count($snippetDetails->sampleParams) > 0;
        $hasSampleAssignments = count($snippetDetails->sampleAssignments) > 0;
        $docLineCount = count($snippetDetails->methodDetails->docLines);
        $callSampleFn = $hasSampleParams
            ? $this->buildCallSampleFunction($snippetDetails, $sampleName)
            : null;
        $shouldGenerateDocBlock = $docLineCount > 0
            || count($snippetDetails->phpDocParams) > 0
            || !$hasSampleParams;
        $preMigrationSurface = in_array(
            $this->serviceDetails->migrationMode,
            [MigrationMode::MIGRATION_MODE_UNSPECIFIED, MigrationMode::PRE_MIGRATION_SURFACE_ONLY]
        );
        $clientType = $preMigrationSurface ?
            $this->serviceDetails->emptyClientType :
            $this->serviceDetails->gapicClientV2Type;

        $sampleFn = AST::fn($sampleName)
            ->withParams($snippetDetails->sampleParams)
            ->withReturnType($snippetDetails->context->type(Type::void()))
            ->withBody(
                AST::block(
                    '// Create a client.',
                    AST::assign(
                        $snippetDetails->serviceClientVar,
                        AST::new($snippetDetails->context->type($clientType))()
                    ),
                    $hasSampleAssignments ? PHP_EOL : null,
                    $hasSampleAssignments ? (
                        $preMigrationSurface
                            ? '// Prepare any non-scalar elements to be passed along with the request.'
                            : '// Prepare the request message.'
                    ) : null,
                    $snippetDetails->sampleAssignments,
                    PHP_EOL,
                    '// Call the API and handle any network failures.',
                    $this->buildTryCatchStatement($tryStatements, $snippetDetails)
                )
            );

        if ($shouldGenerateDocBlock) {
            $sampleFn = $sampleFn->withPhpDoc(
                PhpDoc::block(
                    $docLineCount > 0
                        ? PhpDoc::preFormattedText($snippetDetails->methodDetails->docLines)
                        : null,
                    !$hasSampleParams ? $this->buildGeneratedNotice() : null,
                    $snippetDetails->phpDocParams
                )
            );
        }

        if (!$hasSampleParams) {
            $sampleFn = $sampleFn->withoutNewlineAfterDeclaration();
        }

        return AST::block(
            $sampleFn,
            $callSampleFn
        );
    }

    /**
     * The "callSample" function acts as an entry point for the main sample. If required, it also prepares basic scalar
     * assignments.
     *
     * @param SnippetDetails $snippetDetails
     * @param string $sampleName
     * @return PhpFunction
     */
    private function buildCallSampleFunction(SnippetDetails $snippetDetails, string $sampleName): PhpFunction
    {
        return AST::fn('callSample')
            ->withoutNewlineAfterDeclaration()
            ->withPhpDoc(
                PhpDoc::block(
                    PhpDoc::text('Helper to execute the sample.'),
                    $this->buildGeneratedNotice()
                )
            )
            ->withReturnType($snippetDetails->context->type(Type::void()))
            ->withBody(
                AST::block(
                    $snippetDetails->callSampleAssignments,
                    PHP_EOL,
                    AST::call("\0$sampleName")($snippetDetails->sampleArguments)
                )
            );
    }

    /**
     * @param SnippetDetails $snippetDetails
     * @param Variable $var
     * @return Vector
     */
    private function buildClientMethodCall(SnippetDetails $snippetDetails, Variable $var): Vector
    {
        $vector = Vector::new();
        $methodDetails = $snippetDetails->methodDetails;
        $returnType = $methodDetails->methodReturnType;

        if (!$methodDetails->hasEmptyResponse) {
            $vector = $vector->append(
                AST::inlineVarDoc(
                    $snippetDetails
                        ->context
                        ->type($returnType),
                    $var
                )
            );
        }
        $call = AST::call(
            $snippetDetails->serviceClientVar,
            AST::method($methodDetails->methodName)
        );
        $call = $methodDetails->isClientStreaming() || $methodDetails->isBidiStreaming()
            ? $call()
            : $call($snippetDetails->rpcArguments);
        return $vector->append(
            $methodDetails->hasEmptyResponse
                ? $call
                : AST::assign($var, $call)
        );
    }

    /**
     * @param string $format The format string.
     * @param string $values
     * @return AST
     */
    private function buildPrintFCall(string $format, string ...$values): AST
    {
        $valueStr = array_reduce($values, function ($carry, $item) {
            return $carry .= "$item, ";
        });

        $strLiteral = "'$format' . PHP_EOL";
        if ($valueStr) {
            $strLiteral .= ', ' . substr($valueStr, 0, -2);
        }

        return AST::call(AST::PRINT_F)(
            AST::literal($strLiteral)
        );
    }

    /**
     * Generates a notice letting users know the sample may require modifications
     * to execute successfully.
     *
     * @return PhpDoc
     */
    private function buildGeneratedNotice()
    {
        return PhpDoc::text(
            'This sample has been automatically generated and should be regarded as a code template only.',
            'It will require modifications to work:',
            PhpDoc::preFormattedText(
                Vector::new([
                    ' - It may require correct/in-range values for request initialization.',
                    ' - It may require specifying regional endpoints when creating the service client,',
                    '   please see the apiEndpoint client configuration option for more details.'
                ])
            )
        );
    }

    /**
     * A region tag is used to identify a sample internally.
     *
     * @param string $methodName
     * @return string
     */
    private function generateRegionTag(string $methodName): string
    {
        $versionAndSuffix = strtolower(Helpers::nsVersionAndSuffixPath($this->serviceDetails->namespace)) ?: '_';
        $versionParts = explode('/', $versionAndSuffix);
        $version = $versionParts[0];
        if ($version !== '_') {
            $version = '_' . $version . '_';
        }
        $serviceParts = explode('.', $this->serviceDetails->serviceName);
        $hostNameParts = explode('.', $this->serviceDetails->defaultHost);
        $serviceName = end($serviceParts);
        $shortName = null;

        // See b/247776440 for more details.
        if ($hostNameParts[0] === 'iam-meta-api') {
            $shortName = 'iam';
        } else {
            // account for regional default endpoints e.g., "us-east1-pubsub.googleapis.com"
            $shortNameParts = explode('-', $hostNameParts[0]);
            $shortName = end($shortNameParts);
        }
        return $shortName . $version . 'generated_' . $serviceName . '_' . $methodName . '_sync';
    }
}
