<?php
/*
 * Copyright 2025 Google LLC
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

// [START retail_v2alpha_generated_CatalogService_BatchRemoveCatalogAttributes_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Retail\V2alpha\BatchRemoveCatalogAttributesResponse;
use Google\Cloud\Retail\V2alpha\CatalogServiceClient;

/**
 * Removes all specified
 * [CatalogAttribute][google.cloud.retail.v2alpha.CatalogAttribute]s from the
 * [AttributesConfig][google.cloud.retail.v2alpha.AttributesConfig].
 *
 * @param string $formattedAttributesConfig The attributes config resource shared by all catalog attributes
 *                                          being deleted. Format:
 *                                          `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}/attributesConfig`
 *                                          Please see {@see CatalogServiceClient::attributesConfigName()} for help formatting this field.
 * @param string $attributeKeysElement      The attribute name keys of the
 *                                          [CatalogAttribute][google.cloud.retail.v2alpha.CatalogAttribute]s to
 *                                          delete. A maximum of 1000 catalog attributes can be deleted in a batch.
 */
function batch_remove_catalog_attributes_sample(
    string $formattedAttributesConfig,
    string $attributeKeysElement
): void {
    // Create a client.
    $catalogServiceClient = new CatalogServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $attributeKeys = [$attributeKeysElement,];

    // Call the API and handle any network failures.
    try {
        /** @var BatchRemoveCatalogAttributesResponse $response */
        $response = $catalogServiceClient->batchRemoveCatalogAttributes(
            $formattedAttributesConfig,
            $attributeKeys
        );
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}

/**
 * Helper to execute the sample.
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function callSample(): void
{
    $formattedAttributesConfig = CatalogServiceClient::attributesConfigName(
        '[PROJECT]',
        '[LOCATION]',
        '[CATALOG]'
    );
    $attributeKeysElement = '[ATTRIBUTE_KEYS]';

    batch_remove_catalog_attributes_sample($formattedAttributesConfig, $attributeKeysElement);
}
// [END retail_v2alpha_generated_CatalogService_BatchRemoveCatalogAttributes_sync]
