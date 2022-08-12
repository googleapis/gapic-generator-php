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
use Google\ApiCore\OperationResponse;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Ast\Variable;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\Type;
use Google\LongRunning\Operation;
use Google\Protobuf\GPBEmpty;

class SnippetGenerator
{
    /** @var int The license year. */
    private int $licenseYear;

    /** @var ServiceDetails The service details. */
    private ServiceDetails $serviceDetails;

    // TODO: existing idioms make constructor private, consider this
    public function __construct(int $licenseYear, ServiceDetails $serviceDetails)
    {
        $this->licenseYear = $licenseYear;
        $this->serviceDetails = $serviceDetails;
    }

    public static function generate(int $licenseYear, ServiceDetails $serviceDetails): Map
    {
        return (new SnippetGenerator($licenseYear, $serviceDetails))->generateImpl();
    }

    public function generateImpl(): Map
    {
        $files = Map::new();

        foreach ($this->serviceDetails->methods as $method) {
            $regionTag = $this->generateRegionTag($method->name);
            $snippetDetails = new SnippetDetails($method, $this->serviceDetails);

            // TODO: run prettier-php on the resulting files. it'll help clean up any outstanding formatting issues
            // the existing formatters aren't equipped to catch
            $files = $files->set(
                $method->name,
                AST::file(null)
                    ->withApacheLicense($this->licenseYear)
                    ->withGeneratedCodeWarning()
                    ->withBlock(
                        AST::block(
                            AST::literal("require_once __DIR__ . '/../../../vendor/autoload.php'"),
                            PHP_EOL,
                            "// [START $regionTag]",
                            $snippetDetails
                                ->useStatements
                                ->toVector()
                                ->map(fn ($use) => AST::literal("use {$use}")),
                            $this->rpcMethodExample($method, $snippetDetails),
                            "// [END $regionTag]"
                        )
                    )
                );
        }

        return $files;
    }

    private function rpcMethodExample(MethodDetails $method, SnippetDetails $snippetDetails): AST
    {
        // this approach is based heavily on the existing code in ExamplesGenerator
        // TODO: investigate replacing ExamplesGenerator with this codebase, and instead
        // of generating examples directly in client code, link out to these seperate snippets
        // using the @example phpdoc annotation
        switch ($method->methodType) {
            case MethodDetails::NORMAL:
                $code = $this->rpcMethodExampleNormal($method, $snippetDetails);
                break;
            case MethodDetails::CUSTOM_OP:
                // Fallthrough - rpcMethodExampleOperation handles custom operations as well.
            case MethodDetails::LRO:
                $code = $this->rpcMethodExampleOperation($method, $snippetDetails);
                break;
            case MethodDetails::PAGINATED:
                $code = $this->rpcMethodExamplePaginated($method, $snippetDetails);
                break;
            case MethodDetails::BIDI_STREAMING:
                $code = $this->rpcMethodExampleBidiStreaming($method, $snippetDetails);
                break;
            case MethodDetails::SERVER_STREAMING:
                $code = $this->rpcMethodExampleServerStreaming($method, $snippetDetails);
                break;
            case MethodDetails::CLIENT_STREAMING:
                $code = $this->rpcMethodExampleClientStreaming($method, $snippetDetails);
                break;
            default:
                throw new \Exception("Cannot handle method-type: '{$method->methodType}'");
        }
        $snippetDetails->context->finalize(null);
        return $code;
    }

    private function rpcMethodExampleNormal(MethodDetails $method, SnippetDetails $snippetDetails): AST
    {
        $responseVar = AST::var('response');
        $call = AST::call(
            $snippetDetails->serviceClientVar,
            AST::method($method->methodName)
        )($snippetDetails->rpcArguments);

        return $this->buildSnippetStructure(
            $method,
            $snippetDetails,
            $method->hasEmptyResponse
                ? [$call]
                : [
                    AST::literal("/** @var {$method->responseType->name} {$responseVar->toCode()} */"),
                    AST::assign(
                        $responseVar,
                        $call
                    ),
                    AST::call("\0printf")(
                        AST::literal(
                            "'Response data: %s' . PHP_EOL, {$responseVar->toCode()}->serializeToJsonString()"
                        )
                    )
                ]
        );
    }

