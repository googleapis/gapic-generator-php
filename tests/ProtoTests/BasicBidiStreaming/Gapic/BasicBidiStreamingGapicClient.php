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

/*
 * GENERATED CODE WARNING
 * This file was automatically generated - do not edit!
 */

declare(strict_types=1);

namespace Testing\BasicBidiStreaming\Gapic;

use Google\ApiCore\ApiException;
use Google\ApiCore\Call;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\GapicClientTrait;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\GrpcTransport;
use Google\ApiCore\Transport\RestTransport;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Testing\BasicBidiStreaming\Response;

/**
 *
 * This class provides the ability to make remote calls to the backing service through method
 * calls that map to API methods. Sample code to get started:
 *
 * ```
 * $basicBidiStreamingServiceClient = new BasicBidiStreamingClient();
 * try {
 *     $aNumber = 0;
 *     $request = new Request();
 *     $request->setANumber($aNumber);
 *     // Write all requests to the server, then read all responses until the
 *     // stream is complete
 *     $requests = [
 *         $request,
 *     ];
 *     $stream = $basicBidiStreamingServiceClient->methodBidi();
 *     $stream->writeAll($requests);
 *     foreach ($stream->closeWriteAndReadAll() as $element) {
 *         // doSomethingWith($element);
 *     }
 * // Alternatively:
 *     // Write requests individually, making read() calls if
 *     // required. Call closeWrite() once writes are complete, and read the
 *     // remaining responses from the server.
 *     $requests = [
 *         $request,
 *     ];
 *     $stream = $basicBidiStreamingServiceClient->methodBidi();
 *     foreach ($requests as $request) {
 *         $stream->write($request);
 *         // if required, read a single response from the stream
 *         $element = $stream->read();
 *         // doSomethingWith($element)
 *     }
 *     $stream->closeWrite();
 *     $element = $stream->read();
 *     while (!is_null($element)) {
 *         // doSomethingWith($element)
 *         $element = $stream->read();
 *     }
 * } finally {
 *     $basicBidiStreamingServiceClient->close();
 * }
 * ```
 *
 * @experimental
 */
class BasicBidiStreamingGapicClient
{
    use GapicClientTrait;

    /** The name of the service. */
    const SERVICE_NAME = 'testing.basicbidistreaming.BasicBidiStreaming';

    /** The default address of the service. */
    const SERVICE_ADDRESS = 'bidi.example.com';

    /** The default port of the service. */
    const DEFAULT_SERVICE_PORT = 443;

    /** The name of the code generator, to be included in the agent header. */
    const CODEGEN_NAME = 'gapic';

    /** The default scopes required by the service. */
    public static $serviceScopes = [
        'scope1',
        'scope2',
    ];

    private static function getClientDefaults()
    {
        return [
            'serviceName' => self::SERVICE_NAME,
            'apiEndpoint' => self::SERVICE_ADDRESS . ':' . self::DEFAULT_SERVICE_PORT,
            'clientConfig' => __DIR__ . '/../resources/basic_bidi_streaming_client_config.json',
            'descriptorsConfigPath' => __DIR__ . '/../resources/basic_bidi_streaming_descriptor_config.php',
            'gcpApiConfigPath' => __DIR__ . '/../resources/basic_bidi_streaming_grpc_config.json',
            'credentialsConfig' => [
                'scopes' => self::$serviceScopes,
            ],
            'transportConfig' => [
                'rest' => [
                    'restClientConfigPath' => __DIR__ . '/../resources/basic_bidi_streaming_rest_client_config.php',
                ],
            ],
        ];
    }

