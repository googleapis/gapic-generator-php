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

// [START cloudkms_v1_generated_KeyManagementService_GetCryptoKeyVersion_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Kms\V1\CryptoKeyVersion;
use Google\Cloud\Kms\V1\KeyManagementServiceClient;

/**
 * Returns metadata for a given
 * [CryptoKeyVersion][google.cloud.kms.v1.CryptoKeyVersion].
 *
 * @param string $formattedName Required. The [name][google.cloud.kms.v1.CryptoKeyVersion.name] of the
 *                              [CryptoKeyVersion][google.cloud.kms.v1.CryptoKeyVersion] to get.
 */
function get_crypto_key_version_sample(string $formattedName)
{
    $keyManagementServiceClient = new KeyManagementServiceClient();
    
    try {
        /** @var CryptoKeyVersion $response */
        $response = $keyManagementServiceClient->getCryptoKeyVersion($formattedName);
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
    $formattedName = KeyManagementServiceClient::cryptoKeyVersionName('[PROJECT]', '[LOCATION]', '[KEY_RING]', '[CRYPTO_KEY]', '[CRYPTO_KEY_VERSION]');
    
    get_crypto_key_version_sample($formattedName);
}


// [END cloudkms_v1_generated_KeyManagementService_GetCryptoKeyVersion_sync]
