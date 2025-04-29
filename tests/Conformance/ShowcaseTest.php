<?php
/*
 * Copyright 2021 Google LLC
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

namespace Google\Generator\Tests\Conformance;

use PHPUnit\Framework\TestCase;
use Google\Showcase\V1beta1\Client\EchoClient;
use Google\Showcase\V1beta1\FailEchoWithDetailsRequest;
use Google\ApiCore\InsecureCredentialsWrapper;
use Google\ApiCore\InsecureRequestBuilder;
use Google\ApiCore\Transport\GrpcTransport;
use Google\ApiCore\Transport\RestTransport;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Grpc\ChannelCredentials;

final class ShowcaseTest extends TestCase
{
    public function testFailWithDetailsRest(): void
    {
        $restConfigPath = __DIR__ . '/src/Showcase/V1beta1/resources/echo_rest_client_config.php';
        $requestBuilder = new InsecureRequestBuilder('localhost:7469', $restConfigPath);
        $httpHandler = HttpHandlerFactory::build();
        $transport = new RestTransport($requestBuilder, [$httpHandler, 'async']);
        $echoClient = new EchoClient([
            'apiEndpoint' => 'localhost:7469',
            'credentials' => new InsecureCredentialsWrapper(),
            'transport' => $transport,
        ]);
        $response = $echoClient->failEchoWithDetails(new FailEchoWithDetailsRequest());
    }

    public function testFailWithDetailsGrpc(): void
    {
        $transport = GrpcTransport::build('localhost:7469', ['stubOpts' => ['credentials' => ChannelCredentials::createInsecure()]]);
        $echoClient = new EchoClient([
            //'apiEndpoint' => 'localhost:7469',
            'credentials' => new InsecureCredentialsWrapper(),
            'transport' => $transport,
        ]);
        $response = $echoClient->failEchoWithDetails(new FailEchoWithDetailsRequest());
    }
}
