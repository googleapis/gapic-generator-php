<?php
/*
 * Copyright 2023 Google LLC
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
 * https://github.com/googleapis/googleapis/blob/master/google/cloud/retail/v2alpha/product_service.proto
 * Updates to the above are reflected here through a refresh process.
 *
 * @experimental
 */

namespace Google\Cloud\Retail\V2alpha\Client\BaseClient;

use Google\ApiCore\ApiException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\GapicClientTrait;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\OperationResponse;
use Google\ApiCore\PagedListResponse;
use Google\ApiCore\ResourceHelperTrait;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Google\Cloud\Retail\V2alpha\AddFulfillmentPlacesRequest;
use Google\Cloud\Retail\V2alpha\CreateProductRequest;
use Google\Cloud\Retail\V2alpha\DeleteProductRequest;
use Google\Cloud\Retail\V2alpha\GetProductRequest;
use Google\Cloud\Retail\V2alpha\ImportProductsRequest;
use Google\Cloud\Retail\V2alpha\ListProductsRequest;
use Google\Cloud\Retail\V2alpha\Product;
use Google\Cloud\Retail\V2alpha\RemoveFulfillmentPlacesRequest;
use Google\Cloud\Retail\V2alpha\SetInventoryRequest;
use Google\Cloud\Retail\V2alpha\UpdateProductRequest;
use Google\LongRunning\Operation;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Service Description: Service for ingesting [Product][google.cloud.retail.v2alpha.Product]
 * information of the customer's website.
 *
 * This class provides the ability to make remote calls to the backing service through method
 * calls that map to API methods.
 *
 * Many parameters require resource names to be formatted in a particular way. To
 * assist with these names, this class includes a format method for each type of
 * name, and additionally a parseName method to extract the individual identifiers
 * contained within formatted names that are returned by the API.
 *
 * @experimental
 *
 * @method PromiseInterface addFulfillmentPlacesAsync(AddFulfillmentPlacesRequest $request, array $optionalArgs = [])
 * @method PromiseInterface createProductAsync(CreateProductRequest $request, array $optionalArgs = [])
 * @method PromiseInterface deleteProductAsync(DeleteProductRequest $request, array $optionalArgs = [])
 * @method PromiseInterface getProductAsync(GetProductRequest $request, array $optionalArgs = [])
 * @method PromiseInterface importProductsAsync(ImportProductsRequest $request, array $optionalArgs = [])
 * @method PromiseInterface listProductsAsync(ListProductsRequest $request, array $optionalArgs = [])
 * @method PromiseInterface removeFulfillmentPlacesAsync(RemoveFulfillmentPlacesRequest $request, array $optionalArgs = [])
 * @method PromiseInterface setInventoryAsync(SetInventoryRequest $request, array $optionalArgs = [])
 * @method PromiseInterface updateProductAsync(UpdateProductRequest $request, array $optionalArgs = [])
 */
class ProductServiceBaseClient
{
    use GapicClientTrait;
    use ResourceHelperTrait;

    /** The name of the service. */
    const SERVICE_NAME = 'google.cloud.retail.v2alpha.ProductService';

    /** The default address of the service. */
    const SERVICE_ADDRESS = 'retail.googleapis.com';

    /** The default port of the service. */
    const DEFAULT_SERVICE_PORT = 443;

    /** The name of the code generator, to be included in the agent header. */
    const CODEGEN_NAME = 'gapic';

    /** The default scopes required by the service. */
    public static $serviceScopes = [
        'https://www.googleapis.com/auth/cloud-platform',
    ];

    private $operationsClient;

