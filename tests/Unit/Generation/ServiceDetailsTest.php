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

use Google\Generator\Collections\Vector;
use Google\Generator\Generation\GapicClientGenerator;
use Google\Generator\Generation\ServiceDetails;
use Google\Generator\Generation\SourceFileContext;
use Google\Generator\Tests\Tools\ProtoLoader;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\ServiceYamlConfig;
use Google\Generator\Utils\Transport;
use Google\Protobuf\Internal\FileDescriptorSet;
use PHPUnit\Framework\TestCase;

final class ServiceDetailsTest extends TestCase
{
    public function testHasOperationsMethodsAndInterface(): void
    {
        $descBytes = ProtoLoader::loadDescriptorBytes('ProtoTests/BasicLro/basic-lro.proto');
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $files = Vector::new($descSet->getFile());
        ProtoAugmenter::augment($files);
        $catalog = new ProtoCatalog($files);

        $operationsFile = $files->filter(fn ($x) => $x->getName() === 'google/longrunning/operations.proto')[0];
        $operationsService = $operationsFile->getService()[0];
        $operationsDetails = new ServiceDetails(
            $catalog,
            ProtoHelpers::getNamespace($operationsFile),
            $operationsFile->getPackage(),
            $operationsService,
            $operationsFile,
            new ServiceYamlConfig(null),
            Transport::GRPC_REST,
        );

        $this->assertTrue($operationsDetails->hasOperationsMethods());

        $ctx = new SourceFileContext($operationsDetails->gapicClientType->getNamespace(), 2026);
        $file = GapicClientGenerator::generate($ctx, $operationsDetails);
        $code = $file->toCode();
        $this->assertStringContainsString('use Google\ApiCore\OperationsClientInterface;', $code);
        $this->assertStringContainsString(
            'final class OperationsClient implements ServiceInterface, OperationsClientInterface',
            $code
        );

        $basicLroFile = $files->filter(fn ($x) => $x->getName() === 'tests/Unit/ProtoTests/BasicLro/basic-lro.proto')[0];
        $basicLroService = $basicLroFile->getService()[0];
        $basicLroDetails = new ServiceDetails(
            $catalog,
            ProtoHelpers::getNamespace($basicLroFile),
            $basicLroFile->getPackage(),
            $basicLroService,
            $basicLroFile,
            new ServiceYamlConfig(null),
            Transport::GRPC_REST,
        );

        $this->assertFalse($basicLroDetails->hasOperationsMethods());
    }
}
