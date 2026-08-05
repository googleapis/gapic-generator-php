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

namespace Google\Generator\Tests\Unit\Utils;

use PHPUnit\Framework\TestCase;
use Google\Generator\Utils\ServiceYamlConfig;

final class ServiceYamlConfigTest extends TestCase
{
    public function testParsesServiceYamlWithTrailingWhitespaceAndMultipleRuleBlocks(): void
    {
        // YAML with trailing whitespace on blank line between blocks containing 'rules' keys
        $yaml = "type: google.api.Service\n" .
            "name: test.googleapis.com\n" .
            "backend:\n" .
            "  rules:\n" .
            "  - selector: 'google.test.Service.*'\n" .
            "    deadline: 60.0\n" .
            "    \n" .
            "http:\n" .
            "  rules:\n" .
            "  - selector: google.test.Service.TestMethod\n" .
            "    get: '/v1/test'\n";

        $config = new ServiceYamlConfig($yaml);

        $this->assertCount(1, $config->httpRules);
        $this->assertEquals('google.test.Service.TestMethod', $config->httpRules[0]->getSelector());
        $this->assertCount(1, $config->backendRules);
        $this->assertEquals('google.test.Service.*', $config->backendRules[0]->getSelector());
    }

    public function testParsesUnindentedMultiLineStringContinuations(): void
    {
        // Legacy Cloud Functions style YAML with unindented string continuation
        $yaml = "type: google.api.Service\n" .
            "name: cloudfunctions.googleapis.com\n" .
            "documentation:\n" .
            "  summary: 'Manages lightweight user-provided functions executed in response to events.'\n" .
            "  overview: 'Manages lightweight user-provided functions executed in response to\n" .
            "events.'\n" .
            "http:\n" .
            "  rules:\n" .
            "  - selector: google.cloud.functions.v1.CloudFunctionsService.ListFunctions\n" .
            "    get: '/v1/{parent=projects/*/locations/*}/functions'\n";

        $config = new ServiceYamlConfig($yaml);

        $this->assertCount(1, $config->httpRules);
        $this->assertEquals('google.cloud.functions.v1.CloudFunctionsService.ListFunctions', $config->httpRules[0]->getSelector());
    }
}