    // rpcMethodExampleOperation handles both google.longrunning and custom operations.
    private function rpcMethodExampleOperation(MethodDetails $method, SnippetDetails $snippetDetails): AST
    {
        $responseVar = AST::var('response');
        $useResponseFn = function (Variable $var) use ($method, $responseVar) {
            $isCustomOp = $method->methodType === MethodDetails::CUSTOM_OP;
            $result = AST::var('result');
            $error = AST::var('error');
            $noResult = $isCustomOp
                ? '// if creating/modifying, retrieve the target resource'
                : '// operation succeeded and returns no value';

            // TODO: ensure responsetype/ status classes are ended to imports
            return AST::if($var->operationSucceeded())
                ->then(
                    // Custom operations and google.protobuf.Empty responses have no result.
                    $isCustomOp || $method->hasEmptyLroResponse
                        ? $noResult
                        : Vector::new([
                            AST::literal("/** @var {$method->lroResponseType->name} {$responseVar->toCode()} */"),
                            AST::assign($result, $var->getResult()),
                            AST::call("\0printf")(
                                AST::literal(
                                    "'Response data: %s' . PHP_EOL, {$result->toCode()}->serializeToJsonString()"
                                )
                            )
                        ])
                )->else(
                    AST::literal("/** @var Status {$error->toCode()} */"),
                    AST::assign($error, $var->getError()),
                    AST::call("\0printf")(
                        AST::literal(
                            "'Operation failed with data: %s' . PHP_EOL, {$error->toCode()}->serializeToJsonString()"
                        )
                    )
                );
        };
        
        return $this->buildSnippetStructure(
            $method,
            $snippetDetails,
            [
                AST::literal("/** @var OperationResponse {$responseVar->toCode()} */"),
                AST::assign(
                    $responseVar,
                    AST::call(
                        $snippetDetails->serviceClientVar,
                        AST::method($method->methodName)
                    )($snippetDetails->rpcArguments)
                ),
                $responseVar->pollUntilComplete(),
                PHP_EOL,
                $useResponseFn($responseVar)
            ]
        );
    }

    private function rpcMethodExamplePaginated(MethodDetails $method, SnippetDetails $snippetDetails): AST
    {
        $responseVar = AST::var('response');
        $page = AST::var('page');
        $isMap = $method->resourcesField->isMap;
        $element = AST::var('element');
        $indexVar = $isMap ? AST::var('key') : null;

        return $this->buildSnippetStructure(
            $method,
            $snippetDetails,
            [
                '// Iterate over pages of elements',
                AST::assign(
                    $responseVar,
                    AST::call(
                        $snippetDetails->serviceClientVar,
                        AST::method($method->methodName)
                    )($snippetDetails->rpcArguments)
                ),
                AST::foreach($responseVar->iteratePages(), $page)(
                    // TODO: figure out how to get the type of the element being iterated over
                    AST::literal("/** @var {$method->resourcesField->type->name} {$element->toCode()} */"),
                    AST::foreach($page, $element, $indexVar)(
                        AST::call("\0printf")(
                            AST::literal(
                                "'Element data: %s' . PHP_EOL, {$element->toCode()}->serializeToJsonString()"
                            )
                        )
                    )
                )
            ]
        );
    }

    private function rpcMethodExampleBidiStreaming(MethodDetails $method, SnippetDetails $snippetDetails): AST
    {
        $requestVars = $method->requiredFields->map(fn ($x) => AST::var($x->camelName));
        $request = AST::var('request');
        $requests = AST::var('requests');
        $stream = AST::var('stream');
        $element = AST::var('element');

        return $this->buildSnippetStructure(
            $method,
            $snippetDetails,
            [
                Vector::zip(
                    $snippetDetails->sampleAssignments,
                    $method->requiredFields,
                    fn ($var, $f) => AST::assign(
                        $var,
                        $f->exampleValue($snippetDetails->context)
                    )
                ),
                AST::assign(
                    $request,
                    AST::new(
                        $snippetDetails
                            ->context
                            ->type($method->requestType)
                    )()
                ),
                Vector::zip(
                    $method->requiredFields,
                    $requestVars,
                    fn ($field, $param) => AST::call(
                        $request,
                        $field->setter
                    )($param)
                ),
                '// Write all requests to the server, then read all responses until the',
                '// stream is complete',
                AST::assign($requests, AST::array([$request])),
                AST::assign(
                    $stream,
                    $snippetDetails
                        ->serviceClientVar
                        ->instanceCall(
                            AST::method($method->methodName)
                        )()
                ),
                $stream->writeAll($requests),
                AST::foreach($stream->closeWriteAndReadAll(), $element)(
                    AST::call("\0printf")(
                        AST::literal(
                            "'Element data: %s' . PHP_EOL, {$element->toCode()}->serializeToJsonString()"
                        )
                    )
                )
            ]
        );
    }

