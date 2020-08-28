<?php
declare(strict_types=1);

namespace Google\Generator\Generation;

use \Google\Generator\Collections\Set;
use \Nette\PhpGenerator\PhpNamespace;

/** Track per-file data. */
class SourceFileContext
{
    private string $namespace;
    private Set $uses;

    public function __construct()
    {
        $this->namespace = '';
        $this->uses = Set::new();
    }

    /**
     * Set the namespace of this file.
     * 
     * @param string $namespace The current namespace of this file.
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * The type specified is being used in this file.
     * Return the correct ResolvedType to use in the generated source code.
     * 
     * @param Type $type The type being used.
     * 
     * @return ResolvedType
     */
    public function type(Type $type): ResolvedType
    {
        // TODO: Handle type name collisions.
        if ($type->isClass()) {
            if ($type->getNamespace() !== $this->namespace) {
                // No 'use' required if type is in the current namespace
                $fullname = $type->getFullname();
                $this->uses = $this->uses->add($fullname);
            }
        }
        return new ResolvedType($type->name);
    }

    /**
     * Add required 'use' statements to the source file.
     * 
     * @param PhpNamespace $namespace The namespace within the file that requires the 'use' statements.
     */
    public function addUses(PhpNamespace $namespace): void
    {
        // TODO: Sort 'use' statements in canonical order.
        foreach ($this->uses as $use) {
            $namespace->addUse($use);
        }
    }
}
