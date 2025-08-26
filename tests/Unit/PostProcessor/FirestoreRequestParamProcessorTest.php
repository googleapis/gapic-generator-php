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
use Google\PostProcessor\FirestoreRequestParamProcessor;
use ParseError;

final class FirestoreRequestParamProcessorTest extends TestCase
{
    private static $classContents = <<<EOL
<?php
namespace Google\Cloud\Firestore\V1\Client;

final class FirestoreClient
{
    use GapicClientTrait;

    /** The name of the service. */
    private const SERVICE_NAME = 'google.firestore.v1.Firestore';

    private static function getClientDefaults()
    {
        return [
            'serviceName' => self::SERVICE_NAME,
        ];
    }

    public function __construct(array \$options = [])
    {
        \$clientOptions = \$this->buildClientOptions(\$options);
        \$this->setClientOptions(\$clientOptions);
    }

    /**
     * Listens to changes. This method is only available via gRPC or WebChannel
     * (not REST).
     *
     * @example samples/V1/FirestoreClient/listen.php
     *
     * @param array \$callOptions {
     *     Optional.
     *
     *     @type int \$timeoutMillis
     *           Timeout to use for this call.
     * }
     *
     * @return BidiStream
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function listen(array \$callOptions = []): BidiStream
    {
        return \$this->startApiCall('Listen', null, \$callOptions);
    }
}
EOL;

    public function testFirestoreRequestParamProcessor()
    {
        $firestorePostProcessor = new FirestoreRequestParamProcessor(self::$classContents);

        // Insert the function before this one
        $firestorePostProcessor->addDatabaseRequestParamToListenMethod();
        $newClassContents = $firestorePostProcessor->getContents();

        $this->assertStringContainsString('@type string $datbase', $newClassContents);
        $this->assertStringContainsString('new \Google\ApiCore\RequestParamsHeaderDescriptor', $newClassContents);
    }

    public function testFirestoreRequestParamDoesNotContainSyntaxErrors()
    {
        $firestorePostProcessor = new FirestoreRequestParamProcessor(self::$classContents);

        $codeString = $firestorePostProcessor->getContents();
        $tempFile = tempnam(sys_get_temp_dir(), 'phpunit_check_syntax_');
        file_put_contents($tempFile, $codeString);

        $command = 'php -l ' . escapeshellarg($tempFile);
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        $this->assertEquals(0, $returnVar, 'The code output contains a syntax error');

        unlink($tempFile); // Clean up the temporary file
    }
}
