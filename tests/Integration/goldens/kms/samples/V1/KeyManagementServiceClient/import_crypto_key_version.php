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

// [START cloudkms_v1_generated_KeyManagementService_ImportCryptoKeyVersion_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Kms\V1\CryptoKeyVersion;
use Google\Cloud\Kms\V1\KeyManagementServiceClient;

/**
 * Imports a new [CryptoKeyVersion][google.cloud.kms.v1.CryptoKeyVersion] into
 * an existing [CryptoKey][google.cloud.kms.v1.CryptoKey] using the wrapped
 * key material provided in the request.
 *
 * The version ID will be assigned the next sequential id within the
 * [CryptoKey][google.cloud.kms.v1.CryptoKey].
 *
 * @param string                    $formattedParent Required. The [name][google.cloud.kms.v1.CryptoKey.name] of the
 *                                                   [CryptoKey][google.cloud.kms.v1.CryptoKey] to be imported into.
 * @param CryptoKeyVersionAlgorithm $algorithm       Required. The
 *                                                   [algorithm][google.cloud.kms.v1.CryptoKeyVersion.CryptoKeyVersionAlgorithm]
 *                                                   of the key being imported. This does not need to match the
 *                                                   [version_template][google.cloud.kms.v1.CryptoKey.version_template] of the
 *                                                   [CryptoKey][google.cloud.kms.v1.CryptoKey] this version imports into.
 * @param string                    $importJob       Required. The [name][google.cloud.kms.v1.ImportJob.name] of the
 *                                                   [ImportJob][google.cloud.kms.v1.ImportJob] that was used to wrap this key
 *                                                   material.
 */
function import_crypto_key_version_sample(string $formattedParent, CryptoKeyVersionAlgorithm $algorithm, string $importJob)
{
    $keyManagementServiceClient = new KeyManagementServiceClient();
    
    try {
        /** @var CryptoKeyVersion $response */
        $response = $keyManagementServiceClient->importCryptoKeyVersion($formattedParent, $algorithm, $importJob);
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
    $formattedParent = KeyManagementServiceClient::cryptoKeyName('[PROJECT]', '[LOCATION]', '[KEY_RING]', '[CRYPTO_KEY]');
    $algorithm = CryptoKeyVersionAlgorithm::CRYPTO_KEY_VERSION_ALGORITHM_UNSPECIFIED;
    $importJob = 'import_job';
    
    import_crypto_key_version_sample($formattedParent, $algorithm, $importJob);
}


// [END cloudkms_v1_generated_KeyManagementService_ImportCryptoKeyVersion_sync]
