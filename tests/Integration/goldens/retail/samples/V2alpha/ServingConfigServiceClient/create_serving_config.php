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

// [START retail_v2alpha_generated_ServingConfigService_CreateServingConfig_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Retail\V2alpha\ServingConfig;
use Google\Cloud\Retail\V2alpha\ServingConfigServiceClient;
use Google\Cloud\Retail\V2alpha\SolutionType;

/**
 * Creates a ServingConfig.
 *
 * A maximum of 100
 * [ServingConfig][google.cloud.retail.v2alpha.ServingConfig]s are allowed in
 * a [Catalog][google.cloud.retail.v2alpha.Catalog], otherwise a
 * FAILED_PRECONDITION error is returned.
 *
 * @param string $formattedParent                   Full resource name of parent. Format:
 *                                                  `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}`
 *                                                  Please see {@see ServingConfigServiceClient::catalogName()} for help formatting this field.
 * @param string $servingConfigDisplayName          The human readable serving config display name. Used in Retail
 *                                                  UI.
 *
 *                                                  This field must be a UTF-8 encoded string with a length limit of 128
 *                                                  characters. Otherwise, an INVALID_ARGUMENT error is returned.
 * @param int    $servingConfigSolutionTypesElement Immutable. Specifies the solution types that a serving config can
 *                                                  be associated with. Currently we support setting only one type of solution.
 * @param string $servingConfigId                   The ID to use for the ServingConfig, which will become the final
 *                                                  component of the ServingConfig's resource name.
 *
 *                                                  This value should be 4-63 characters, and valid characters
 *                                                  are /[a-z][0-9]-_/.
 */
function create_serving_config_sample(
    string $formattedParent,
    string $servingConfigDisplayName,
    int $servingConfigSolutionTypesElement,
    string $servingConfigId
): void {
    // Create a client.
    $servingConfigServiceClient = new ServingConfigServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $servingConfigSolutionTypes = [$servingConfigSolutionTypesElement,];
    $servingConfig = (new ServingConfig())
        ->setDisplayName($servingConfigDisplayName)
        ->setSolutionTypes($servingConfigSolutionTypes);

    // Call the API and handle any network failures.
    try {
        /** @var ServingConfig $response */
        $response = $servingConfigServiceClient->createServingConfig(
            $formattedParent,
            $servingConfig,
            $servingConfigId
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
    $formattedParent = ServingConfigServiceClient::catalogName('[PROJECT]', '[LOCATION]', '[CATALOG]');
    $servingConfigDisplayName = '[DISPLAY_NAME]';
    $servingConfigSolutionTypesElement = SolutionType::SOLUTION_TYPE_UNSPECIFIED;
    $servingConfigId = '[SERVING_CONFIG_ID]';

    create_serving_config_sample(
        $formattedParent,
        $servingConfigDisplayName,
        $servingConfigSolutionTypesElement,
        $servingConfigId
    );
}
// [END retail_v2alpha_generated_ServingConfigService_CreateServingConfig_sync]
