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

// [START logging_v2_generated_ConfigServiceV2_UpdateBucket_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Logging\V2\ConfigServiceV2Client;
use Google\Cloud\Logging\V2\LogBucket;
use Google\Protobuf\FieldMask;

/**
 * Updates a bucket. This method replaces the following fields in the
 * existing bucket with values from the new bucket: `retention_period`
 *
 * If the retention period is decreased and the bucket is locked,
 * FAILED_PRECONDITION will be returned.
 *
 * If the bucket has a LifecycleState of DELETE_REQUESTED, FAILED_PRECONDITION
 * will be returned.
 *
 * A buckets region may not be modified after it is created.
 *
 * @param string $formattedName The full resource name of the bucket to update.
 *                              "projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
 *                              "organizations/[ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
 *                              "billingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
 *                              "folders/[FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" Example:
 *                              `"projects/my-project-id/locations/my-location/buckets/my-bucket-id"`. Also
 *                              requires permission "resourcemanager.projects.updateLiens" to set the locked
 *                              property
 *                              For help formatting this field, please see {@see
 *                              ConfigServiceV2Client::logBucketName()}.
 */
function update_bucket_sample(string $formattedName): void
{
    // Create a client.
    $configServiceV2Client = new ConfigServiceV2Client();

    // Prepare any non-scalar elements to be passed along with the request.
    $bucket = new LogBucket();
    $updateMask = new FieldMask();

    // Call the API and handle any network failures.
    try {
        /** @var LogBucket $response */
        $response = $configServiceV2Client->updateBucket($formattedName, $bucket, $updateMask);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
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
    $formattedName = ConfigServiceV2Client::logBucketName('[PROJECT]', '[LOCATION]', '[BUCKET]');

    update_bucket_sample($formattedName);
}
// [END logging_v2_generated_ConfigServiceV2_UpdateBucket_sync]
