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

require_once __DIR__ . '/../../../vendor/autoload.php';

// [START retail_v2alpha_generated_ProductService_SetInventory_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Google\Cloud\Retail\V2alpha\Product;
use Google\Cloud\Retail\V2alpha\ProductServiceClient;
use Google\Cloud\Retail\V2alpha\SetInventoryResponse;
use Google\Rpc\Status;

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
 * @param string $inventoryTitle Product title.
 *
 *                               This field must be a UTF-8 encoded string with a length limit of 1,000
 *                               characters. Otherwise, an INVALID_ARGUMENT error is returned.
 *
 *                               Google Merchant Center property
 *                               [title](https://support.google.com/merchants/answer/6324415). Schema.org
 *                               property [Product.name](https://schema.org/name).
 */
function set_inventory_sample(string $inventoryTitle): void
{
    // Create a client.
    $productServiceClient = new ProductServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $inventory = (new Product())
        ->setTitle($inventoryTitle);

    // Call the API and handle any network failures.
    try {
        /** @var OperationResponse $response */
        $response = $productServiceClient->setInventory($inventory);
        $response->pollUntilComplete();

        if ($response->operationSucceeded()) {
            /** @var SetInventoryResponse $response */
            $result = $response->getResult();
            printf(
                'Operation successful with response data: %s' . PHP_EOL,
                $result->serializeToJsonString()
            );
        } else {
            /** @var Status $error */
            $error = $response->getError();
            printf(
                'Operation failed with error data: %s' . PHP_EOL,
                $error->serializeToJsonString()
            );
        }
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}

/**
 * Helper to execute the sample.
 *
 * TODO(developer): Replace sample parameters before running the code.
 */
function callSample(): void
{
    $inventoryTitle = '[TITLE]';

    set_inventory_sample($inventoryTitle);
}
// [END retail_v2alpha_generated_ProductService_SetInventory_sync]
