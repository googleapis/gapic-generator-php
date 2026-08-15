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

/*
 * GENERATED CODE WARNING
 * This file was automatically generated - do not edit!
 */

namespace Testing\Resumableupload\Tests\Unit\Client;

use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\ResumableUpload\ResumableUpload;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use ReflectionClass;
use Testing\Resumableupload\Client\ResumableUploadClient;
use Testing\Resumableupload\CreateYouTubeVideoUploadRequest;

/**
 * @group resumableupload
 *
 * @group gapic
 */
class ResumableUploadClientTest extends GeneratedTest
{
    /** @return TransportInterface */
    private function createTransport($deserialize = null)
    {
        return new MockTransport($deserialize);
    }

    /** @return CredentialsWrapper */
    private function createCredentials()
    {
        return $this->getMockBuilder(CredentialsWrapper::class)->disableOriginalConstructor()->getMock();
    }

    /** @return ResumableUploadClient */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new ResumableUploadClient($options);
    }

    /** @test */
    public function createYouTubeVideoUploadTest()
    {
        $transport = $this->createTransport();
        $gapicClient = $this->createClient([
            'transport' => $transport,
        ]);
        $request = new CreateYouTubeVideoUploadRequest();
        $upload = $gapicClient->createYouTubeVideoUpload($request);
        $this->assertInstanceOf(ResumableUpload::class, $upload);
        $callProp = (new ReflectionClass($upload))
            ->getProperty('call');
        $this->assertSame('testing.resumableupload.ResumableUpload/CreateYouTubeVideoUpload', $callProp->getValue($upload)->getMethod());
    }
}
