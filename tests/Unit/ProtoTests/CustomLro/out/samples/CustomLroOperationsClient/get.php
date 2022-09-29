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

// [START customlro_generated_CustomLroOperations_Get_sync]
use Google\ApiCore\ApiException;
use Testing\CustomLro\CustomLroOperationsClient;
use Testing\CustomLro\CustomOperationResponse;

/**
 *
 * @param string $operation Name of the Operations resource to return.
 * @param string $project   Project ID for this request.
 * @param string $region    Name of the region for this request.
 * @param string $foo       The foo from the initial request.
 */
function get_sample(string $operation, string $project, string $region, string $foo): void
{
    // Create a client.
    $customLroOperationsClient = new CustomLroOperationsClient();

    // Call the API and handle any network failures.
    try {
        /** @var CustomOperationResponse $response */
        $response = $customLroOperationsClient->get($operation, $project, $region, $foo);
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
    $operation = '[OPERATION]';
    $project = '[PROJECT]';
    $region = '[REGION]';
    $foo = '[FOO]';

    get_sample($operation, $project, $region, $foo);
}
// [END customlro_generated_CustomLroOperations_Get_sync]
