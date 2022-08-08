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

// [START cloudkms_v1_generated_KeyManagementService_GetKeyRing_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Kms\V1\KeyManagementServiceClient;
use Google\Cloud\Kms\V1\KeyRing;

/**
 * Returns metadata for a given [KeyRing][google.cloud.kms.v1.KeyRing].
 *
 * @param string $formattedName Required. The [name][google.cloud.kms.v1.KeyRing.name] of the
 *                              [KeyRing][google.cloud.kms.v1.KeyRing] to get.
 */
function get_key_ring_sample(string $formattedName)
{
    $keyManagementServiceClient = new KeyManagementServiceClient();
    
    try {
        /** @var KeyRing $response */
        $response = $keyManagementServiceClient->getKeyRing($formattedName);
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
function callSample()
{
    $formattedName = KeyManagementServiceClient::keyRingName('[PROJECT]', '[LOCATION]', '[KEY_RING]');
    
    get_key_ring_sample($formattedName);
}


// [END cloudkms_v1_generated_KeyManagementService_GetKeyRing_sync]
