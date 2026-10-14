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
use Google\Generator\Generation\MethodDetails;
use Google\Generator\Generation\ServiceDetails;
use Google\Generator\Tests\Tools\ProtoLoader;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Generator\Utils\ProtoCatalog;
use Google\Generator\Utils\ServiceYamlConfig;
use Google\Generator\Utils\Transport;
use Google\Protobuf\Internal\FileDescriptorSet;
use PHPUnit\Framework\TestCase;

final class MethodDetailsTest extends TestCase
{
    /**
     * @dataProvider provideResumableUploadPackages
     */
    public function testResumableUploadAllowlistVerifiesPackage(
        string $methodName,
        string $package,
        string $expectedMethodType
    ): void {
        $descBytes = ProtoLoader::loadDescriptorBytes('ProtoTests/ResumableUpload/resumable_upload.proto');
        $descSet = new FileDescriptorSet();
        $descSet->mergeFromString($descBytes);
        $files = Vector::new($descSet->getFile());
        ProtoAugmenter::Augment($files);
        $catalog = new ProtoCatalog($files);
        $file = $files->filter(fn ($x) => $x->getName() === 'tests/Unit/ProtoTests/ResumableUpload/resumable_upload.proto')[0];

        $service = $file->getService()[0];
        $service->getMethod()[0]->setName($methodName);

        $serviceDetails = new ServiceDetails(
            $catalog,
            'Testing\\Resumableupload',
            $package,
            $service,
            $file,
            new ServiceYamlConfig(null),
            Transport::GRPC_REST,
        );

        $this->assertSame($expectedMethodType, $serviceDetails->methods[0]->methodType);
    }

    public function provideResumableUploadPackages(): array
    {
        return [
            ['CreateYouTubeVideoUpload', 'google.ads.googleads.v23.services', MethodDetails::RESUMABLE_UPLOAD],
            ['CreateYouTubeVideoUpload', 'testing.resumableupload', MethodDetails::RESUMABLE_UPLOAD],
            ['CreateYouTubeVideoUpload', 'google.showcase.v1beta1', MethodDetails::NORMAL],
            ['CreateYouTubeVideoUpload', 'google.example.v1', MethodDetails::NORMAL],
            ['UploadMedia', 'google.showcase.v1beta1', MethodDetails::RESUMABLE_UPLOAD],
            ['UploadMedia', 'google.ads.googleads.v23.services', MethodDetails::NORMAL],
            ['UploadMedia', 'google.example.v1', MethodDetails::NORMAL],
        ];
    }
}
