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

namespace Google\Generator\Tests\Unit\PostProcessor;

use PHPUnit\Framework\TestCase;
use Google\PostProcessor\FragmentInjectionProcessor;
use ParseError;

final class FragmentInjectionProcessorTest extends TestCase
{
    private $methodFragment = <<<EOL
    /**
     * The fragment
     */
    public function methodOne(): string
    {
        return \$this->foo;
    }
EOL;

    private $classContents1 = <<<EOL
<?php
class Bar
{
    /**
     * The constructor
     */
    public function __construct(private string \$foo)
    {
    }
    /**
     * Empty method
     */
    public function bar()
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
        $addFragmentUtil = new FragmentInjectionProcessor($classContents);

        // Insert the function before this one
        $addFragmentUtil->insert($this->methodFragment, $insertBeforeMethod);
        $newClassContents = $addFragmentUtil->getContents();

        eval(ltrim($newClassContents, '<?php'));

        // Test that the function exists as expected
        $b = new \Bar('bar');
        $this->assertEquals('bar', $b->methodOne());
    }

    public function provideAstMethodReplacer()
    {
        return [
            [$this->classContents1],
            [$this->classContents1, 'bar'],
            [$this->classContents2],
        ];
    }

    public function testAstMethodReplacerWithNoClass()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Provided contents does not contain a PHP class');

        $addFragmentUtil = new FragmentInjectionProcessor('<?php echo "foo";');
    }

    public function testAstMethodReplacerWithNoMethod()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Provided contents does not contain method __construct');

        $addFragmentUtil = new FragmentInjectionProcessor($this->classContents2);
        $addFragmentUtil->insert('private $foo;', '__construct');
    }

    public function testAstMethodReplacerWithSyntaxError()
    {
        $this->expectException(\ParseError::class);
        $this->expectExceptionMessage('Provided contents contains a PHP syntax error');

        $addFragmentUtil = new FragmentInjectionProcessor($this->classContents1);
        $addFragmentUtil->insert('SYNTAX ERROR');
    }

    public function testFragmentInjectionProcessor()
    {
        $tmpDir = sys_get_temp_dir() . '/test-fragment-injection-processor-' . rand();
        mkdir($tmpDir . '/fragments', 0777, true);
        mkdir($tmpDir . '/proto/src', 0777, true);
        file_put_contents($tmpDir . '/fragments/Bar.build.txt', $this->methodFragment);
        file_put_contents($toFile = $tmpDir . '/proto/src/Bar.php', $this->classContents1);

        FragmentInjectionProcessor::run($tmpDir);

        // If we've gotten here, the PHP code is valid. Just assert that the contents have changed.
        $this->expectOutputString('Fragment written to ' . $toFile . PHP_EOL);
    }

    public function testFragmentInjectionProcessorFailsOnSyntaxError()
    {
        $this->expectException(ParseError::class);
        $this->expectExceptionMessage('Provided contents contains a PHP syntax error');

        $tmpDir = sys_get_temp_dir() . '/test-fragment-injection-processor-' . rand();
        mkdir($tmpDir . '/fragments', 0777, true);
        mkdir($tmpDir . '/proto/src', 0777, true);
        file_put_contents($tmpDir . '/fragments/Bar.build.txt', 'SYNTAX ERROR');
        file_put_contents($tmpDir . '/proto/src/Bar.php', $this->classContents);

        FragmentInjectionProcessor::run($tmpDir);
    }

    public function testFragmentInjectionProcessorSkipsMissingClasses()
    {
        $tmpDir = sys_get_temp_dir() . '/test-fragment-injection-processor-' . rand();
        mkdir($tmpDir . '/fragments', 0777, true);
        file_put_contents($tmpDir . '/fragments/Bar.build.txt', $this->methodFragment);

        FragmentInjectionProcessor::run($tmpDir);

        // If we've gotten here, the PHP code is valid. Just assert that the contents have changed.
        $this->expectOutputString("Class not found for fragments/Bar.build.txt - Skipping.\n");
    }
}
