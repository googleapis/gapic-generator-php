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

use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;

/** Base of the PHP code AST. */
abstract class AST
{
    /** @var string Constant to reference `$this`. */
    public const THIS = "\0\$this";

    /** @var string Constant to reference `self`. */
    public const SELF = "\0self";

    /** @var string Constant to reference `null`. */
    public const NULL = "\0null";

    /** @var string Constant to reference `__DIR__`. */
    public const __DIR__ = "\0__DIR__";

    /** @var string Constant to reference `__CLASS__`. */
    public const __CLASS__ = "\0__CLASS__";

    /** @var string Constant to reference `isset`. */
    public const ISSET = "\0isset";

    /** @var string Constant to reference `class` as used in `<type>::class`. */
    public const CLS = "\0class";

    /** @var string Constant to reference `count`. */
    public const COUNT = "\0count";

    /** @var string Constant to reference `iterator_to_array`. */
    public const ITERATOR_TO_ARRAY = "\0iterator_to_array";

    /** @var string Constant to reference `is_null`. */
    public const IS_NULL = "\0is_null";

    /** @var string Constant to reference `array_merge`. */
    public const ARRAY_MERGE = "\0array_merge";

    /** @var string Constant to reference `array_unshift`. */
    public const ARRAY_UNSHIFT = "\0array_unshift";

    /** @var string Constant to reference `call_user_func_array`. */
    public const CALL_USER_FUNC_ARRAY = "\0call_user_func_array";

    /** @var string Constant to reference `preg_match`. */
    public const PREG_MATCH = "\0preg_match";

    /** @var string Constant to reference `printf`. */
    public const PRINT_F = "\0printf";

    /** @var string Constant to reference `substr`. */
    public const SUBSTR = "\0substr";

    /** @var string Constant to reference `trigger_error`. */
    public const TRIGGER_ERROR = "\0trigger_error";

    /** @var string Constant to reference `E_USER_ERROR`. */
    public const E_USER_ERROR = "\0E_USER_ERROR";

    /** @var string Constant to reference `parse_url`. */
    public const PARSE_URL = "\0parse_url";

    /** @var string Constant to reference `str_replace`. */
    public const STRING_REPLACE = "\0str_replace";

    /** @var string Constant to reference `empty`. */
    public const EMPTY = "\0empty";

    /** @var string Constant to reference `getenv`. */
    public const GET_ENV = "\0getenv";

    protected static function deref($obj): string
    {
        return $obj === static::SELF || $obj instanceof ResolvedType ? '::' : '->';
    }

    protected static function toPhp($x, &$omitSemicolon = false): string
    {
        if ($x instanceof ShouldNotApplySemicolonInterface) {
            $omitSemicolon = true;
        }

        if (is_string($x)) {
            if (strncmp($x, "\0", 1) === 0) {
                // \0 prefix means the string that follows is used verbatim.
                return substr($x, 1);
            } elseif (substr($x, 0, 2) === '//' || $x === PHP_EOL) {
                // '//' prefix means a comment.
                $omitSemicolon = true;
                return $x;
            } else {
                // Otherwise strings are treated as string literals.
                return "'{$x}'";
            }
        } elseif (is_int($x)) {
            return strval($x);
        } elseif (is_float($x)) {
            $result = strval($x);
            return strpos($result, '.') === false ? $result . '.0' : $result;
        } elseif (is_bool($x)) {
            return $x ? 'true' : 'false';
        } elseif ($x instanceof PhpClassMember) {
            return $x->getName();
        } elseif ($x instanceof AST) {
            return $x->toCode();
        } elseif ($x instanceof ResolvedType) {
            return $x->toCode();
        }  elseif (is_array($x)) {
            return AST::array($x)->toCode();
        } else if ($x instanceof Vector) {
            return AST::array($x->toArray())->toCode();
        } else if ($x instanceof Map) {
            throw new \Exception("Cannot convert Map to PHP code.");
        } else {
            $t = gettype($x);
            throw new \Exception("Cannot convert $t to PHP code.");
        }
    }

    protected function clone(callable $fnOnClone)
    {
        $clone = clone $this;
        $fnOnClone($clone);
        return $clone;
    }