    /**
     * Constructor.
     *
     * @param array $options {
     *     Optional. Options for configuring the service API wrapper.
     *
     *     @type string $serviceAddress
     *           **Deprecated**. This option will be removed in a future major release. Please
     *           utilize the `$apiEndpoint` option instead.
     *     @type string $apiEndpoint
     *           The address of the API remote host. May optionally include the port, formatted
     *           as "<uri>:<port>". Default 'bidi.example.com:443'.
     *     @type string|array|FetchAuthTokenInterface|CredentialsWrapper $credentials
     *           The credentials to be used by the client to authorize API calls. This option
     *           accepts either a path to a credentials file, or a decoded credentials file as a
     *           PHP array.
     *           *Advanced usage*: In addition, this option can also accept a pre-constructed
     *           {@see FetchAuthTokenInterface} object or {@see CredentialsWrapper} object. Note
     *           that when one of these objects are provided, any settings in $credentialsConfig
     *           will be ignored.
     *     @type array $credentialsConfig
     *           Options used to configure credentials, including auth token caching, for the
     *           client. For a full list of supporting configuration options, see
     *           {@see CredentialsWrapper::build()}
     *     @type bool $disableRetries
     *           Determines whether or not retries defined by the client configuration should be
     *           disabled. Defaults to `false`.
     *     @type string|array $clientConfig
     *           Client method configuration, including retry settings. This option can be either
     *           a path to a JSON file, or a PHP array containing the decoded JSON data. By
     *           default this settings points to the default client config file, which is
     *           provided in the resources folder.
     *     @type string|TransportInterface $transport
     *           The transport used for executing network requests. May be either the string
     *           `rest` or `grpc`. Defaults to `grpc` if gRPC support is detected on the system.
     *           *Advanced usage*: Additionally, it is possible to pass in an already
     *           instantiated {@see TransportInterface} object. Note that when this object is
     *           provided, any settings in `$transportConfig`, and any `$apiEndpoint` setting,
     *           will be ignored.
     *     @type array $transportConfig
     *           Configuration options that will be used to construct the transport. Options for
     *           each supported transport type should be passed in a key for that transport. For
     *           example:
     *           ```
     *           $transportConfig = [
     *               'grpc' => [
     *                   '...' => '...',
     *               ],
     *               'rest' => [
     *                   '...' => '...',
     *               ],
     *           ];
     *           ```
     *           See the {@see GrpcTransport::build()} and {@see RestTransport::build()} methods
     *           for the supported options.
     * }
     *
     * @throws ValidationException
     *
     * @experimental
     */
    public function __construct(array $options = [])
    {
        $clientOptions = $this->buildClientOptions($options);
        $this->setClientOptions($clientOptions);
    }

    /**
     *
     * Sample code:
     * ```
     * $basicBidiStreamingServiceClient = new BasicBidiStreamingClient();
     * try {
     *     $aNumber = 0;
     *     $request = new Request();
     *     $request->setANumber($aNumber);
     *     // Write all requests to the server, then read all responses until the
     *     // stream is complete
     *     $requests = [
     *         $request,
     *     ];
     *     $stream = $basicBidiStreamingServiceClient->methodBidi();
     *     $stream->writeAll($requests);
     *     foreach ($stream->closeWriteAndReadAll() as $element) {
     *         // doSomethingWith($element);
     *     }
     * // Alternatively:
     *     // Write requests individually, making read() calls if
     *     // required. Call closeWrite() once writes are complete, and read the
     *     // remaining responses from the server.
     *     $requests = [
     *         $request,
     *     ];
     *     $stream = $basicBidiStreamingServiceClient->methodBidi();
     *     foreach ($requests as $request) {
     *         $stream->write($request);
     *         // if required, read a single response from the stream
     *         $element = $stream->read();
     *         // doSomethingWith($element)
     *     }
     *     $stream->closeWrite();
     *     $element = $stream->read();
     *     while (!is_null($element)) {
     *         // doSomethingWith($element)
     *         $element = $stream->read();
     *     }
     * } finally {
     *     $basicBidiStreamingServiceClient->close();
     * }
     * ```
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return Response
     *
     * @throws ApiException if the remote call fails
     *
     * @experimental
     */
    public function methodBidi(array $optionalArgs = [])
    {
        return $this->startCall('MethodBidi', Response::class, $optionalArgs, null, Call::BIDI_STREAMING_CALL);
    }
}
