<?php
/*
 * Copyright 2024 Google LLC
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

namespace Google\Generator\Tests\Unit\ProtoTests;

use PHPUnit\Framework\TestCase;
use Testing\BasicDiregapic\LibraryClient;
use Google\ApiCore\InsecureCredentialsWrapper;
use Google\ApiCore\ValidationException;
use Testing\BasicGrpcOnly\Client\BasicGrpcOnlyClient;

final class ClientTest extends TestCase
{
    public function testUnsupportedTransportThrowsException()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Unexpected transport option "grpc". Supported transports: rest');

        $client = new LibraryClient([
            'transport' => 'grpc',
            'credentials' => new InsecureCredentialsWrapper(),
        ]);
    }

    public function testGrpcOnlyClientThrowsExceptionForRestTransport()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Unexpected transport option "rest". Supported transports: grpc, grpc-fallback');

        $client = new BasicGrpcOnlyClient([
            'transport' => 'rest',
            'credentials' => new InsecureCredentialsWrapper(),
        ]);
    }
}
