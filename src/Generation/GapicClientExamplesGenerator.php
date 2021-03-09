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
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\GapicYamlConfig;
use Google\Generator\Utils\Helpers;

class GapicClientExamplesGenerator
{
    public function __construct(ServiceDetails $serviceDetails, GapicYamlConfig $gapicYamlConfig)
    {
        $this->serviceDetails = $serviceDetails;
        $this->gapicYamlConfig = $gapicYamlConfig;
        // Create a separate context, as this code isn't part of the generated client.
        $this->ctx = new SourceFileContext('');
        $this->prod = new TestNameValueProducer($serviceDetails->catalog, $this->ctx, $gapicYamlConfig);
    }

    private ServiceDetails $serviceDetails;
    private GapicYamlConfig $gapicYamlConfig;
    private SourceFileContext $ctx;
    private TestNameValueProducer $prod;

    public function rpcMethodExample(MethodDetails $method): AST
    {
        // Each GapicClientExamplesGenerator instance can only generate a single example.
        // TODO: Handle special arg types; e.g. resources.
        switch ($method->methodType) {
            case MethodDetails::NORMAL:
                $code = $this->rpcMethodExampleNormal($method);
                break;
            case MethodDetails::LRO:
                $code = $this->rpcMethodExampleLro($method);
                break;
            case MethodDetails::PAGINATED:
                $code = $this->rpcMethodExamplePaginated($method);
                break;
            case MethodDetails::BIDI_STREAMING:
                $code = $this->rpcMethodExampleBidiStreaming($method);
                break;
            case MethodDetails::SERVER_STREAMING:
                $code = $this->rpcMethodExampleServerStreaming($method);
                break;
            case MethodDetails::CLIENT_STREAMING:
                $code = $this->rpcMethodExampleClientStreaming($method);
                break;
            default:
                throw new \Exception("Cannot handle method-type: '{$method->methodType}'");
        }
        $this->ctx->finalize(null);
        return $code;
    }

    // TODO: Understand whether this is relevant for bidi and client streaming methods.
    // Return: [Vector of init code, Vector or args to pass to RPC call]
    private function initCallVars(MethodDetails $method): array
    {
        $fnGetValue = function (FieldDetails $f, string $value) {
            if ($f->isEnum) {
                return AST::access($this->ctx->type($f->typeSingular), AST::constant($value));
            } else {
                switch ($f->type->name) {
                    case 'string':
                        return $value;
                    default:
                        throw new \Exception("Cannot handle init-field of type: '{$f->type->name}'");
                }
            }
        };

        $config = $this->gapicYamlConfig->configsByMethodName->get($method->name, null);
        $inits = !is_null($config) && isset($config['sample_code_init_fields']) ?
            Map::fromPairs(array_map(fn ($x) => explode('=', $x, 2), $config['sample_code_init_fields'])) : Map::new();
        $result = $method->allFields
            ->map(function ($f) use ($inits, $fnGetValue, $method) {
                $value = $inits->get($f->name, null);
                $valueIndex = $inits->get($f->name . '[0]', null);
                if ($f->isRequired || !is_null($value) || !is_null($valueIndex)) {
                    $varName = $f->useResourceTestValue ? Helpers::toCamelCase("formatted_{$f->name}") : $f->camelName;
                    $var = AST::var($varName);
                    if (!is_null($value)) {
                        // Look for given init value for this field.
                        $initCode = AST::assign($var, $fnGetValue($f, $value));
                    } elseif (!is_null($valueIndex)) {
                        // Look for given indexed init value for this field.
                        if (!$f->isRepeated) {
                            throw new \Exception('Only a repeated field may use indexed init fields');
                        }
                        $initElements = Vector::range(0, 9)
                            ->map(fn ($i) => [AST::var("{$f->camelName}Element" . ($i === 0 ? '' : $i + 1)), $inits->get("{$f->name}[{$i}]", null)])
                            ->takeWhile(fn ($x) => !is_null($x[1]));
                        $initCode = $initElements
                            ->map(fn ($x) => AST::assign($x[0], $fnGetValue($f, $x[1])))
                            ->append(AST::assign($var, AST::array($initElements->map(fn ($x) => $x[0])->toArray())));
                    } else {
                        if (!$f->useResourceTestValue) {
                            // Use a default example value if no values are specified.
                            $initCode = AST::assign($var, $f->exampleValue($this->ctx));
                        } else {
                            $serviceClient = AST::var($this->serviceDetails->clientVarName);
                            $initCode = $this->prod->fieldInit($method, $f, fn () => [$var, $varName], $serviceClient);
                        }
                    }
                    return [$initCode, $var, $f->isRequired];
                } else {
                    return null;
                }
            })
            ->filter(fn ($x) => !is_null($x))
            ->orderBy(fn ($x) => $x[2] ? 0 : 1);

        return [
            // Output var init code.
            $result->map(fn ($x) => $x[0]),
            // Output args to pass to RPC method. Required args are passed individually, optional args passed in array.
            $result->takeWhile(fn ($x) => $x[2])->map(fn ($x) => $x[1])->concat(
                $result->any(fn ($x) => !$x[2]) ?
                    Vector::new([AST::array($result->skipWhile(fn ($x) => $x[2])->toArray(fn ($x) => $x[1]->name, fn ($x) => $x[1]))]) :
                    Vector::new([])
            )
        ];
    }

