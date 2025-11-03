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

// [START retail_v2alpha_generated_MerchantCenterAccountLinkService_CreateMerchantCenterAccountLink_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Google\Cloud\Retail\V2alpha\MerchantCenterAccountLink;
use Google\Cloud\Retail\V2alpha\MerchantCenterAccountLinkServiceClient;
use Google\Rpc\Status;

/**
 * Creates a
 * [MerchantCenterAccountLink][google.cloud.retail.v2alpha.MerchantCenterAccountLink].
 *
 * @param string $formattedParent                                  The branch resource where this MerchantCenterAccountLink will be
 *                                                                 created. Format:
 *                                                                 `projects/{PROJECT_NUMBER}/locations/global/catalogs/{CATALOG_ID}`
 *                                                                 Please see {@see MerchantCenterAccountLinkServiceClient::catalogName()} for help formatting this field.
 * @param int    $merchantCenterAccountLinkMerchantCenterAccountId The linked [Merchant center account
 *                                                                 id](https://developers.google.com/shopping-content/guides/accountstatuses).
 *                                                                 The account must be a standalone account or a sub-account of a MCA.
 * @param string $merchantCenterAccountLinkBranchId                The branch ID (e.g. 0/1/2) within the catalog that products from
 *                                                                 merchant_center_account_id are streamed to. When updating this field, an
 *                                                                 empty value will use the currently configured default branch. However,
 *                                                                 changing the default branch later on won't change the linked branch here.
 *
 *                                                                 A single branch ID can only have one linked Merchant Center account ID.
 */
function create_merchant_center_account_link_sample(
    string $formattedParent,
    int $merchantCenterAccountLinkMerchantCenterAccountId,
    string $merchantCenterAccountLinkBranchId
): void {
    // Create a client.
    $merchantCenterAccountLinkServiceClient = new MerchantCenterAccountLinkServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $merchantCenterAccountLink = (new MerchantCenterAccountLink())
        ->setMerchantCenterAccountId($merchantCenterAccountLinkMerchantCenterAccountId)
        ->setBranchId($merchantCenterAccountLinkBranchId);

    // Call the API and handle any network failures.
    try {
        /** @var OperationResponse $response */
        $response = $merchantCenterAccountLinkServiceClient->createMerchantCenterAccountLink(
            $formattedParent,
            $merchantCenterAccountLink
        );
        $response->pollUntilComplete();

        if ($response->operationSucceeded()) {
            /** @var MerchantCenterAccountLink $result */
            $result = $response->getResult();
            printf('Operation successful with response data: %s' . PHP_EOL, $result->serializeToJsonString());
        } else {
            /** @var Status $error */
            $error = $response->getError();
            printf('Operation failed with error data: %s' . PHP_EOL, $error->serializeToJsonString());
        }
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
    $formattedParent = MerchantCenterAccountLinkServiceClient::catalogName(
        '[PROJECT]',
        '[LOCATION]',
        '[CATALOG]'
    );
    $merchantCenterAccountLinkMerchantCenterAccountId = 0;
    $merchantCenterAccountLinkBranchId = '[BRANCH_ID]';

    create_merchant_center_account_link_sample(
        $formattedParent,
        $merchantCenterAccountLinkMerchantCenterAccountId,
        $merchantCenterAccountLinkBranchId
    );
}
// [END retail_v2alpha_generated_MerchantCenterAccountLinkService_CreateMerchantCenterAccountLink_sync]
