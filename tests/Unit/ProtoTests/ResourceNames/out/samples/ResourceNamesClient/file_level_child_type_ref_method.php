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

// [START resourcenames_generated_ResourceNames_FileLevelChildTypeRefMethod_sync]
use Google\ApiCore\ApiException;
use Testing\ResourceNames\Client\ResourceNamesClient;
use Testing\ResourceNames\FileLevelChildTypeRefRequest;
use Testing\ResourceNames\PlaceholderResponse;

/**
 * @param string $reqFolderName             Please see {@see ResourceNamesClient::folderName()} for help formatting this field.
 * @param string $reqFolderMultiName        Please see {@see ResourceNamesClient::folder1Name()} for help formatting this field.
 * @param string $reqFolderMultiNameHistory Please see {@see ResourceNamesClient::folder1Name()} for help formatting this field.
 * @param string $reqOrderTest1             Please see {@see ResourceNamesClient::order2Name()} for help formatting this field.
 * @param string $reqOrderTest2             Please see {@see ResourceNamesClient::order2Name()} for help formatting this field.
 */
function file_level_child_type_ref_method_sample(
    string $reqFolderName,
    string $reqFolderMultiName,
    string $reqFolderMultiNameHistory,
    string $reqOrderTest1,
    string $reqOrderTest2
): void {
    // Create a client.
    $resourceNamesClient = new ResourceNamesClient();

    // Prepare the request message.
    $request = (new FileLevelChildTypeRefRequest())
        ->setReqFolderName($reqFolderName)
        ->setReqFolderMultiName($reqFolderMultiName)
        ->setReqFolderMultiNameHistory($reqFolderMultiNameHistory)
        ->setReqOrderTest1($reqOrderTest1)
        ->setReqOrderTest2($reqOrderTest2);

    // Call the API and handle any network failures.
    try {
        /** @var PlaceholderResponse $response */
        $response = $resourceNamesClient->fileLevelChildTypeRefMethod($request);
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
    $reqFolderName = ResourceNamesClient::folderName('[FOLDER_ID]');
    $reqFolderMultiName = ResourceNamesClient::folder1Name('[FOLDER1_ID]');
    $reqFolderMultiNameHistory = ResourceNamesClient::folder1Name('[FOLDER1_ID]');
    $reqOrderTest1 = ResourceNamesClient::order2Name('[ORDER2_ID]');
    $reqOrderTest2 = ResourceNamesClient::order2Name('[ORDER2_ID]');

    file_level_child_type_ref_method_sample(
        $reqFolderName,
        $reqFolderMultiName,
        $reqFolderMultiNameHistory,
        $reqOrderTest1,
        $reqOrderTest2
    );
}
// [END resourcenames_generated_ResourceNames_FileLevelChildTypeRefMethod_sync]