    private function rpcMethodExampleNormal(MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        [$varsInitCode, $callVars] = $this->initCallVars($method);
        $response = AST::var('response');
        return AST::block(
            AST::assign($serviceClient, AST::new($this->ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                $varsInitCode,
                $method->hasEmptyResponse ?
                    AST::call($serviceClient, AST::method($method->methodName))($callVars) :
                    AST::assign($response, AST::call($serviceClient, AST::method($method->methodName))($callVars))
            )->finally(
                AST::call($serviceClient, AST::method('close'))()
            )
        );
    }

    private function rpcMethodExampleLro(MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $operationResponse = AST::var('operationResponse');
        $result = AST::var('result');
        $error = AST::var('error');
        $operationName = AST::var('operationName');
        $newOperationResponse = AST::var('newOperationResponse');
        $useResponseFn = fn ($var) => AST::if($var->operationSucceeded())
            ->then(
                $method->hasEmptyLroResponse ?
                    '// operation succeeded and returns no value' :
                    Vector::new([
                        AST::assign($result, $var->getResult()),
                        '// doSomethingWith($result)'
                    ])
            )
            ->else(
                AST::assign($error, $var->getError()),
                '// handleError($error)'
            );
        [$varsInitCode, $callVars] = $this->initCallVars($method);
        return AST::block(
            AST::assign($serviceClient, AST::new($this->ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                $varsInitCode,
                AST::assign($operationResponse, AST::call($serviceClient, AST::method($method->methodName))($callVars)),
                $operationResponse->pollUntilComplete(),
                $useResponseFn($operationResponse),
                '// Alternatively:',
                '// start the operation, keep the operation name, and resume later',
                AST::assign($operationResponse, AST::call($serviceClient, AST::method($method->methodName))($callVars)),
                AST::assign($operationName, $operationResponse->getName()),
                '// ... do other work',
                AST::assign($newOperationResponse, $serviceClient->resumeOperation($operationName, $method->methodName)),
                AST::while(AST::not($newOperationResponse->isDone()))(
                    '// ... do other work',
                    $newOperationResponse->reload()
                ),
                $useResponseFn($newOperationResponse)
            )->finally(
                AST::call($serviceClient, AST::method('close'))()
            )
        );
    }

    private function rpcMethodExamplePaginated(MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $pagedResponse = AST::var('pagedResponse');
        $page = AST::var('page');
        $element = AST::var('element');
        [$varsInitCode, $callVars] = $this->initCallVars($method);
        return AST::block(
            AST::assign($serviceClient, AST::new($this->ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                $varsInitCode,
                '// Iterate over pages of elements',
                AST::assign($pagedResponse, AST::call($serviceClient, AST::method($method->methodName))($callVars)),
                AST::foreach($pagedResponse->iteratePages(), $page)(
                    AST::foreach($page, $element)(
                        '// doSomethingWith($element);'
                    )
                ),
                '// Alternatively:',
                '// Iterate through all elements',
                AST::assign($pagedResponse, AST::call($serviceClient, AST::method($method->methodName))($callVars)),
                AST::foreach($pagedResponse->iterateAllElements(), $element)(
                    '// doSomethingWith($element);'
                )
            )->finally(
                $serviceClient->close()
            )
        );
    }

    private function rpcMethodExampleBidiStreaming(MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $requestVars = $method->requiredFields->map(fn ($x) => AST::var($x->camelName));
        $request = AST::var('request');
        $requests = AST::var('requests');
        $stream = AST::var('stream');
        $element = AST::var('element');
        return AST::block(
            AST::assign($serviceClient, AST::new($this->ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                Vector::zip($requestVars, $method->requiredFields, fn ($var, $f) => AST::assign($var, $f->exampleValue($this->ctx))),
                AST::assign($request, AST::new($this->ctx->type($method->requestType))()),
                Vector::zip($method->requiredFields, $requestVars, fn ($field, $param) => AST::call($request, $field->setter)($param)),
                '// Write all requests to the server, then read all responses until the',
                '// stream is complete',
                AST::assign($requests, AST::array([$request])),
                AST::assign($stream, $serviceClient->instanceCall(AST::method($method->methodName))()),
                $stream->writeAll($requests),
                AST::foreach($stream->closeWriteAndReadAll(), $element)(
                    '// doSomethingWith($element);'
                ),
                '// Alternatively:',
                '// Write requests individually, making read() calls if',
                '// required. Call closeWrite() once writes are complete, and read the',
                '// remaining responses from the server.',
                AST::assign($requests, AST::array([$request])),
                AST::assign($stream, $serviceClient->instanceCall(AST::method($method->methodName))()),
                AST::foreach($requests, $request)(
                    $stream->write($request),
                    '// if required, read a single response from the stream',
                    AST::assign($element, $stream->read()),
                    '// doSomethingWith($element)',
                ),
                $stream->closeWrite(),
                AST::assign($element, $stream->read()),
                AST::while(AST::not(AST::call(AST::IS_NULL)($element)))(
                    '// doSomethingWith($element)',
                    AST::assign($element, $stream->read()),
                ),
            )->finally(
                $serviceClient->close()
            )
        );
    }

    private function rpcMethodExampleServerStreaming(MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $stream = AST::var('stream');
        $element = AST::var('element');
        [$varsInitCode, $callVars] = $this->initCallVars($method);
        return AST::block(
            AST::assign($serviceClient, AST::new($this->ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                $varsInitCode,
                '// Read all responses until the stream is complete',
                AST::assign($stream, AST::call($serviceClient, AST::method($method->methodName))($callVars)),
                AST::foreach($stream->readAll(), $element)(
                    '// doSomethingWith($element);'
                ),
            )->finally(
                $serviceClient->close()
            )
        );
    }

    private function rpcMethodExampleClientStreaming(MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $requestVars = $method->requiredFields->map(fn ($x) => AST::var($x->camelName));
        $request = AST::var('request');
        $requests = AST::var('requests');
        $stream = AST::var('stream');
        $result = AST::var('result');
        return AST::block(
            AST::assign($serviceClient, AST::new($this->ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                Vector::zip($requestVars, $method->requiredFields, fn ($var, $f) => AST::assign($var, $f->exampleValue($this->ctx))),
                AST::assign($request, AST::new($this->ctx->type($method->requestType))()),
                Vector::zip($method->requiredFields, $requestVars, fn ($field, $param) => AST::call($request, $field->setter)($param)),
                '// Write data to server and wait for a response',
                AST::assign($requests, AST::array([$request])),
                AST::assign($stream, $serviceClient->instanceCall(AST::method($method->methodName))()),
                AST::assign($result, $stream->writeAllAndReadResponse($requests)),
                '// doSomethingWith($result)',
                '// Alternatively:',
                '// Write data as it becomes available, then wait for a response',
                AST::assign($requests, AST::array([$request])),
                AST::assign($stream, $serviceClient->instanceCall(AST::method($method->methodName))()),
                AST::foreach($requests, $request)(
                    $stream->write($request)
                ),
                AST::assign($result, $stream->readResponse()),
                '// doSomethingWith($result)',
            )->finally(
                $serviceClient->close()
            )
        );
    }
}