    /**
     * Create a PHP file.
     *
     * @param ?PhpClass $class The class to be contained within this file.
     *
     * @return PhpFile
     */
    public static function file(?PhpClass $class): PhpFile
    {
        return new PhpFile($class);
    }

    /**
     * Create a class.
     *
     * @param Type $type The type of the class to create.
     * @param ?ResolvedType $extends
     * @param bool $final Flag indicating if the class is final or not.
     * @param bool $abstract Flag indicating if the class is abtract or not.
     *
     * @return PhpClass
     */
    public static function class(
        Type $type,
        ?ResolvedType $extends = null,
        bool $final = false,
        bool $abstract = false
    ): PhpClass {
        return new PhpClass($type, $extends, $final, $abstract);
    }

    /**
     * Create a class constant.
     *
     * @param string $name The name of the constant.
     *
     * @return PhpConstant
     */
    public static function constant(string $name): PhpConstant
    {
        return new PhpConstant($name);
    }

    /**
     * Create a class property.
     *
     * @param string $name The name of the property.
     *
     * @return PhpProperty
     */
    public static function property(string $name): PhpProperty
    {
        return new PhpProperty($name);
    }

    /**
     * Create a class method.
     *
     * @param string $name The name of the method.
     *
     * @return PhpMethod
     */
    public static function method(string $name): PhpMethod
    {
        return new PhpMethod($name);
    }

    /**
     * Create a function.
     *
     * @param string $name The name of the function.
     * @param bool $appendNewline Whether a newline should be appended to the end of the function declaration.
     *
     * @return PhpFunction
     */
    public static function fn(string $name, bool $appendNewline = true): PhpFunction
    {
        return new PhpFunction($name, $appendNewline);
    }

    /**
     * Create a parameter.
     *
     * @param ?ResolvedType $type The type of the parameter.
     * @param Variable $var The AST variable used as the parameter.
     * @param mixed $default Optional; the default value of the parameter.
     *
     * @return PhpParam
     */
    public static function param(?ResolvedType $type, Variable $var, $default = null): PhpParam
    {
        return new PhpParam($type, $var, $default);
    }

    /**
     * Create a comment within a class.
     *
     * @param PhpDoc $comment The comment.
     *
     * @return PhpComment
     */
    public static function comment(PhpDoc $comment): PhpComment
    {
        return new PhpComment($comment);
    }

    /**
     * Create a block of PHP code.
     *
     * @param array $code The code to include in this block.
     *     Each item must be an AST instance or a Vector thereof.
     *     Null values will be ignored.
     *
     * @return AST
     */
    public static function block(...$code): AST
    {
        return new PhpBlock($code);
    }

    /**
     * Creates an inline variable comment.
     *
     * @param ResolvedType $type
     * @param Variable $var
     * @param string $comment
     *
     * @return Expression
     */
    public static function inlineVarDoc(ResolvedType $type, Variable $var, string $comment = null): Expression
    {
        return new class($type, $var, $comment) extends Expression {
            public function __construct(ResolvedType $type, Variable $var, string $comment = null)
            {
                $this->type = $type;
                $this->var = $var;
                $this->comment = $comment;
            }
            public function toCode(): string
            {
                if ($this->comment) {
                    $this->comment = ' ' . $this->comment;
                }
                return "/** @var {$this->type->toCode()} {$this->var->toCode()}{$this->comment} */";
            }
        };
    }

    /**
     * Create a literal expression. The value specified is output exactly as-is.
     *
     * @param string $value The value of the literal.
     *
     * @return Expression
     */
    public static function literal(string $value): Expression
    {
        return new class($value) extends Expression {
            public function __construct($value)
            {
                $this->value = $value;
            }
            public function toCode(): string
            {
                return $this->value;
            }
        };
    }

    /**
     * Create an interpolated string, using double quotes as delimiters.
     *
     * @param string $value The value of the string.
     *
     * @return Expression
     */
    public static function interpolatedString(string $value): Expression
    {
        return new class($value) extends Expression {
            public function __construct($value)
            {
                $this->value = $value;
            }
            public function toCode(): string
            {
                return '"' . $this->value . '"';
            }
        };
    }