    private static function getClientDefaults()
    {
        return [
            'serviceName' => self::SERVICE_NAME,
            'apiEndpoint' => self::SERVICE_ADDRESS . ':' . self::DEFAULT_SERVICE_PORT,
            'clientConfig' => __DIR__ . '/../../resources/product_service_client_config.json',
            'descriptorsConfigPath' => __DIR__ . '/../../resources/product_service_descriptor_config.php',
            'gcpApiConfigPath' => __DIR__ . '/../../resources/product_service_grpc_config.json',
            'credentialsConfig' => [
                'defaultScopes' => self::$serviceScopes,
            ],
            'transportConfig' => [
                'rest' => [
                    'restClientConfigPath' => __DIR__ . '/../../resources/product_service_rest_client_config.php',
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
     * Formats a string containing the fully-qualified path to represent a branch
     * resource.
     *
     * @param string $project
     * @param string $location
     * @param string $catalog
     * @param string $branch
     *
     * @return string The formatted branch resource.
     *
     * @experimental
     */
    public static function branchName($project, $location, $catalog, $branch)
    {
        return self::getPathTemplate('branch')->render([
            'project' => $project,
            'location' => $location,
            'catalog' => $catalog,
            'branch' => $branch,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a product
     * resource.
     *
     * @param string $project
     * @param string $location
     * @param string $catalog
     * @param string $branch
     * @param string $product
     *
     * @return string The formatted product resource.
     *
     * @experimental
     */
    public static function productName($project, $location, $catalog, $branch, $product)
    {
        return self::getPathTemplate('product')->render([
            'project' => $project,
            'location' => $location,
            'catalog' => $catalog,
            'branch' => $branch,
            'product' => $product,
        ]);
    }

    private static function registerPathTemplates()
    {
        self::loadPathTemplates(__DIR__ . '/../../resources/product_service_descriptor_config.php', self::SERVICE_NAME);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - branch: projects/{project}/locations/{location}/catalogs/{catalog}/branches/{branch}
     * - product: projects/{project}/locations/{location}/catalogs/{catalog}/branches/{branch}/products/{product}
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
     *
     * @experimental
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
     *           as "<uri>:<port>". Default 'retail.googleapis.com:443'.
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
     *
     * @experimental
     */
    public function __construct(array $options = [])
    {
        $clientOptions = $this->buildClientOptions($options);
        $this->setClientOptions($clientOptions);
        $this->operationsClient = $this->createOperationsClient($clientOptions);
    }

    public function __call($method, $args)
    {
        if (substr($method, -5) !== 'Async') {
            trigger_error('Call to undefined method ' . __CLASS__ . "::$method()", E_USER_ERROR);
        }

        array_unshift($args, substr($method, 0, -5));
        return call_user_func_array([$this, 'startAsyncCall'], $args);
    }

    /**
     * Incrementally adds place IDs to
     * [Product.fulfillment_info.place_ids][google.cloud.retail.v2alpha.FulfillmentInfo.place_ids].
     *
     * This process is asynchronous and does not require the
     * [Product][google.cloud.retail.v2alpha.Product] to exist before updating
     * fulfillment information. If the request is valid, the update will be
     * enqueued and processed downstream. As a consequence, when a response is
     * returned, the added place IDs are not immediately manifested in the
     * [Product][google.cloud.retail.v2alpha.Product] queried by
     * [GetProduct][google.cloud.retail.v2alpha.ProductService.GetProduct] or
     * [ListProducts][google.cloud.retail.v2alpha.ProductService.ListProducts].
     *
     * This feature is only available for users who have Retail Search enabled.
     * Please submit a form [here](https://cloud.google.com/contact) to contact
     * cloud sales if you are interested in using Retail Search.
     *
     * The async variant is {@see self::addFulfillmentPlacesAsync()} .
     *
     * @param AddFulfillmentPlacesRequest $request      A request to house fields associated with the call.
     * @param array                       $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return OperationResponse
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function addFulfillmentPlaces(AddFulfillmentPlacesRequest $request, array $optionalArgs = []): OperationResponse
    {
        return $this->startApiCall('AddFulfillmentPlaces', $request, $optionalArgs)->wait();
    }

    /**
     * Creates a [Product][google.cloud.retail.v2alpha.Product].
     *
     * The async variant is {@see self::createProductAsync()} .
     *
     * @param CreateProductRequest $request      A request to house fields associated with the call.
     * @param array                $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return Product
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function createProduct(CreateProductRequest $request, array $optionalArgs = []): Product
    {
        return $this->startApiCall('CreateProduct', $request, $optionalArgs)->wait();
    }

    /**
     * Deletes a [Product][google.cloud.retail.v2alpha.Product].
     *
     * The async variant is {@see self::deleteProductAsync()} .
     *
     * @param DeleteProductRequest $request      A request to house fields associated with the call.
     * @param array                $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function deleteProduct(DeleteProductRequest $request, array $optionalArgs = []): void
    {
        $this->startApiCall('DeleteProduct', $request, $optionalArgs)->wait();
    }

    /**
     * Gets a [Product][google.cloud.retail.v2alpha.Product].
     *
     * The async variant is {@see self::getProductAsync()} .
     *
     * @param GetProductRequest $request      A request to house fields associated with the call.
     * @param array             $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return Product
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function getProduct(GetProductRequest $request, array $optionalArgs = []): Product
    {
        return $this->startApiCall('GetProduct', $request, $optionalArgs)->wait();
    }

    /**
     * Bulk import of multiple [Product][google.cloud.retail.v2alpha.Product]s.
     *
     * Request processing may be synchronous. No partial updating is supported.
     * Non-existing items are created.
     *
     * Note that it is possible for a subset of the
     * [Product][google.cloud.retail.v2alpha.Product]s to be successfully updated.
     *
     * The async variant is {@see self::importProductsAsync()} .
     *
     * @param ImportProductsRequest $request      A request to house fields associated with the call.
     * @param array                 $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return OperationResponse
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function importProducts(ImportProductsRequest $request, array $optionalArgs = []): OperationResponse
    {
        return $this->startApiCall('ImportProducts', $request, $optionalArgs)->wait();
    }

    /**
     * Gets a list of [Product][google.cloud.retail.v2alpha.Product]s.
     *
     * The async variant is {@see self::listProductsAsync()} .
     *
     * @param ListProductsRequest $request      A request to house fields associated with the call.
     * @param array               $optionalArgs {
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
     *
     * @experimental
     */
    public function listProducts(ListProductsRequest $request, array $optionalArgs = []): PagedListResponse
    {
        return $this->startApiCall('ListProducts', $request, $optionalArgs);
    }

    /**
     * Incrementally removes place IDs from a
     * [Product.fulfillment_info.place_ids][google.cloud.retail.v2alpha.FulfillmentInfo.place_ids].
     *
     * This process is asynchronous and does not require the
     * [Product][google.cloud.retail.v2alpha.Product] to exist before updating
     * fulfillment information. If the request is valid, the update will be
     * enqueued and processed downstream. As a consequence, when a response is
     * returned, the removed place IDs are not immediately manifested in the
     * [Product][google.cloud.retail.v2alpha.Product] queried by
     * [GetProduct][google.cloud.retail.v2alpha.ProductService.GetProduct] or
     * [ListProducts][google.cloud.retail.v2alpha.ProductService.ListProducts].
     *
     * This feature is only available for users who have Retail Search enabled.
     * Please submit a form [here](https://cloud.google.com/contact) to contact
     * cloud sales if you are interested in using Retail Search.
     *
     * The async variant is {@see self::removeFulfillmentPlacesAsync()} .
     *
     * @param RemoveFulfillmentPlacesRequest $request      A request to house fields associated with the call.
     * @param array                          $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return OperationResponse
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function removeFulfillmentPlaces(RemoveFulfillmentPlacesRequest $request, array $optionalArgs = []): OperationResponse
    {
        return $this->startApiCall('RemoveFulfillmentPlaces', $request, $optionalArgs)->wait();
    }

    /**
     * Updates inventory information for a
     * [Product][google.cloud.retail.v2alpha.Product] while respecting the last
     * update timestamps of each inventory field.
     *
     * This process is asynchronous and does not require the
     * [Product][google.cloud.retail.v2alpha.Product] to exist before updating
     * fulfillment information. If the request is valid, the update will be
     * enqueued and processed downstream. As a consequence, when a response is
     * returned, updates are not immediately manifested in the
     * [Product][google.cloud.retail.v2alpha.Product] queried by
     * [GetProduct][google.cloud.retail.v2alpha.ProductService.GetProduct] or
     * [ListProducts][google.cloud.retail.v2alpha.ProductService.ListProducts].
     *
     * When inventory is updated with
     * [CreateProduct][google.cloud.retail.v2alpha.ProductService.CreateProduct]
     * and
     * [UpdateProduct][google.cloud.retail.v2alpha.ProductService.UpdateProduct],
     * the specified inventory field value(s) will overwrite any existing value(s)
     * while ignoring the last update time for this field. Furthermore, the last
     * update time for the specified inventory fields will be overwritten to the
     * time of the
     * [CreateProduct][google.cloud.retail.v2alpha.ProductService.CreateProduct]
     * or
     * [UpdateProduct][google.cloud.retail.v2alpha.ProductService.UpdateProduct]
     * request.
     *
     * If no inventory fields are set in
     * [CreateProductRequest.product][google.cloud.retail.v2alpha.CreateProductRequest.product],
     * then any pre-existing inventory information for this product will be used.
     *
     * If no inventory fields are set in [UpdateProductRequest.set_mask][],
     * then any existing inventory information will be preserved.
     *
     * Pre-existing inventory information can only be updated with
     * [SetInventory][google.cloud.retail.v2alpha.ProductService.SetInventory],
     * [AddFulfillmentPlaces][google.cloud.retail.v2alpha.ProductService.AddFulfillmentPlaces],
     * and
     * [RemoveFulfillmentPlaces][google.cloud.retail.v2alpha.ProductService.RemoveFulfillmentPlaces].
     *
     * This feature is only available for users who have Retail Search enabled.
     * Please submit a form [here](https://cloud.google.com/contact) to contact
     * cloud sales if you are interested in using Retail Search.
     *
     * The async variant is {@see self::setInventoryAsync()} .
     *
     * @param SetInventoryRequest $request      A request to house fields associated with the call.
     * @param array               $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return OperationResponse
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function setInventory(SetInventoryRequest $request, array $optionalArgs = []): OperationResponse
    {
        return $this->startApiCall('SetInventory', $request, $optionalArgs)->wait();
    }

    /**
     * Updates a [Product][google.cloud.retail.v2alpha.Product].
     *
     * The async variant is {@see self::updateProductAsync()} .
     *
     * @param UpdateProductRequest $request      A request to house fields associated with the call.
     * @param array                $optionalArgs {
     *     Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *           Retry settings to use for this call. Can be a {@see RetrySettings} object, or an
     *           associative array of retry settings parameters. See the documentation on
     *           {@see RetrySettings} for example usage.
     * }
     *
     * @return Product
     *
     * @throws ApiException Thrown if the API call fails.
     *
     * @experimental
     */
    public function updateProduct(UpdateProductRequest $request, array $optionalArgs = []): Product
    {
        return $this->startApiCall('UpdateProduct', $request, $optionalArgs)->wait();
    }
}