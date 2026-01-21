<?php
/*
 * Copyright 2026 Google LLC
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

use Google\Api\RoutingParameter;
use Google\Api\RoutingRule;
use PHPUnit\Framework\TestCase;
use Google\PostProcessor\ProtobufDeprecationsProcessor;
use Google\Protobuf\RepeatedField;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;

final class ProtobufDeprecationsProcessorTest extends TestCase
{
    private $classContents = <<<EOL
<?php

use Google\Protobuf\Internal\RepeatedField;

class Bar
{
    /**
     * The constructor
     */
    public function __construct(private RepeatedField \$foo)
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

    /**
     * @runInSeparateProcess
     */
    public function testFileBeforeFix()
    {
        eval(ltrim($this->classContents, '<?php'));

        // Verify the deprecations exist
        $param = (new ReflectionClass(\Bar::class))->getConstructor()->getParameters()[0];
        $this->assertNotEquals(RepeatedField::class, $param->getType());
    }

    /**
     * @runInSeparateProcess
     */
    public function testFixRepeatedFieldDeprecations()
    {
        $addFragmentUtil = new ProtobufDeprecationsProcessor($this->classContents);

        // Fix the deprecations
        $addFragmentUtil->fixRepeatedFieldDeprecations();
        $newClassContents = $addFragmentUtil->getContents();

        eval(ltrim($newClassContents, '<?php'));

        // Verify the deprecations were fixed
        $param = (new ReflectionClass(\Bar::class))->getConstructor()->getParameters()[0];
        $this->assertEquals(RepeatedField::class, $param->getType());
    }

    /**
     * @runInSeparateProcess
     */
    public function testFixRepeatedFieldDeprecationsWithProtobufMessage()
    {
        $protobufMessage = __DIR__ . '/../../../generated/Google/Api/RoutingRule.php';

        $addFragmentUtil = new ProtobufDeprecationsProcessor(file_get_contents($protobufMessage));

        // Fix the deprecations
        $addFragmentUtil->fixRepeatedFieldDeprecations();
        $newClassContents = $addFragmentUtil->getContents();

        eval(ltrim($newClassContents, '<?php'));

        $factory  = DocBlockFactory::createInstance();

        $reflection = new ReflectionMethod(RoutingRule::class, 'getRoutingParameters');
        $docblock = $factory->create($reflection->getDocComment());
        $returnTags = $docblock->getTagsByName('return');

        $this->assertEquals('\\' . RepeatedField::class, (string) $returnTags[0]->getType());

        $reflection = new ReflectionMethod(RoutingRule::class, 'setRoutingParameters');
        $docblock = $factory->create($reflection->getDocComment());
        $paramTags = $docblock->getTagsByName('param');

        $this->assertStringContainsString(RepeatedField::class, (string) $paramTags[0]->getType());
    }

    /**
     * @runInSeparateProcess
     */
    public function testFragmentInjectionProcessor()
    {
        $tmpDir = sys_get_temp_dir() . '/test-fragment-injection-processor-' . rand();
        mkdir($tmpDir . '/proto/src', 0777, true);
        file_put_contents($toFile = $tmpDir . '/proto/src/Bar.php', $this->classContents);

        ProtobufDeprecationsProcessor::run($tmpDir);

        // Verify the deprecations were fixed
        require_once($toFile);
        $param = (new ReflectionClass(\Bar::class))->getConstructor()->getParameters()[0];
        $this->assertEquals(RepeatedField::class, $param->getType());

        // Verify post-processor output
        $this->expectOutputString('Deprecations fixed in ' . $toFile . PHP_EOL);
    }
}
