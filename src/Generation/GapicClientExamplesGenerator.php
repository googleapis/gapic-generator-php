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
use Google\Generator\Collections\Vector;

class GapicClientExamplesGenerator
{
    public function __construct(ServiceDetails $serviceDetails)
    {
        $this->serviceDetails = $serviceDetails;
    }

    private ServiceDetails $serviceDetails;

    public function rpcMethodExample(MethodDetails $method): AST
    {
        // TODO: Handle special arg types; e.g. resources.
        // Create a separate context, as this code isn't part of the generated client.
        $exampleCtx = new SourceFileContext('');
        switch ($method->methodType) {
            case MethodDetails::NORMAL:
                $code = $this->rpcMethodExampleNormal($exampleCtx, $method);
                break;
            case MethodDetails::LRO:
                $code = $this->rpcMethodExampleLro($exampleCtx, $method);
                break;
            case MethodDetails::PAGINATED:
                $code = $this->rpcMethodExamplePaginated($exampleCtx, $method);
                break;
            case MethodDetails::BIDI_STREAMING:
                $code = $this->rpcMethodExampleBidiStreaming($exampleCtx, $method);
                break;
            default:
                throw new \Exception("Cannot handle method-type: '{$method->methodType}'");
        }
        $exampleCtx->finalize(null);
        return $code;
    }

    private function rpcMethodExampleNormal(SourceFileContext $ctx, MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $callVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
        return AST::block(
            AST::assign($serviceClient, AST::new($ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                Vector::zip($callVars, $method->requiredFields, fn($var, $f) => AST::assign($var, $f->type->defaultValue())),
                AST::call($serviceClient, AST::method($method->methodName))($callVars)
            )->finally(
                AST::call($serviceClient, AST::method('close'))()
            )
        );
    }

    private function rpcMethodExampleLro(SourceFileContext $ctx, MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $callVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
        $operationResponse = AST::var('operationResponse');
        $result = AST::var('result');
        $error = AST::var('error');
        $operationName = AST::var('operationName');
        $newOperationResponse = AST::var('newOperationResponse');
        $useResponseFn = fn($var) => AST::if($var->operationSucceeded())
            ->then(
                AST::assign($result, $var->getResult()),
                '// doSomethingWith($result)'
            )
            ->else(
                AST::assign($error, $var->getError()),
                '// handleError($error)'
            );
        return AST::block(
            AST::assign($serviceClient, AST::new($ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                Vector::zip($callVars, $method->requiredFields, fn($var, $f) => AST::assign($var, $f->type->defaultValue())),
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

    private function rpcMethodExamplePaginated(SourceFileContext $ctx, MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $callVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
        $pagedResponse = AST::var('pagedresponse');
        $page = AST::var('page');
        $element = AST::var('element');
        return AST::block(
            AST::assign($serviceClient, AST::new($ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                Vector::zip($callVars, $method->requiredFields, fn($var, $f) => AST::assign($var, $f->type->defaultValue())),
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

    private function rpcMethodExampleBidiStreaming(SourceFileContext $ctx, MethodDetails $method): AST
    {
        $serviceClient = AST::var($this->serviceDetails->clientVarName);
        $requestVars = $method->requiredFields->map(fn($x) => AST::var($x->camelName));
        $request = AST::var('request');
        $requests = AST::var('requests');
        $stream = AST::var('stream');
        $element = AST::var('element');
        return AST::block(
            AST::assign($serviceClient, AST::new($ctx->type($this->serviceDetails->emptyClientType))()),
            AST::try(
                Vector::zip($requestVars, $method->requiredFields, fn($var, $f) => AST::assign($var, $f->type->defaultValue())),
                AST::assign($request, AST::new($ctx->type($method->requestType))()),
                Vector::zip($method->requiredFields, $requestVars, fn($field, $param) => AST::call($request, $field->setter)($param)),
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
}
