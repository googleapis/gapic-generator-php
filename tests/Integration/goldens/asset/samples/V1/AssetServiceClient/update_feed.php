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

require_once __DIR__ . '../../../vendor/autoload.php';

// [START cloudasset_v1_generated_AssetService_UpdateFeed_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Asset\V1\AssetServiceClient;
use Google\Cloud\Asset\V1\Feed;
use Google\Protobuf\FieldMask;

/** Updates an asset feed configuration. */
function update_feed_sample()
{
    $assetServiceClient = new AssetServiceClient();
    $feed = new Feed([
        'name' => 'name',
        'feed_output_config' => 'feed_output_config',
    ]);
    $updateMask = new FieldMask();
    
    try {
        /** @var Feed $response */
        $response = $assetServiceClient->updateFeed($feed, $updateMask);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s', $ex->getMessage());
    }
}


// [END cloudasset_v1_generated_AssetService_UpdateFeed_sync]
