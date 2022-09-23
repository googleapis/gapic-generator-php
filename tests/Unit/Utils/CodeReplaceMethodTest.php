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

    public function testWriteMethodToClassScript()
    {
        [$output, $returnVar] = $this->callWriteMethodToClassScript(
            $this->methodFragment, $this->classContents);

        $this->assertStringContainsString('New method content written to ', $output);
        $this->assertEquals(0, $returnVar);
    }

    public function testWriteMethodToClassScriptWithSyntaxError()
    {
        [$output, $returnVar] = $this->callWriteMethodToClassScript(
            'SYNTAX ERROR', $this->classContents);

        $this->assertStringContainsString(
            'Parse error: syntax error, unexpected identifier "SYNTAX"',
            $output
        );
        $this->assertEquals(255, $returnVar);
    }

    private function callWriteMethodToClassScript(
        string $methodFragment,
        string $classContents
    ): array {
        $fragmentFile = tempnam(sys_get_temp_dir(), 'test-snippet-');
        $classFile = tempnam(sys_get_temp_dir(), 'test-class-');
        file_put_contents($fragmentFile, $methodFragment);
        file_put_contents($classFile, $classContents);

        exec(sprintf('php %s/../../../scripts/write_method_to_class.php %s %s',
            __DIR__,
            escapeshellarg($fragmentFile),
            escapeshellarg($classFile)
        ), $output, $returnVar);

        return [implode("\n", $output), $returnVar];
    }
}
