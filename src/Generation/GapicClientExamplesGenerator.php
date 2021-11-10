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
use Google\Generator\Ast\PhpMethod;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\Helpers;

class GapicClientExamplesGenerator
{
    public function __construct(ServiceDetails $serviceDetails)
    {
        $this->serviceDetails = $serviceDetails;
        // Create a separate context, as this code isn't part of the generated client.
        $this->ctx = new SourceFileContext('');
        $this->prod = new TestNameValueProducer($serviceDetails->catalog, $this->ctx);
    }

    private ServiceDetails $serviceDetails;
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
            case MethodDetails::CUSTOM_OP:
                // Fallthrough - rpcMethodExampleOperation handles custom operations as well.
            case MethodDetails::LRO:
                $code = $this->rpcMethodExampleOperation($method);
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

        $result = $method->allFields->filter(fn ($f) => !$f->isOneOf || $f->isFirstFieldInOneof())
            ->map(function ($f) use ($fnGetValue, $method) {
                if ($f->isRequired) {
                    $varName = $f->useResourceTestValue
                        ? Helpers::toCamelCase("formatted_{$f->name}")
                        : $f->camelName;
                    $var = AST::var($varName);

                    if (!$f->useResourceTestValue) {
                        // Use a default example value if no values are specified.
                        if ($f->isOneOf) {
                            $var = AST::var(Helpers::toCamelCase($f->getOneofDesc()->getName()));
                            $initCode = AST::assign(
                                $var,
                                AST::call(
                                    AST::new($this->ctx->type($f->toOneofWrapperType($method->serviceDetails->namespace)))(),
                                    AST::method("set" . Helpers::toUpperCamelCase($f->camelName))
                                )($f->exampleValue($this->ctx))
                            );
                        } else {
                            $initCode = AST::assign($var, $f->exampleValue($this->ctx));
                        }
                    } else {
                        $serviceClient = AST::var($this->serviceDetails->clientVarName);
                        $astAcc = Vector::new([]);
                        $this->prod->fieldInit($method, $f, $var, $varName, $serviceClient, $astAcc);
                        $initCode = $astAcc === null ? $astAcc : $astAcc->flatten();
                    }
                    return [$initCode, $var, $f->isRequired];
                }
                return null;
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

    // rpcMethodExampleOperation handles both google.longrunning and custom operations.
    private function rpcMethodExampleOperation(MethodDetails $method): AST
    {
        $isCustomOp = $method->methodType === MethodDetails::CUSTOM_OP;
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $operationResponse = AST::var('operationResponse');
        $result = AST::var('result');
        $error = AST::var('error');
        $operationName = AST::var('operationName');
        $nameGetter = $isCustomOp ?
            $method->operationNameField->getter :
            new PhpMethod('getName');
        $noResult = $isCustomOp ?
            '// if creating/modifying, retrieve the target resource':
            '// operation succeeded and returns no value';
        $newOperationResponse = AST::var('newOperationResponse');
        $useResponseFn = fn ($var) => AST::if($var->operationSucceeded())
            ->then(
                // Custom operations and google.protobuf.Empty responses have no result.
                $isCustomOp || $method->hasEmptyLroResponse ?
                    $noResult :
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
                AST::assign($operationName, $operationResponse->instanceCall($nameGetter)()),
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
        $isMap = $method->resourcesField->isMap;
        $element = AST::var('element');
        $indexVar = $isMap ? AST::var('key') : null;
        [$varsInitCode, $callVars] = $this->initCallVars($method);
        return AST::block(
            AST::assign($serviceClient, AST::new($this->ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                $varsInitCode,
                '// Iterate over pages of elements',
                AST::assign($pagedResponse, AST::call($serviceClient, AST::method($method->methodName))($callVars)),
                AST::foreach($pagedResponse->iteratePages(), $page)(
                    AST::foreach($page, $element, $indexVar)(
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
