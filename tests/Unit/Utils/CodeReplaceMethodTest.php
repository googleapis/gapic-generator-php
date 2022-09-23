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
use Google\Generator\Utils\CodeReplaceMethod;

final class CodeReplaceMethodTest extends TestCase
{
    private $methodFragment = <<<EOL
    public function methodOne(): string
    {
        return \$this->foo;
    }
EOL;

    private $classContents = <<<EOL
<?php
class Bar
{
    public function __construct(private string \$foo)
    {
    }
}
EOL;

    public function testAstMethodReplacer()
    {
        // the class / method to insert into
        // if no method is defined, the first method is used ("__construct" in this case)
        $insertBeforeMethod = new CodeReplaceMethod($this->classContents);

        // Insert the function before this one
        $insertBeforeMethod->insertBefore($this->methodFragment);
        $newClassContents = $insertBeforeMethod->getContents();

        eval(ltrim($newClassContents, '<?php'));

        // Test that the function exists as expected
        $b = new \Bar('bar');
        $this->assertEquals('bar', $b->methodOne());
    }

    public function testAstMethodReplacerWithSyntaxError()
    {
        $this->expectException(\ParseError::class);
        $this->expectExceptionMessage('Insertion caused a syntax error');

        $insertBeforeMethod = new CodeReplaceMethod($this->classContents);
        $insertBeforeMethod->insertBefore('SYNTAX ERROR');
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

        exec(sprintf('php %s/../../../scripts/write_method_to_class.php %s %s 2>&1',
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
            [$this->methodFragment, $this->classContents],
            ['SYNTAX ERROR', $this->classContents, 'syntax error', 255]
        ];
    }
}
