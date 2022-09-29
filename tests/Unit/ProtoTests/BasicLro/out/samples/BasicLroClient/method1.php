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

// [START lro_generated_BasicLro_Method1_sync]
use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Google\Rpc\Status;
use Testing\BasicLro\BasicLroClient;
use Testing\BasicLro\LroResponse;

/**
 * To test method ordering; LRO methods referenced in gapic.yaml
 * file are always generated first; so this method will be emitted
 * before the above MethodNonLro1.
 */
function method1_sample(): void
{
    // Create a client.
    $basicLroClient = new BasicLroClient();

    // Call the API and handle any network failures.
    try {
        /** @var OperationResponse $response */
        $response = $basicLroClient->method1();
        $response->pollUntilComplete();

        if ($response->operationSucceeded()) {
            /** @var LroResponse $response */
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
// [END lro_generated_BasicLro_Method1_sync]
