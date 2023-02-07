<?php
/*
 * Copyright 2022 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
declare(strict_types=1);

namespace Google\Generator\Tests\Unit\Utils;

use PHPUnit\Framework\TestCase;
use Google\Generator\Utils\AddFragmentToClass;

final class AddFragmentToClassTest extends TestCase
{
    private $methodFragment = <<<EOL
    public function methodOne(): string
    {
        return \$this->foo;
    }
EOL;

    private $classContents1 = <<<EOL
<?php
class Bar
{
    public function __construct(private string \$foo)
    {
    }
}
EOL;

    private $classContents2 = <<<'EOL'
<?php
class Bar
{
    private $foo = 'bar';
}
EOL;

    /**
     * @runInSeparateProcess
     * @dataProvider provideAstMethodReplacer
     */
    public function testAstMethodReplacer(string $classContents, string $insertBeforeMethod = null)
    {
        // the class / method to insert into
        // if no method is defined, the first method is used ("__construct" in this case)
        $insertBeforeMethod = new AddFragmentToClass($classContents, $insertBeforeMethod);

        // Insert the function before this one
        $insertBeforeMethod->insert($this->methodFragment);
        $newClassContents = $insertBeforeMethod->getContents();

        eval(ltrim($newClassContents, '<?php'));

        // Test that the function exists as expected
        $b = new \Bar('bar');
        $this->assertEquals('bar', $b->methodOne());
    }

    public function provideAstMethodReplacer()
    {
        return [
            [$this->classContents1],
            [$this->classContents1, '__construct'],
            [$this->classContents2],
        ];
    }

    public function testAstMethodReplacerWithNoClass()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Provided contents does not contain a PHP class');

        $insertBeforeMethod = new AddFragmentToClass('<?php echo "foo";');
    }

    public function testAstMethodReplacerWithNoMethod()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Provided contents does not contain method __construct');

        $insertBeforeMethod = new AddFragmentToClass($this->classContents2);
        $insertBeforeMethod->insert('private $foo;', '__construct');
    }

    public function testAstMethodReplacerWithSyntaxError()
    {
        $this->expectException(\ParseError::class);
        $this->expectExceptionMessage('Provided contents contains a PHP syntax error');

        $insertBeforeMethod = new AddFragmentToClass($this->classContents1);
        $insertBeforeMethod->insert('SYNTAX ERROR');
    }

    /**
     * @dataProvider provideWriteMethodToClassScript
     */
    public function testWriteMethodToClassScript(
        string $methodFragment,
        string $classContents,
        string $expectedOutput = 'New method content written to',
        int $expectedReturnVar = 0,
    ) {
        $fragmentFile = tempnam(sys_get_temp_dir(), 'test-snippet-');
        $classFile = tempnam(sys_get_temp_dir(), 'test-class-');
        file_put_contents($fragmentFile, $methodFragment);
        file_put_contents($classFile, $classContents);

        exec(sprintf('php %s/../../../scripts/write_fragment_to_class.php %s %s 2>&1',
            __DIR__,
            escapeshellarg($fragmentFile),
            escapeshellarg($classFile)
        ), $output, $returnVar);

        $this->assertStringContainsString($expectedOutput, implode("\n", $output));
        $this->assertEquals($expectedReturnVar, $returnVar);
    }

    public function provideWriteMethodToClassScript()
    {
        return [
            [$this->methodFragment, $this->classContents1],
            ['SYNTAX ERROR', $this->classContents1, 'syntax error', 255]
        ];
    }
}
