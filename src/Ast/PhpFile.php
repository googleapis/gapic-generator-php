<?php
declare(strict_types=1);

namespace Google\Generator\Ast;

use Google\Generator\Collections\Set;

final class PhpFile extends AST
{
    public function __construct(PhpClass $class)
    {
        $this->class = $class;
        $this->uses = Set::new();
    }

    private Set $uses;

    public function withUses(Set $uses)
    {
        return $this->clone(fn($clone) => $clone->uses = $uses);
    }

    public function toCode(): string
    {
        return
            "<?php\n" .
            "declare(strict_types=1);\n" .
            "\n" .
            "namespace {$this->class->type->getNamespace()};\n" .
            "\n" .
            $this->uses->toVector()->map(fn($x) => "use {$x->getFullname(true)};\n")->join() .
            (count($this->uses) >= 1 ? "\n" : '') .
            static::toPhp($this->class);
    }
}
