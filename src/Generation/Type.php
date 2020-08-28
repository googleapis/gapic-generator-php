<?php
declare(strict_types=1);

namespace Google\Generator\Generation;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\Descriptor;
use Google\Generator\Collections\Vector;

/** A fully-specified PHP type. */
class Type
{
    /**
     * Build a type from a class full-name.
     * 
     * @param string $fullname The full name of the class.
     * 
     * @return Type
     */
    public static function fromName(string $fullname): Type
    {
        $parts = Vector::new(explode('\\', $fullname))
            ->skipWhile(fn($x) => $x === ''); // First element will be empty if leading '\' given.
        return new Type($parts->skipLast(1), $parts->last());
    }

    private function __construct(?Vector $namespaceParts, string $name)
    {
        $this->namespaceParts = $namespaceParts;
        $this->name = $name;
    }

    /** @var ?Vector *Readonly* Vector of strings; the namespace parts if a class, otherwise null. */
    public ?Vector $namespaceParts;

    /** @var string *Readonly* The name of type class, or inbuilt type. */
    public string $name;

    /**
     * Does this Type represent a class?
     * 
     * @return bool
     */
    public function isClass(): bool
    {
        return !is_null($this->namespaceParts);
    }

    /**
     * Get the namespace, if this Type represents a class; otherwise throws an exception.
     * 
     * @return string Namespace if a class, otherwise throws an exception.
     */
    public function getNamespace(): string
    {
        if (!$this->isClass()) {
            throw new \Exception('Non-class types do not have a namespace');
        }
        return $this->namespaceParts->join('\\');
    }

    /**
     * Get the full name of this Type.
     * 
     * @return string
     */
    public function getFullname(): string
    {
        return $this->isClass() ? "\\{$this->getNamespace()}\\{$this->name}" : $this->name;
    }
}
