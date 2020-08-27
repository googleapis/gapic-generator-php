<?php
declare(strict_types=1);

namespace Google\Generator\Tests\Ast;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Ast\AST;

final class ASTTest extends TestCase
{
    private static function stripLf($s): string
    {
        return str_replace("\n", '', $s);
    }

    public function testReturnInBlock(): void
    {
        $x = AST::var('x');
        $return = AST::return($x);
        $block = AST::Block($return);
        $this->assertEquals('return $x;', $block->toCode());
    }

    public function testArrayEmpty(): void
    {
        $ast = AST::array([]);
        $this->assertEquals('[]', static::stripLf($ast->toCode()));
    }

    public function testArraySequential(): void
    {
        $x = AST::var('x');
        $ast = AST::array([$x, 2]);
        $this->assertEquals('[$x,2,]', static::stripLf($ast->toCode()));
    }

    public function testArrayAssociative(): void
    {
        $x = AST::var('x');
        $ast = AST::array(['one' => $x, 'two' => 2]);
        $this->assertEquals('[\'one\' => $x,\'two\' => 2,]', static::stripLf($ast->toCode()));
    }

    public function testCallThis(): void
    {
        // TODO: Use a Nette\PhpGenerator\Method as the callee, once the Nette package is referenced.
        $ast = AST::call(AST::THIS, "\0method1")(AST::NULL, AST::NULL);
        $this->assertEquals('$this->method1(null, null)', $ast->toCode());
    }

    public function testCallVar(): void
    {
        // TODO: Use a Nette\PhpGenerator\Method as the callee, once the Nette package is referenced.
        $x = AST::var('x');
        $ast = AST::call($x, "\0method2")($x, $x, $x);
        $this->assertEquals('$x->method2($x, $x, $x)', $ast->toCode());
    }

    public function testAccessVar(): void
    {
        // TODO: Use a Nette\PhpGenerator\Property as the accessee, once the Nette package is referenced.
        $x = AST::var('x');
        $ast = AST::Access($x, "\0property1");
        $this->assertEquals('$x->property1', $ast->toCode());
    }

    public function testAccessSelf(): void
    {
        // TODO: Use a Nette\PhpGenerator\Property as the accessee, once the Nette package is referenced.
        $ast = AST::Access(AST::SELF, "\0property2");
        $this->assertEquals('self::property2', $ast->toCode());
    }
}