    private function rpcMethodExampleServerStreaming(MethodDetails $method, SnippetDetails $snippetDetails): AST
    {
        $stream = AST::var('stream');
        $element = AST::var('element');

        return $this->buildSnippetStructure(
            $method,
            $snippetDetails,
            [
                '// Read all responses until the stream is complete',
                AST::assign(
                    $stream,
                    AST::call(
                        $snippetDetails->serviceClientVar,
                        AST::method($method->methodName)
                    )($snippetDetails->rpcArguments)
                ),
                AST::foreach($stream->readAll(), $element)(
                    AST::call("\0printf")(
                        AST::literal(
                            "'Element data: %s' . PHP_EOL, {$element->toCode()}->serializeToJsonString()"
                        )
                    )
                )
            ]
        );
    }

    private function rpcMethodExampleClientStreaming(MethodDetails $method, SnippetDetails $snippetDetails): AST
    {
        $requestVars = $method->requiredFields->map(fn ($x) => AST::var($x->camelName));
        $request = AST::var('request');
        $requests = AST::var('requests');
        $stream = AST::var('stream');
        $result = AST::var('result');

        return $this->buildSnippetStructure(
            $method,
            $snippetDetails,
            [
                Vector::zip($requestVars, $method->requiredFields, fn ($var, $f) => AST::assign($var, $f->exampleValue($snippetDetails->context))),
                AST::assign($request, AST::new($snippetDetails->context->type($method->requestType))()),
                Vector::zip($method->requiredFields, $requestVars, fn ($field, $param) => AST::call($request, $field->setter)($param)),
                '// Write data to server and wait for a response',
                AST::assign($requests, AST::array([$request])),
                AST::assign($stream, $snippetDetails->serviceClientVar->instanceCall(AST::method($method->methodName))()),
                AST::assign($result, $stream->writeAllAndReadResponse($requests)),
                AST::call("\0printf")(AST::literal("'Response data: %s' . PHP_EOL, {$result->toCode()}->serializeToJsonString()"))
            ]
        );
    }

    private function buildTryCatchStatement(array $tryStatements, SnippetDetails $snippetDetails)
    {
        $exceptionVar = AST::var('ex');

        return AST::try(...$tryStatements)
            ->catch(
                $snippetDetails
                    ->context
                    ->type(Type::fromName(ApiException::class)),
                $exceptionVar
            )(
                AST::call("\0printf")(
                    AST::literal(
                        "'Call failed with message: %s' . PHP_EOL, {$exceptionVar->toCode()}->getMessage()"
                    )
                )
            );
    }

    private function buildSnippetStructure(MethodDetails $method, SnippetDetails $snippetDetails, array $tryStatements)
    {
        $sampleName = Helpers::toSnakeCase($method->methodName) . '_sample';
        $callSample = $this->getCallSampleFn($snippetDetails, $sampleName);

        return AST::block(
            AST::fn($sampleName)
                ->withPhpDoc(
                    PhpDoc::block(
                        PhpDoc::preFormattedText($method->docLines),
                        $snippetDetails->phpDocParams
                    )
                )
                ->withParams($snippetDetails->sampleParams)
                ->withBody(
                    AST::block(
                        AST::assign(
                            $snippetDetails->serviceClientVar,
                            AST::new(
                                $snippetDetails->context->type(
                                    $this->serviceDetails->emptyClientType
                                )
                            )()
                        ),
                        $snippetDetails->sampleAssignments,
                        PHP_EOL,
                        $this->buildTryCatchStatement($tryStatements, $snippetDetails)
                    )
                ),
            $callSample
        );
    }

    private function getCallSampleFn(SnippetDetails $snippetDetails, string $sampleName)
    {
        if (count($snippetDetails->sampleParams) === 0) {
            return null;
        }

        return AST::fn('callSample')
            ->withPhpDoc(
                PhpDoc::block(
                    PhpDoc::text('Helper to execute the sample.'),
                    PhpDoc::text('TODO(developer): Replace sample parameters before running the code.')
                )
            )
            ->withBody(
                AST::block(
                    $snippetDetails->callSampleAssignments,
                    PHP_EOL,
                    AST::call("\0$sampleName")($snippetDetails->sampleArguments)
                )
            );
    }

    private function generateRegionTag($methodName)
    {
        $version = strtolower(Helpers::nsVersionAndSuffixPath($this->serviceDetails->namespace)) ?: '_';
        if ($version !== '_') {
            $version = '_' . $version . '_';
        }
        $serviceParts = explode('.', $this->serviceDetails->serviceName);
        $hostNameParts = explode('.', $this->serviceDetails->defaultHost);
        $serviceName = end($serviceParts);
        $shortName = $hostNameParts[0];

        return $shortName . $version . 'generated_' . $serviceName . '_' . $methodName . '_sync';
    }
}
