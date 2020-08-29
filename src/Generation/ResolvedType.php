<?php
declare(strict_types=1);

namespace Google\Generator\Generation;

/**
 * Represent a resolved type, ready to use in code output.
 * This class is required to allow a resolved type to be differentiated from other plain strings.
 */
class ResolvedType
{
    /** @var string The type as is ready to use in code output. */
    public string $typeName;

    /**
     * Construct a ResolvedType.
     * 
     * @param string $typeName The resolved name of the type.
     */
    public function __construct(string $typeName)
    {
        $this->typeName = $typeName;
    }

    public function __toString()
    {
        return $this->typeName;
    }
}