    /**
     * Create a PHP variable.
     *
     * @param string $name The name of the variable, without leading '$'.
     *
     * @return Expression
     */
    public static function var(string $name): Expression
    {
        return new Variable($name);
    }

    /**
     * Create a 'return' statement, returning the specified expression.
     *
     * @param Expression $expr Expression to return.
     *
     * @return AST
     */
    public static function return(Expression $expr): AST
    {
        return new class($expr) extends AST {
            public function __construct($expr)
            {
                $this->expr = $expr;
            }
            public function toCode(): string
            {
                return 'return ' . static::toPhp($this->expr);
            }
        };
    }

    /**
     * Create a 'throw' statement, throwing the specified expression.
     *
     * @param Expression $expr Expression to throw.
     *
     * @return AST
     */
    public static function throw(Expression $expr): AST
    {
        return new class($expr) extends AST {
            public function __construct($expr)
            {
                $this->expr = $expr;
            }
            public function toCode(): string
            {
                return 'throw ' . static::toPhp($this->expr);
            }
        };
    }

    /**
     * Create an array initializer expression.
     *
     * @param mixed $data The array content. Supports both associative and sequential arrays.
     *     May be an array or a Map.
     * @param bool $oneLine Indicates the code should be generated on one line, rather than multiple.
     *
     * @return Expression
     */
    public static function array(mixed $data, bool $oneLine = false): Expression
    {
        if (is_array($data)) {
            $keyValues = Vector::new(array_map(fn ($v, $k) => [$k, $v], $data, array_keys($data)))
                ->filter(fn ($x) => !is_null($x[1]));
        } elseif ($data instanceof Map) {
            $keyValues = $data->mapValues(fn ($k, $v) => [$k, $v])->values();
        } else {
            throw new \Exception('$data must be an array or a Map.');
        }
        return new class($keyValues, $oneLine) extends Expression {
            public function __construct($keyValues, $oneLine)
            {
                $this->keyValues = $keyValues;
                $this->oneLine = $oneLine;
            }
            public function toCode(): string
            {
                $isAssocArray = $this->keyValues->map(fn ($x) => $x[0])->toArray() !== range(0, count($this->keyValues) - 1);
                $items = $isAssocArray ?
                    $this->keyValues->map(fn ($x) => static::toPhp($x[0]) . ' => ' . static::toPhp($x[1])) :
                    $this->keyValues->map(fn ($x) => static::toPhp($x[1]));
                if (count($items) === 1 && substr($items[0], 0, 4) === 'new ') {
                    return "{$items}";
                }
                if ($this->oneLine) {
                    $itemsStr = $items->skipLast(1)->map(fn ($x) => "{$x}, ")->join();
                    $itemsStr = $itemsStr . "{$items->last()}";
                    return "[{$itemsStr}]";
                }
                $itemsStr = $items->map(fn ($x) => "{$x},\n")->join();
                $firstNl = count($items) === 0 ? '' : "\n";
                return "[{$firstNl}{$itemsStr}]";
            }
        };
    }

    /**
     * Create an array containing an ellipsis `[...]`. For demo code only.
     *
     * @return Expression
     */
    public static function arrayEllipsis(): Expression
    {
        return new class() extends Expression {
            public function toCode(): string
            {
                return '[...]';
            }
        };
    }

    /**
     * Create an expression to access a class property or const.
     *
     * @param mixed $obj The object containing the accessee.
     * @param mixed $accessee The property or const being accessed.
     *
     * @return Expression
     */
    public static function access($obj, $accessee): Expression
    {
        return new class($obj, $accessee) extends Expression {
            public function __construct($obj, $accessee)
            {
                $this->obj = $obj;
                $this->accessee = $accessee;
            }
            public function toCode(): string
            {
                $dollar = $this->obj == AST::SELF && $this->accessee instanceof PhpProperty ? '$' : '';
                return static::toPhp($this->obj) . static::deref($this->obj) . $dollar . static::toPhp($this->accessee);
            }
        };
    }

    /**
     * Create an expression to index an object.
     *
     * @param mixed $obj The object containing the indexer.
     * @param mixed $index The index; null for an empty index.
     *
     * @return Expression
     */
    public static function index($obj, $index): Expression
    {
        return new class($obj, $index) extends Expression {
            public function __construct($obj, $index)
            {
                $this->obj = $obj;
                $this->index = $index;
            }
            public function toCode(): string
            {
                return static::toPhp($this->obj) . '[' .
                    (is_null($this->index) ? '' : static::toPhp($this->index)) .
                    ']';
            }
        };
    }

