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
 * https://github.com/googleapis/googleapis/blob/master/google/logging/v2/logging.proto
 * Updates to the above are reflected here through a refresh process.
 */

namespace Google\Cloud\Logging\V2\Client\BaseClient;

use Google\ApiCore\ApiException;
use Google\ApiCore\BidiStream;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\GapicClientTrait;
use Google\ApiCore\PagedListResponse;
use Google\ApiCore\ResourceHelperTrait;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Google\Cloud\Logging\V2\DeleteLogRequest;
use Google\Cloud\Logging\V2\ListLogEntriesRequest;
use Google\Cloud\Logging\V2\ListLogsRequest;
use Google\Cloud\Logging\V2\ListMonitoredResourceDescriptorsRequest;
use Google\Cloud\Logging\V2\WriteLogEntriesRequest;
use Google\Cloud\Logging\V2\WriteLogEntriesResponse;

/**
 * Service Description: Service for ingesting and querying logs.
 *
 * This class provides the ability to make remote calls to the backing service through method
 * calls that map to API methods.
 *
 * Many parameters require resource names to be formatted in a particular way. To
 * assist with these names, this class includes a format method for each type of
 * name, and additionally a parseName method to extract the individual identifiers
 * contained within formatted names that are returned by the API.
 */
class LoggingServiceV2BaseClient
{
    use GapicClientTrait;
    use ResourceHelperTrait;

    /** The name of the service. */
    const SERVICE_NAME = 'google.logging.v2.LoggingServiceV2';

    /** The default address of the service. */
    const SERVICE_ADDRESS = 'logging.googleapis.com';

    /** The default port of the service. */
    const DEFAULT_SERVICE_PORT = 443;

    /** The name of the code generator, to be included in the agent header. */
    const CODEGEN_NAME = 'gapic';

    /** The default scopes required by the service. */
    public static $serviceScopes = [
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/cloud-platform.read-only',
        'https://www.googleapis.com/auth/logging.admin',
        'https://www.googleapis.com/auth/logging.read',
        'https://www.googleapis.com/auth/logging.write',
    ];

    private static function getClientDefaults()
    {
        return [
            'serviceName' => self::SERVICE_NAME,
            'apiEndpoint' => self::SERVICE_ADDRESS . ':' . self::DEFAULT_SERVICE_PORT,
            'clientConfig' => __DIR__ . '/../../resources/logging_service_v2_client_config.json',
            'descriptorsConfigPath' => __DIR__ . '/../../resources/logging_service_v2_descriptor_config.php',
            'gcpApiConfigPath' => __DIR__ . '/../../resources/logging_service_v2_grpc_config.json',
            'credentialsConfig' => [
                'defaultScopes' => self::$serviceScopes,
            ],
            'transportConfig' => [
                'rest' => [
                    'restClientConfigPath' => __DIR__ . '/../../resources/logging_service_v2_rest_client_config.php',
                ],
            ],
        ];
    }

