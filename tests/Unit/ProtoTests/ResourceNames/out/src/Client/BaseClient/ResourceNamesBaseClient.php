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
 * Generated by gapic-generator-php from the file
 * https://github.com/googleapis/googleapis/blob/master/tests/Unit/ProtoTests/ResourceNames/resource-names.proto
 * Updates to the above are reflected here through a refresh process.
 */

namespace Testing\ResourceNames\Client\BaseClient;

use Google\ApiCore\ApiException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\GapicClientTrait;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Testing\ResourceNames\FileLevelChildTypeRefRequest;
use Testing\ResourceNames\FileLevelTypeRefRequest;
use Testing\ResourceNames\MultiPatternRequest;
use Testing\ResourceNames\PlaceholderResponse;
use Testing\ResourceNames\SinglePatternRequest;
use Testing\ResourceNames\WildcardChildReferenceRequest;
use Testing\ResourceNames\WildcardMultiPatternRequest;
use Testing\ResourceNames\WildcardPatternRequest;
use Testing\ResourceNames\WildcardReferenceRequest;

/**
 * Service Description:
 *
 * This class provides the ability to make remote calls to the backing service through method
 * calls that map to API methods.
 *
 * Many parameters require resource names to be formatted in a particular way. To
 * assist with these names, this class includes a format method for each type of
 * name, and additionally a parseName method to extract the individual identifiers
 * contained within formatted names that are returned by the API.
 */
class ResourceNamesBaseClient
{
    use GapicClientTrait;

    /** The name of the service. */
    const SERVICE_NAME = 'testing.resourcenames.ResourceNames';

    /** The default address of the service. */
    const SERVICE_ADDRESS = 'resourcenames.example.com';

    /** The default port of the service. */
    const DEFAULT_SERVICE_PORT = 443;

    /** The name of the code generator, to be included in the agent header. */
    const CODEGEN_NAME = 'gapic';

    /** The default scopes required by the service. */
    public static $serviceScopes = [];

    private static function getClientDefaults()
    {
        return [
            'serviceName' => self::SERVICE_NAME,
            'apiEndpoint' => self::SERVICE_ADDRESS . ':' . self::DEFAULT_SERVICE_PORT,
            'clientConfig' => __DIR__ . '/../../resources/resource_names_client_config.json',
            'descriptorsConfigPath' => __DIR__ . '/../../resources/resource_names_descriptor_config.php',
            'gcpApiConfigPath' => __DIR__ . '/../../resources/resource_names_grpc_config.json',
            'credentialsConfig' => [
                'defaultScopes' => self::$serviceScopes,
            ],
            'transportConfig' => [
                'rest' => [
                    'restClientConfigPath' => __DIR__ . '/../../resources/resource_names_rest_client_config.php',
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
     *     @type string $apiEndpoint
     *           The address of the API remote host. May optionally include the port, formatted
     *           as "<uri>:<port>". Default 'resourcenames.example.com:443'.
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
     *           $apiEndpoint setting, will be ignored.
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
     *     @type callable $clientCertSource
     *           A callable which returns the client cert as a string. This can be used to
     *           provide a certificate and private key to the transport layer for mTLS.
     * }
     *
     * @throws ValidationException
     */
    public function __construct(array $options = [])
    {
        $clientOptions = $this->buildClientOptions($options);
        $this->setClientOptions($clientOptions);
    }

    /**
     * @param FileLevelChildTypeRefRequest $request      A request to house fields associated with the call.
     * @param array                        $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function fileLevelChildTypeRefMethod(FileLevelChildTypeRefRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('FileLevelChildTypeRefMethod', $request, $optionalArgs)->wait();
    }

    /**
     * @param FileLevelTypeRefRequest $request      A request to house fields associated with the call.
     * @param array                   $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function fileLevelTypeRefMethod(FileLevelTypeRefRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('FileLevelTypeRefMethod', $request, $optionalArgs)->wait();
    }

    /**
     * @param MultiPatternRequest $request      A request to house fields associated with the call.
     * @param array               $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function multiPatternMethod(MultiPatternRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('MultiPatternMethod', $request, $optionalArgs)->wait();
    }

    /**
     * @param SinglePatternRequest $request      A request to house fields associated with the call.
     * @param array                $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function singlePatternMethod(SinglePatternRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('SinglePatternMethod', $request, $optionalArgs)->wait();
    }

    /**
     * @param WildcardChildReferenceRequest $request      A request to house fields associated with the call.
     * @param array                         $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function wildcardChildReferenceMethod(WildcardChildReferenceRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('WildcardChildReferenceMethod', $request, $optionalArgs)->wait();
    }

    /**
     * @param WildcardPatternRequest $request      A request to house fields associated with the call.
     * @param array                  $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function wildcardMethod(WildcardPatternRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('WildcardMethod', $request, $optionalArgs)->wait();
    }

    /**
     * @param WildcardMultiPatternRequest $request      A request to house fields associated with the call.
     * @param array                       $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function wildcardMultiMethod(WildcardMultiPatternRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('WildcardMultiMethod', $request, $optionalArgs)->wait();
    }

    /**
     * @param WildcardReferenceRequest $request      A request to house fields associated with the call.
     * @param array                    $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PlaceholderResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function wildcardReferenceMethod(WildcardReferenceRequest $request, array $optionalArgs = []): PlaceholderResponse
    {
        return $this->startApiCall('WildcardReferenceMethod', $request, $optionalArgs)->wait();
    }
}