    /**
     * Create an expression to call a method. This method returns a callable into which the args are passed.
     *
     * @param mixed $obj The object containing the method to call; or a built-in function.
     * @param mixed $callee The method to call; or null if calling a built-in function.
     *
     * @return callable The returned callable returns an Expression once called with callee args.
     */
    public static function call($obj, $callee = null): callable
    {
        return fn (...$args) => new class($obj, $callee, $args) extends Expression {
            public function __construct($obj, $callee, $args)
            {
                $this->obj = $obj;
                $this->callee = $callee;
                $this->args = Vector::new($args)->flatten();
            }
            public function toCode(): string
            {
                $args = $this->args->map(fn ($x) => static::toPhp($x))->join(', ');
                if (is_null($this->callee)) {
                    return static::toPhp($this->obj) . "({$args})";
                } else {
                    // Handle calling a function directly on a constructor.
                    // We assume that a constructor call will always start with `new `.
                    $objCode = static::toPhp($this->obj);
                    $calleeOnNewline = false;
                    if (substr($objCode, 0, 4) === 'new ') {
                        $objCode = '(' . $objCode . ')';
                        $calleeOnNewline = true;
                    } elseif (substr($objCode, 0, 5) === '(new ') {
                        $calleeOnNewline = true;
                    }

                    return $objCode .
                        ($calleeOnNewline ? PHP_EOL : null) .
                        static::deref($this->obj) .
                        static::toPhp($this->callee) .
                        "({$args})";
                }
            }
        };
    }

    /**
     * Create an expression to call a static method. This method returns a callable into which the args are passed.
     *
     * @param ResolvedType $type The object type to intantiate the call from.
     * @param mixed $callee The method to call.
     *
     * @return callable The returned callable returns an Expression once called with callee args.
     */
    public static function staticCall(ResolvedType $type, $callee): callable
    {
        return fn (...$args) => new class($type, $callee, $args) extends Expression {
            public function __construct($type, $callee, $args)
            {
                $this->type = $type;
                $this->callee = $callee;
                $this->args = Vector::new($args)->flatten();
            }

            public function toCode(): string
            {
                $args = $this->args->map(fn ($x) => static::toPhp($x))->join(', ');
                return static::toPhp($this->type) . '::' . static::toPhp($this->callee) . "({$args})";
            }
        };
    }

    /**
     * Create an object instantiation expression. This method returns a callable into which the args are passed.
     *
     * @param ResolvedType $type The type to instantiate.
     *
     * @return callable The returned callable returns an Expression once called with callee args.
     */
    public static function new(ResolvedType $type): callable
    {
        return fn (...$args) => new class($type, Vector::new($args)) extends Expression {
            public function __construct($type, $args)
            {
                $this->type = $type;
                $this->args = $args;
            }

            public function toCode(): string
            {
                $args = $this->args->map(fn ($x) => static::toPhp($x))->join(', ');
                return 'new ' . static::toPhp($this->type) . "({$args})";
            }
        };
    }

    /**
     * Create a string concat expression.
     *
     * @param array $items The items to concat.
     *
     * @return Expression
     */
    public static function concat(...$items): ?Expression
    {
        $items = Vector::New($items);
        $null = $items->any(fn ($x) => is_null($x));
        return $null ? null : new class($items) extends Expression {
            public function __construct($items)
            {
                $this->items = $items;
            }
            public function toCode(): string
            {
                return $this->items->map(fn ($x) => static::toPhp($x))->join(' . ');
            }
        };
    }

    /**
     * Create an assignment expression.
     *
     * @param AST $to Assign a value to this.
     * @param mixed $from Assign from this.
     *
     * @return Expression
     */
    public static function assign(AST $to, $from): Expression
    {
        return new class($to, $from) extends Expression {
            public function __construct($to, $from)
            {
                $this->to = $to;
                $this->from = $from;
            }
            public function toCode(): string
            {
                return static::toPhp($this->to) . " = " . static::toPhp($this->from);
            }
        };
    }