    /**
     * Formats a string containing the fully-qualified path to represent a
     * billing_account resource.
     *
     * @param string $billingAccount
     *
     * @return string The formatted billing_account resource.
     */
    public static function billingAccountName($billingAccount)
    {
        return self::getPathTemplate('billingAccount')->render([
            'billing_account' => $billingAccount,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a
     * billing_account_log resource.
     *
     * @param string $billingAccount
     * @param string $log
     *
     * @return string The formatted billing_account_log resource.
     */
    public static function billingAccountLogName($billingAccount, $log)
    {
        return self::getPathTemplate('billingAccountLog')->render([
            'billing_account' => $billingAccount,
            'log' => $log,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a folder
     * resource.
     *
     * @param string $folder
     *
     * @return string The formatted folder resource.
     */
    public static function folderName($folder)
    {
        return self::getPathTemplate('folder')->render([
            'folder' => $folder,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a folder_log
     * resource.
     *
     * @param string $folder
     * @param string $log
     *
     * @return string The formatted folder_log resource.
     */
    public static function folderLogName($folder, $log)
    {
        return self::getPathTemplate('folderLog')->render([
            'folder' => $folder,
            'log' => $log,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a log
     * resource.
     *
     * @param string $project
     * @param string $log
     *
     * @return string The formatted log resource.
     */
    public static function logName($project, $log)
    {
        return self::getPathTemplate('log')->render([
            'project' => $project,
            'log' => $log,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a organization
     * resource.
     *
     * @param string $organization
     *
     * @return string The formatted organization resource.
     */
    public static function organizationName($organization)
    {
        return self::getPathTemplate('organization')->render([
            'organization' => $organization,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a
     * organization_log resource.
     *
     * @param string $organization
     * @param string $log
     *
     * @return string The formatted organization_log resource.
     */
    public static function organizationLogName($organization, $log)
    {
        return self::getPathTemplate('organizationLog')->render([
            'organization' => $organization,
            'log' => $log,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a project
     * resource.
     *
     * @param string $project
     *
     * @return string The formatted project resource.
     */
    public static function projectName($project)
    {
        return self::getPathTemplate('project')->render([
            'project' => $project,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a project_log
     * resource.
     *
     * @param string $project
     * @param string $log
     *
     * @return string The formatted project_log resource.
     */
    public static function projectLogName($project, $log)
    {
        return self::getPathTemplate('projectLog')->render([
            'project' => $project,
            'log' => $log,
        ]);
    }

    private static function registerPathTemplates()
    {
        self::loadPathTemplates(__DIR__ . '/../../resources/logging_service_v2_descriptor_config.php', self::SERVICE_NAME);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - billingAccount: billingAccounts/{billing_account}
     * - billingAccountLog: billingAccounts/{billing_account}/logs/{log}
     * - folder: folders/{folder}
     * - folderLog: folders/{folder}/logs/{log}
     * - log: projects/{project}/logs/{log}
     * - organization: organizations/{organization}
     * - organizationLog: organizations/{organization}/logs/{log}
     * - project: projects/{project}
     * - projectLog: projects/{project}/logs/{log}
     *
     * The optional $template argument can be supplied to specify a particular pattern,
     * and must match one of the templates listed above. If no $template argument is
     * provided, or if the $template argument does not match one of the templates
     * listed, then parseName will check each of the supported templates, and return
     * the first match.
     *
     * @param string $formattedName The formatted name string
     * @param string $template      Optional name of template to match
     *
     * @return array An associative array from name component IDs to component values.
     *
     * @throws ValidationException If $formattedName could not be matched.
     */
    public static function parseName($formattedName, $template = null)
    {
        return self::parseFormattedName($formattedName, $template);
    }

    /**
     * Constructor.
     *
     * @param array $options {
     *     Optional. Options for configuring the service API wrapper.
     *
     *     @type string $apiEndpoint
     *           The address of the API remote host. May optionally include the port, formatted
     *           as "<uri>:<port>". Default 'logging.googleapis.com:443'.
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
     * Deletes all the log entries in a log. The log reappears if it receives new
     * entries. Log entries written shortly before the delete operation might not
     * be deleted. Entries received after the delete operation with a timestamp
     * before the operation will be deleted.
     *
     * @param DeleteLogRequest $request      A request to house fields associated with the call.
     * @param array            $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function deleteLog(DeleteLogRequest $request, array $optionalArgs = []): void
    {
        $this->startApiCall('DeleteLog', $request, $optionalArgs)->wait();
    }

    /**
     * Lists log entries.  Use this method to retrieve log entries that originated
     * from a project/folder/organization/billing account.  For ways to export log
     * entries, see [Exporting
     * Logs](https://cloud.google.com/logging/docs/export).
     *
     * @param ListLogEntriesRequest $request      A request to house fields associated with the call.
     * @param array                 $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PagedListResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function listLogEntries(ListLogEntriesRequest $request, array $optionalArgs = []): PagedListResponse
    {
        return $this->startApiCall('ListLogEntries', $request, $optionalArgs);
    }

    /**
     * Lists the logs in projects, organizations, folders, or billing accounts.
     * Only logs that have entries are listed.
     *
     * @param ListLogsRequest $request      A request to house fields associated with the call.
     * @param array           $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PagedListResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function listLogs(ListLogsRequest $request, array $optionalArgs = []): PagedListResponse
    {
        return $this->startApiCall('ListLogs', $request, $optionalArgs);
    }

    /**
     * Lists the descriptors for monitored resource types used by Logging.
     *
     * @param ListMonitoredResourceDescriptorsRequest $request      A request to house fields associated with the call.
     * @param array                                   $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return PagedListResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function listMonitoredResourceDescriptors(ListMonitoredResourceDescriptorsRequest $request, array $optionalArgs = []): PagedListResponse
    {
        return $this->startApiCall('ListMonitoredResourceDescriptors', $request, $optionalArgs);
    }

    /**
     * Streaming read of log entries as they are ingested. Until the stream is
     * terminated, it will continue reading logs.
     *
     * @param array $optionalArgs {
     *     Optional.
     *
     *     @type int $timeoutMillis
     *           Timeout to use for this call.
     * }
     *
     * @return BidiStream
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function tailLogEntries(array $optionalArgs = []): BidiStream
    {
        return $this->startApiCall('TailLogEntries', null, $optionalArgs);
    }

    /**
     * Writes log entries to Logging. This API method is the
     * only way to send log entries to Logging. This method
     * is used, directly or indirectly, by the Logging agent
     * (fluentd) and all logging libraries configured to use Logging.
     * A single request may contain log entries for a maximum of 1000
     * different resources (projects, organizations, billing accounts or
     * folders)
     *
     * @param WriteLogEntriesRequest $request      A request to house fields associated with the call.
     * @param array                  $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return WriteLogEntriesResponse
     *
     * @throws ApiException Thrown if the API call fails.
     */
    public function writeLogEntries(WriteLogEntriesRequest $request, array $optionalArgs = []): WriteLogEntriesResponse
    {
        return $this->startApiCall('WriteLogEntries', $request, $optionalArgs)->wait();
    }
}