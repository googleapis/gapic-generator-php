<?php declare(strict_types=1);

namespace Google\Generator\Utils;

use \Nette\PhpGenerator\ClassType;

/** Wrap {@see ClassType}, to provide useful helpers. */
class ClassTypeProxy
{
    private ClassType $value;

    public function __construct(string $name)
    {
        $this->value = new ClassType($name);
    }

    /** Pass-through all undefined functions to the underlying {@see ClassType} */
    public function __call(string $name , array $args)
    {
        return call_user_func_array([$this->value, $name], $args);
    }

    /** Override addMember to ignore null values. */
    public function addMember($member): void
    {
        if (!is_null($member))
        {
            $this->value->AddMember($member);
        }
    }

    /** Return the underlying {@see ClassType}. */
    public function getValue(): ClassType
    {
        return $this->value;
    }
}
