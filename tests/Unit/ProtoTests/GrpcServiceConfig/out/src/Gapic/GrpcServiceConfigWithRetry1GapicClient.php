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
 * This file was generated from the file
 * https://github.com/google/googleapis/blob/master/tests/Unit/ProtoTests/GrpcServiceConfig/grpc-service-config1.proto
 * and updates to that file get reflected here through a refresh process.
 *
 * @experimental
 */

namespace Testing\GrpcServiceConfig\Gapic;

use Google\ApiCore\ApiException;

use Google\ApiCore\Call;

use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\GapicClientTrait;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\OperationResponse;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Google\LongRunning\Operation;
use Testing\GrpcServiceConfig\Request1;
use Testing\GrpcServiceConfig\Response1;

/**
 * Service Description:
 *
 * This class provides the ability to make remote calls to the backing service through method
 * calls that map to API methods. Sample code to get started:
 *
 * ```
 * $grpcServiceConfigWithRetry1Client = new GrpcServiceConfigWithRetry1Client();
 * try {
 *     $response = $grpcServiceConfigWithRetry1Client->method1A();
 * } finally {
 *     $grpcServiceConfigWithRetry1Client->close();
 * }
 * ```
 *
 * @experimental
 */
class GrpcServiceConfigWithRetry1GapicClient
{
    use GapicClientTrait;

    /** The name of the service. */
    const SERVICE_NAME = 'testing.grpcserviceconfig.GrpcServiceConfigWithRetry1';

    /** The default address of the service. */
    const SERVICE_ADDRESS = 'grpcserviceconfig.example.com';

    /** The default port of the service. */
    const DEFAULT_SERVICE_PORT = 443;

    /** The name of the code generator, to be included in the agent header. */
    const CODEGEN_NAME = 'gapic';

    /** The default scopes required by the service. */
    public static $serviceScopes = [];

    private $operationsClient;

    private static function getClientDefaults()
    {
        return [
            'serviceName' => self::SERVICE_NAME,
            'serviceAddress' => self::SERVICE_ADDRESS . ':' . self::DEFAULT_SERVICE_PORT,
            'clientConfig' => __DIR__ . '/../resources/grpc_service_config_with_retry1_client_config.json',
            'descriptorsConfigPath' => __DIR__ . '/../resources/grpc_service_config_with_retry1_descriptor_config.php',
            'gcpApiConfigPath' => __DIR__ . '/../resources/grpc_service_config_with_retry1_grpc_config.json',
            'credentialsConfig' => [
                'defaultScopes' => self::$serviceScopes,
            ],
            'transportConfig' => [
                'rest' => [
                    'restClientConfigPath' => __DIR__ . '/../resources/grpc_service_config_with_retry1_rest_client_config.php',
                ],
            ],
        ];
    }

    /**
     * Return an OperationsClient object with the same endpoint as $this.
     *
     * @return OperationsClient
     *
     * @experimental
     */
    public function getOperationsClient()
    {
        return $this->operationsClient;
    }

    /**
     * Resume an existing long running operation that was previously started by a long
     * running API method. If $methodName is not provided, or does not match a long
     * running API method, then the operation can still be resumed, but the
     * OperationResponse object will not deserialize the final response.
     *
     * @param string $operationName The name of the long running operation
     * @param string $methodName    The name of the method used to start the operation
     *
     * @return OperationResponse
     *
     * @experimental
     */
    public function resumeOperation($operationName, $methodName = null)
    {
        $options = isset($this->descriptors[$methodName]['longRunning']) ? $this->descriptors[$methodName]['longRunning'] : [];
        $operation = new OperationResponse($operationName, $this->getOperationsClient(), $options);
        $operation->reload();
        return $operation;
    }

