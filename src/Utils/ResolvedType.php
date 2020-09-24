<?php
declare(strict_types=1);

namespace Google\Generator\Utils;

/**
 * Represent a resolved type, ready to use in code output.
 * This class is required to allow a resolved type to be differentiated from other plain strings.
 */
class ResolvedType
{
    /**
     * Construct a ResolvedType.
     * 
     * @param string $typeName The resolved name of the type.
     */
    public function __construct(Type $type, \Closure $fnToCode)
    {
        $this->type = $type;
        $this->fnToCode = $fnToCode;
    }

    /** @var Type *Readonly* The type of this resolved-type. */
    public Type $type;

    private \Closure $fnToCode;

    public function toCode(): string
    {
        return ($this->fnToCode)();
    }
}