    /**
     * Create a binary operation expression.
     *
     * @param AST $lhs The left-hand side of the binary operation.
     * @param string $op The operation to preform.
     * @param mixed $rhs The right-hand side of the binary operation.
     *
     * @return Expression
     */
    public static function binaryOp(AST $lhs, string $op, $rhs): Expression
    {
        return new class($lhs, $op, $rhs) extends Expression {
            public function __construct($lhs, $op, $rhs)
            {
                $this->lhs = $lhs;
                $this->op = $op;
                $this->rhs = $rhs;
            }
            public function toCode(): string
            {
                return static::toPhp($this->lhs) . " {$this->op} " . static::toPhp($this->rhs);
            }
        };
    }

    /**
     * Create a not expression.
     *
     * @param Expression $operand The operand to logically negate.
     *
     * @return Expression
     */
    public static function not(Expression $operand): Expression
    {
        return new class($operand) extends Expression {
            public function __construct($operand)
            {
                $this->operand = $operand;
            }
            public function toCode(): string
            {
                return '!' . static::toPhp($this->operand);
            }
        };
    }

    /**
     * Create an if statement.
     *
     * @param Expression $expr The conditional expression for the if statement.
     * @param bool $appendNewline Whether a newline should be appended to the end of the statement.
     *
     * @return AST
     */
    public static function if(Expression $expr, bool $appendNewline = true): AST
    {
        return new class($expr, $appendNewline) extends AST implements ShouldNotApplySemicolonInterface {
            public function __construct($expr, $appendNewline)
            {
                $this->expr = $expr;
                $this->then = null;
                $this->elseif = Vector::new([]);
                $this->else = null;
                $this->appendNewline = $appendNewline;
            }
            public function then(...$code)
            {
                return $this->clone(fn ($clone) => $clone->then = AST::block(...$code));
            }
            public function elseif(Expression $cond, ...$code)
            {
                // [Condition, AST::block]
                return $this->clone(fn ($clone) => $clone->elseif = $clone->elseif->append([$cond, AST::block(...$code)]));
            }
            public function else(...$code)
            {
                return $this->clone(fn ($clone) => $clone->else = AST::block(...$code));
            }
            public function toCode(): string
            {
                $elseif = implode("", $this->elseif->map(
                    fn ($arrayCondBlockPair) =>
                        " elseif (" . static::toPhp($arrayCondBlockPair[0]) . ") {\n"  .
                        static::toPhp($arrayCondBlockPair[1]) . "\n" .
                        "}"
                )->toArray());
                $else = is_null($this->else) ? '' :
                    " else {\n" .
                    static::toPhp($this->else) . "\n" .
                    "}";
                $code = 'if (' . static::toPhp($this->expr) . ") {\n" .
                    static::toPhp($this->then) . "\n" .
                    "}{$elseif}{$else}";

                if ($this->appendNewline) {
                    $code .= "\n";
                }

                return $code;
            }
        };
    }

    /**
     * Create a '?:' expression.
     *
     * @param Expression $expr The conditional expression for the ternary expression.
     * @param Expression $true The expression to use if $expr evaluates to true.
     * @param Expression $false The expression to use if $expr evaluates to false.
     *
     * @return Expression
     */
    public static function ternary(Expression $expr, Expression $true, Expression $false): Expression
    {
        return new class($expr, $true, $false) extends Expression {
            public function __construct($expr, $true, $false)
            {
                $this->expr = $expr;
                $this->true = $true;
                $this->false = $false;
            }
            public function toCode(): string
            {
                return static::toPhp($this->expr) .
                    ' ? ' . static::toPhp($this->true) .
                    ' : ' . static::toPhp($this->false);
            }
        };
    }

    /**
     * Create a '??' expression.
     *
     * @param Expression $expr The expression to check \isset and !\is_null, as well as the value to return if true.
     * @param Expression $false The expresion to return if $expr is not set or is null.
     *
     * @return Expression
     */
    public static function nullCoalescing(Expression $expr, Expression $false): Expression
    {
        return new class($expr, $false) extends Expression {
            public function __construct($expr, $false)
            {
                $this->expr = $expr;
                $this->false = $false;
            }
            public function toCode(): string
            {
                return static::toPhp($this->expr) .
                    ' ?? ' . static::toPhp($this->false);
            }
        };
    }