    /**
     * Constructor.
     *
     * @param array $options {
     *     Optional. Options for configuring the service API wrapper.
     *
     *     @type string $serviceAddress
     *           The address of the API remote host. May optionally include the port, formatted
     *           as "<uri>:<port>". Default 'grpcserviceconfig.example.com:443'.
     *     @type string|array|FetchAuthTokenInterface|CredentialsWrapper $credentials
     *           The credentials to be used by the client to authorize API calls. This option
     *           accepts either a path to a credentials file, or a decoded credentials file as a
     *           PHP array.
     *           *Advanced usage*: In addition, this option can also accept a pre-constructed
     *           {@see \Google\Auth\FetchAuthTokenInterface} object or
     *           {@see \Google\ApiCore\CredentialsWrapper} object. Note that when one of these
     *           objects are provided, any settings in $credentialsConfig will be ignored.
     *     @type array $credentialsConfig
     *           Options used to configure credentials, including auth token caching, for the
     *           client. For a full list of supporting configuration options, see
     *           {@see \Google\ApiCore\CredentialsWrapper::build()} .
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
     *           instantiated {@see \Google\ApiCore\Transport\TransportInterface} object. Note
     *           that when this object is provided, any settings in $transportConfig, and any
     *           $serviceAddress setting, will be ignored.
     *     @type array $transportConfig
     *           Configuration options that will be used to construct the transport. Options for
     *           each supported transport type should be passed in a key for that transport. For
     *           example:
     *           $transportConfig = [
     *               'grpc' => [...],
     *               'rest' => [...],
     *           ];
     *           See the {@see \Google\ApiCore\Transport\GrpcTransport::build()} and
     *           {@see \Google\ApiCore\Transport\RestTransport::build()} methods for the
     *           supported options.
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
        $this->operationsClient = $this->createOperationsClient($clientOptions);
    }

    /**
     *
     * Sample code:
     * ```
     * $grpcServiceConfigWithRetry1Client = new GrpcServiceConfigWithRetry1Client();
     * try {
     *     $response = $grpcServiceConfigWithRetry1Client->method1A();
     * } finally {
     *     $grpcServiceConfigWithRetry1Client->close();
     * }
     * ```
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a
     *           {@see Google\ApiCore\RetrySettings} object, or an associative array of retry
     *           settings parameters. See the documentation on
     *           {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Testing\GrpcServiceConfig\Response1
     *
     * @throws ApiException if the remote call fails
     *
     * @experimental
     */
    public function method1A(array $optionalArgs = [])
    {
        $request = new Request1();
        return $this->startCall('Method1A', Response1::class, $optionalArgs, $request)->wait();
    }

    /**
     *
     * Sample code:
     * ```
     * $grpcServiceConfigWithRetry1Client = new GrpcServiceConfigWithRetry1Client();
     * try {
     *     $operationResponse = $grpcServiceConfigWithRetry1Client->method1BLro();
     *     $operationResponse->pollUntilComplete();
     *     if ($operationResponse->operationSucceeded()) {
     *         $result = $operationResponse->getResult();
     *     // doSomethingWith($result)
     *     } else {
     *         $error = $operationResponse->getError();
     *         // handleError($error)
     *     }
     *     // Alternatively:
     *     // start the operation, keep the operation name, and resume later
     *     $operationResponse = $grpcServiceConfigWithRetry1Client->method1BLro();
     *     $operationName = $operationResponse->getName();
     *     // ... do other work
     *     $newOperationResponse = $grpcServiceConfigWithRetry1Client->resumeOperation($operationName, 'method1BLro');
     *     while (!$newOperationResponse->isDone()) {
     *         // ... do other work
     *         $newOperationResponse->reload();
     *     }
     *     if ($newOperationResponse->operationSucceeded()) {
     *         $result = $newOperationResponse->getResult();
     *     // doSomethingWith($result)
     *     } else {
     *         $error = $newOperationResponse->getError();
     *         // handleError($error)
     *     }
     * } finally {
     *     $grpcServiceConfigWithRetry1Client->close();
     * }
     * ```
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a
     *           {@see Google\ApiCore\RetrySettings} object, or an associative array of retry
     *           settings parameters. See the documentation on
     *           {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Google\ApiCore\OperationResponse
     *
     * @throws ApiException if the remote call fails
     *
     * @experimental
     */
    public function method1BLro(array $optionalArgs = [])
    {
        $request = new Request1();
        return $this->startOperationsCall('Method1BLro', $optionalArgs, $request, $this->getOperationsClient())->wait();
    }

