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

// [START cloudkms_v1_generated_KeyManagementService_Decrypt_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Kms\V1\DecryptResponse;
use Google\Cloud\Kms\V1\KeyManagementServiceClient;

/**
 * Decrypts data that was protected by
 * [Encrypt][google.cloud.kms.v1.KeyManagementService.Encrypt]. The
 * [CryptoKey.purpose][google.cloud.kms.v1.CryptoKey.purpose] must be
 * [ENCRYPT_DECRYPT][google.cloud.kms.v1.CryptoKey.CryptoKeyPurpose.ENCRYPT_DECRYPT].
 *
 * @param string $formattedName Required. The resource name of the
 *                              [CryptoKey][google.cloud.kms.v1.CryptoKey] to use for decryption. The
 *                              server will choose the appropriate version.
 * @param string $ciphertext    Required. The encrypted data originally returned in
 *                              [EncryptResponse.ciphertext][google.cloud.kms.v1.EncryptResponse.ciphertext].
 */
function decrypt_sample(string $formattedName, string $ciphertext)
{
    $keyManagementServiceClient = new KeyManagementServiceClient();
    
    try {
        /** @var DecryptResponse $response */
        $response = $keyManagementServiceClient->decrypt($formattedName, $ciphertext);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s', $ex->getMessage());
    }
}

/**
 * Helper to execute the sample.
 *
 * TODO(developer): Replace sample parameters before running the code.
 */
function callSample()
{
    $formattedName = KeyManagementServiceClient::cryptoKeyName('[PROJECT]', '[LOCATION]', '[KEY_RING]', '[CRYPTO_KEY]');
    $ciphertext = '';
    
    decrypt_sample($formattedName, $ciphertext);
}


// [END cloudkms_v1_generated_KeyManagementService_Decrypt_sync]
