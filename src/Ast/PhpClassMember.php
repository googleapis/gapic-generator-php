<?php
declare(strict_types=1);

namespace Google\Generator\Ast;

/** A member of a class. */
abstract class PhpClassMember extends AST
{
    use HasPhpDoc;
}
