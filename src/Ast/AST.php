<?php declare(strict_types=1);

namespace Google\Generator\Ast;

use Google\Generator\Collections\Vector;

/** Base of the PHP code AST. */
abstract class AST
{
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
        $code = Vector::new($code)
            ->flatten()
            ->filter(fn($x) => !is_null($x));
        return new class($code) extends AST
        {
            public function __construct($code)
            {
                $this->code = $code;
            }
            public function toCode(): string
            {
                return $this->code
                    ->map(fn($x) => $x->toCode() . ';')
                    ->join();
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
        return new class($name) extends Expression
        {
            public function __construct($name)
            {
                $this->name = $name;
            }
            public function ToCode(): string
            {
                return '$' . $this->name;
            }
        };
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
        return new class($expr) extends AST
        {
            public function __construct($expr)
            {
                $this->expr = $expr;
            }
            public function ToCode(): string
            {
                return 'return ' . $this->expr->toCode();
            }
        };
    }

    /**
     * Convert this AST to lines of text suitable for directlyincluding in the output PHP file.
     * The returned string will be mostly unformatted, so an extra formatting step will be required.
     * 
     * @return string
     */
    public abstract function toCode(): string;
}
