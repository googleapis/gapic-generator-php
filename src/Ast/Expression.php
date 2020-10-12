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

namespace Google\Generator\Ast;

abstract class Expression extends AST implements \ArrayAccess
{
    /**
     * Create an expression to call a method on this instance.
     * This method returns a Callable into which the args are passed.
     *
     * @param mixed $callee The method to call.
     *
     * @return Callable The returned Callable returns an Expression once called with callee args.
     */
    public function instanceCall($callee): Callable
    {
        return AST::call($this, $callee);
    }

    // Allow indexing as a shortcut for AST::index(...)

    public function offsetExists($offset): bool
    {
        return true;
    }

    public function offsetGet($offset)
    {
        return AST::index($this, $offset);
    }

    public function offsetSet($offset, $value): void
    {
        throw new \Exception('Invalid operation (may be used later).');
    }

    public function offsetUnset($offset): void
    {
        throw new \Exception('Invalid operation.');
    }

}
