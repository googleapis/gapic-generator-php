<?php declare(strict_types=1);

namespace Google\Generator\Tests\Ast;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Ast\AST;

final class ASTTest extends TestCase
{
    public function testReturn(): void
    {
        $x = AST::var('x');
        $return = AST::return($x);
        $block = AST::Block($return);
        $this->assertEquals('return $x;', $block->toCode());
    }
}