    /**
     *
     * Sample code:
     * ```
     * $grpcServiceConfigWithRetry1Client = new GrpcServiceConfigWithRetry1Client();
     * try {
     *     $request = new Request1();
     *     // Write all requests to the server, then read all responses until the
     *     // stream is complete
     *     $requests = [
     *         $request,
     *     ];
     *     $stream = $grpcServiceConfigWithRetry1Client->method1BidiStreaming();
     *     $stream->writeAll($requests);
     *     foreach ($stream->closeWriteAndReadAll() as $element) {
     *         // doSomethingWith($element);
     *     }
     *     // Alternatively:
     *     // Write requests individually, making read() calls if
     *     // required. Call closeWrite() once writes are complete, and read the
     *     // remaining responses from the server.
     *     $requests = [
     *         $request,
     *     ];
     *     $stream = $grpcServiceConfigWithRetry1Client->method1BidiStreaming();
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
     *     $grpcServiceConfigWithRetry1Client->close();
     * }
     * ```
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type int $timeoutMillis
     *           Timeout to use for this call.
     * }
     *
     * @return \Google\ApiCore\BidiStream
     *
     * @throws ApiException if the remote call fails
     *
     * @experimental
     */
    public function method1BidiStreaming(array $optionalArgs = [])
    {
        return $this->startCall('Method1BidiStreaming', Response1::class, $optionalArgs, null, Call::BIDI_STREAMING_CALL);
    }

    /**
     *
     * Sample code:
     * ```
     * $grpcServiceConfigWithRetry1Client = new GrpcServiceConfigWithRetry1Client();
     * try {
     *     $response = $grpcServiceConfigWithRetry1Client->method1CServiceLevelRetry();
     * } finally {
     *     $grpcServiceConfigWithRetry1Client->close();
     * }
     * ```
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a
     *           {@see Google\ApiCore\RetrySettings} object, or an associative array of retry
     *           settings parameters. See the documentation on
     *           {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Testing\GrpcServiceConfig\Response1
     *
     * @throws ApiException if the remote call fails
     *
     * @experimental
     */
    public function method1CServiceLevelRetry(array $optionalArgs = [])
    {
        $request = new Request1();
        return $this->startCall('Method1CServiceLevelRetry', Response1::class, $optionalArgs, $request)->wait();
    }

    /**
     *
     * Sample code:
     * ```
     * $grpcServiceConfigWithRetry1Client = new GrpcServiceConfigWithRetry1Client();
     * try {
     *     $response = $grpcServiceConfigWithRetry1Client->method1DTimeoutOnlyRetry();
     * } finally {
     *     $grpcServiceConfigWithRetry1Client->close();
     * }
     * ```
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a
     *           {@see Google\ApiCore\RetrySettings} object, or an associative array of retry
     *           settings parameters. See the documentation on
     *           {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Testing\GrpcServiceConfig\Response1
     *
     * @throws ApiException if the remote call fails
     *
     * @experimental
     */
    public function method1DTimeoutOnlyRetry(array $optionalArgs = [])
    {
        $request = new Request1();
        return $this->startCall('Method1DTimeoutOnlyRetry', Response1::class, $optionalArgs, $request)->wait();
    }

    /**
     *
     * Sample code:
     * ```
     * $grpcServiceConfigWithRetry1Client = new GrpcServiceConfigWithRetry1Client();
     * try {
     *     // Read all responses until the stream is complete
     *     $stream = $grpcServiceConfigWithRetry1Client->method1ServerStreaming();
     *     foreach ($stream->readAll() as $element) {
     *         // doSomethingWith($element);
     *     }
     * } finally {
     *     $grpcServiceConfigWithRetry1Client->close();
     * }
     * ```
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type int $timeoutMillis
     *           Timeout to use for this call.
     * }
     *
     * @return \Google\ApiCore\ServerStream
     *
     * @throws ApiException if the remote call fails
     *
     * @experimental
     */
    public function method1ServerStreaming(array $optionalArgs = [])
    {
        $request = new Request1();
        return $this->startCall('Method1ServerStreaming', Response1::class, $optionalArgs, $request, Call::SERVER_STREAMING_CALL);
    }
}
