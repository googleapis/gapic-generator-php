<?php
declare(strict_types=1);

namespace Google\Generator\Ast;

use Google\Generator\Collections\Set;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;

/** A class definition. */
final class PhpClass extends AST
{
    use HasPhpDoc;

    public function __construct(Type $type)
    {
        $this->type = $type;
        $this->traits = Set::new();
        $this->members = Vector::new();
    }

    /** @var Type *Readonly* The type of this class. */
    public Type $type;

    /** @var Set *Readonly* Set of ResolvedType; the traits used by this class. */
    public Set $traits;

    /** @var Vector *Readonly* Vector of PhpClassMember; all members of this class. */
    public Vector $members;

    /**
     * Create a class with an additional trait.
     * 
     * @param ResolvedType $trait Trait to add. Must be a type which is a trait.
     * 
     * @return PhpClass
     */
    public function withTrait(ResolvedType $trait): PhpClass
    {
        if (!$trait->type->isClass()) {
            throw new \Exception('Only classes (traits) may be used as a trait.');
        }
        return $this->clone(fn($clone) => $clone->traits = $clone->traits->add($trait));
    }

    /**
     * Create a class with an additional member.
     * 
     * @param PhpClassMember $member The member to add.
     * 
     * @return PhpClass
     */
    public function withMember(PhpClassMember $member): PhpClass
    {
        return $this->clone(fn($clone) => $clone->members = $clone->members->append($member));
    }

    public function toCode(): string
    {
        return
            $this->phpDocToCode() .
            "class {$this->type->name}\n" .
            "{\n" .
            $this->traits->toVector()->map(fn($x) => "use {$x->toCode()};\n")->join() .
            (count($this->traits) >= 1 ? "\n" : '') .
            $this->members->map(fn($x) => $x->toCode() . "\n")->join() .
            "}\n";
    }
}
