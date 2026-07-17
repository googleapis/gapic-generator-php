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

namespace Google\Generator\Tests\Unit\Generation;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Generation\BuildMethodFragmentGenerator;
use Google\Generator\Generation\SourceFileContext;
use Google\Generator\Generation\ServiceDetails;
use Google\Generator\Tests\Tools\ProtoLoader;
use Google\Generator\Utils\MigrationMode;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ServiceYamlConfig;
use Google\Generator\Utils\Transport;

final class BuildMethodFragmentGeneratorTest extends TestCase
{
    public function testBuildMethodNamesWithSpacesInSignatures(): void
    {
        $descBytes = ProtoLoader::loadDescriptorBytes('Utils/example.proto');
        $descSet = new \Google\Protobuf\Internal\FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $files = Vector::new($descSet->getFile());
        ProtoAugmenter::Augment($files);
        $catalog = new ProtoCatalog($files);
        $file = $files->filter(fn ($x) => $x->getName() === 'tests/Unit/Utils/example.proto')[0];

        $service = $file->getService()[0];
        $serviceDetails = new ServiceDetails(
            $catalog,
            'Testing\\Unit\\Generation',
            $file->getPackage(),
            $service,
            $file,
            new ServiceYamlConfig(null),
            Transport::GRPC,
            MigrationMode::NEW_SURFACE_ONLY
        );

        $ctx = new SourceFileContext($serviceDetails->gapicClientType->getNamespace(), 2026);
        $fragments = BuildMethodFragmentGenerator::generate($ctx, $serviceDetails);

        // We expect build method fragments to be generated for Request
        $requestClassName = 'Example/Request';
        $this->assertTrue(isset($fragments[$requestClassName]));

        $buildMethods = $fragments[$requestClassName];
        $this->assertEquals(3, $buildMethods->count());

        // First builder method should be 'build'
        $this->assertEquals('build', $buildMethods[0]->name);

        // Second builder method should be 'buildFromNameNumber' (spaces stripped and camel-cased)
        $this->assertEquals('buildFromNameNumber', $buildMethods[1]->name);

        // Third builder method should be 'buildFromNameUserDisplayName' (dots replaced and camel-cased)
        $this->assertEquals('buildFromNameUserDisplayName', $buildMethods[2]->name);
    }
}
