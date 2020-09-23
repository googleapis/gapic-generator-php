<?php
declare(strict_types=1);

namespace Google\Generator\Generation;

use Google\Generator\Ast\PhpFile;
use Google\Generator\Collections\Set;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;

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
                $this->uses = $this->uses->add($type);
            }
        }
        return new ResolvedType($type, fn() => $type->name);
    }

    /**
     * Finalize this source context; after this call there must be no further changes.
     * The PhpFile passed in can be altered as necessary for this finalization.
     *
     * @param PhpFile $file The file being generared with this context.
     *
     * @return PhpFile
     */
    public function finalize(PhpFile $file): PhpFile
    {
        return $file->withUses($this->uses);
    }
}
