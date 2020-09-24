<?php
/*
 * Copyright 2020 Google LLC
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

namespace Google\Generator\Tests\ProtoTests;

use PHPUnit\Framework\TestCase;
use Google\Generator\CodeGenerator;
use Google\Generator\Tests\ProtoTrait;

final class ProtoTest extends TestCase
{
    use ProtoTrait;

    public function testProtoBasic(): void
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
    use GapicClientTrait;

    /** The name of the service. */
    const SERVICE_NAME = 'testing.basic.Basic';
}

EOF;

        $this->assertEquals($expectedCode, $code);
    }
}
