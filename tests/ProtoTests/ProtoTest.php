<?php declare(strict_types=1);

namespace Google\Generator\Tests\ProtoTests;

use PHPUnit\Framework\TestCase;
use Google\Generator\CodeGenerator;
use Google\Generator\Tests\ProtoTrait;

final class ProtoTest extends TestCase
{
    use ProtoTrait;

    public function testCustomOptions(): void
    {
        // TODO: Abstract out this form of testing, once we have more than just this one proto-based test.

        // Load proto descriptor, then generate code.
        $descBytes = $this->loadDescriptorBytes('ProtoTests/Basic/basic.proto');
        $codeIterator = CodeGenerator::GenerateFromDescriptor($descBytes, 'testing.basic');

        // Check generator code is as expected.
        // TODO: Handle multiple output files.
        $code = iterator_to_array($codeIterator)[0];

        // TODO: Move expected text to files, not inline in the test code.
        $expectedCode = <<<'EOF'
<?php

declare(strict_types=1);

namespace testing\basic\Gapic;

use Google\ApiCore\GapicClientTrait;

/**
 * Service Description: This is a basic service.
 *
 * This class provides the ability to make remote calls to the backing service through method
 * calls that map to API methods. Sample code to get started:
 *
 * @experimental
 */
class BasicGapicClient
{
    use \GapicClientTrait;

    /** The name of the service. */
    const SERVICE_NAME = 'testing.basic.Basic';
}

EOF;

        $this->assertEquals($expectedCode, $code);
    }
}
