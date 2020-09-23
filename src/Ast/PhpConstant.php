<?php
declare(strict_types=1);

namespace Google\Generator\Ast;

/** A constant within a class. */
final class PhpConstant extends PhpClassMember
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a constant with the specified value.
     * 
     * @param mixed $value The value of the constant.
     * 
     * @return PhpConstant
     */
    public function withValue($value): PhpConstant
    {
        return $this->clone(fn($clone) => $clone->value = $value);
    }

    public function toCode(): string
    {
        return
            $this->phpDocToCode() .
            "const {$this->name} = " . static::toPhp($this->value) . ';';
    }
}