    /**
     * Create a '??=' (noal-coalescing assignment) expression.
     *
     * @param AST $to Assign a value to this.
     * @param mixed $from Assign from this.
     *
     * @return Expression
     */
    public static function nullCoalescingAssign(AST $to, $from): Expression
    {
        return new class($to, $from) extends Expression {
            public function __construct($to, $from)
            {
                $this->to = $to;
                $this->from = $from;
            }
            public function toCode(): string
            {
                return static::toPhp($this->to) .
                    ' ??= ' . static::toPhp($this->from);
            }
        };
    }

    /**
     * Create a while statement. This method returns a callable into which the loop code is passed.
     *
     * @param Expression $condition The condition checked at the top of the while loop.
     *
     * @return callable The returned callable returns a while statement once called with the loop code.
     */
    public static function while(Expression $condition): callable
    {
        return fn (...$code) => new class($condition, $code) extends AST implements ShouldNotApplySemicolonInterface {
            public function __construct($condition, $code)
            {
                $this->condition = $condition;
                $this->code = AST::block(...$code);
            }
            public function toCode(): string
            {
                return 'while (' . static::toPhp($this->condition) . ") {\n" .
                    static::toPhp($this->code) .
                    "\n}\n";
            }
        };
    }

    /**
     * Create a foreach statement. This method returns a callable into which the foreach body code is passed.
     *
     * @param Expression $expr The expression to foreach over.
     * @param Variable $var The variable into which each element is placed.
     * @param Variable $indexVar Optional; The index variable, if required.
     *
     * @return callable The returned callable returns a foreach statement once called with the foreach body code.
     */
    public static function foreach(Expression $expr, Variable $var, ?Variable $indexVar = null): callable
    {
        return fn (...$code) => new class($expr, $var, $indexVar, $code) extends AST implements ShouldNotApplySemicolonInterface {
            public function __construct($expr, $var, $indexVar, $code)
            {
                $this->expr = $expr;
                $this->var = $var;
                $this->indexVar = $indexVar;
                $this->code = AST::block(...$code);
            }
            public function toCode(): string
            {
                $index = is_null($this->indexVar) ? '' : (static::toPhp($this->indexVar) . ' => ');
                return 'foreach (' . static::toPhp($this->expr) . ' as ' . $index . static::toPhp($this->var) . ") {\n" .
                    static::toPhp($this->code) .
                    "\n}\n";
            }
        };
    }

    /**
     * Create a try/catch/finally statement.
     *
     * @param array $tryCode The code to use in the try block.
     *
     * @return AST
     */
    public static function try(...$tryCode): AST
    {
        return new class($tryCode) extends AST implements ShouldNotApplySemicolonInterface {
            public function __construct($tryCode)
            {
                $this->tryCode = AST::block(...$tryCode);
                $this->catch = null;
                $this->finallyCode = null;
            }
            public function catch(ResolvedType $type, Variable $var): callable
            {
                return fn (...$catchCode) =>
                    $this->clone(fn ($clone) => $clone->catch = [$type, $var, AST::block(...$catchCode)]);
            }
            public function finally(...$finallyCode): AST
            {
                return $this->clone(fn ($clone) => $clone->finallyCode = AST::block(...$finallyCode));
            }
            public function toCode(): string
            {
                return
                    "try {\n" .
                    static::toPhp($this->tryCode) . "\n" .
                    '}' .
                    (is_null($this->catch) ? '' : 'catch (' .
                        static::toPhp($this->catch[0]) . ' ' . static::toPhp($this->catch[1]) . ") {\n" .
                        static::toPhp($this->catch[2]) . "\n}") .
                    (is_null($this->finallyCode) ? '' : "finally {\n" . static::toPhp($this->finallyCode) . "\n}" . PHP_EOL);
            }
        };
    }

    /**
     * Convert this AST to lines of text suitable for directlyincluding in the output PHP file.
     * The returned string will be mostly unformatted, so an extra formatting step will be required.
     *
     * @return string
     */
    abstract public function toCode(): string;
}
