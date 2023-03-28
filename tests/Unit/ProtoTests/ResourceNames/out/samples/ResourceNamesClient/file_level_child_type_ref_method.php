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
use Testing\ResourceNames\FileLevelChildTypeRefRequest;
use Testing\ResourceNames\PlaceholderResponse;
use Testing\ResourceNames\ResourceNamesClient;

/**
 * @param string $formattedReqFolderName             Please see {@see ResourceNamesClient::folderName()} for help formatting this field.
 * @param string $formattedReqFolderMultiName        Please see {@see ResourceNamesClient::folder1Name()} for help formatting this field.
 * @param string $formattedReqFolderMultiNameHistory Please see {@see ResourceNamesClient::folder1Name()} for help formatting this field.
 * @param string $formattedReqOrderTest1             Please see {@see ResourceNamesClient::order2Name()} for help formatting this field.
 * @param string $formattedReqOrderTest2             Please see {@see ResourceNamesClient::order2Name()} for help formatting this field.
 */
function file_level_child_type_ref_method_sample(
    string $formattedReqFolderName,
    string $formattedReqFolderMultiName,
    string $formattedReqFolderMultiNameHistory,
    string $formattedReqOrderTest1,
    string $formattedReqOrderTest2
): void {
    // Create a client.
    $resourceNamesClient = new ResourceNamesClient();

    // Prepare the request message.
    $request = (new FileLevelChildTypeRefRequest())
        ->setReqFolderName($formattedReqFolderName)
        ->setReqFolderMultiName($formattedReqFolderMultiName)
        ->setReqFolderMultiNameHistory($formattedReqFolderMultiNameHistory)
        ->setReqOrderTest1($formattedReqOrderTest1)
        ->setReqOrderTest2($formattedReqOrderTest2);

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
    $formattedReqFolderName = ResourceNamesClient::folderName('[FOLDER_ID]');
    $formattedReqFolderMultiName = ResourceNamesClient::folder1Name('[FOLDER1_ID]');
    $formattedReqFolderMultiNameHistory = ResourceNamesClient::folder1Name('[FOLDER1_ID]');
    $formattedReqOrderTest1 = ResourceNamesClient::order2Name('[ORDER2_ID]');
    $formattedReqOrderTest2 = ResourceNamesClient::order2Name('[ORDER2_ID]');

    file_level_child_type_ref_method_sample(
        $formattedReqFolderName,
        $formattedReqFolderMultiName,
        $formattedReqFolderMultiNameHistory,
        $formattedReqOrderTest1,
        $formattedReqOrderTest2
    );
}
// [END resourcenames_generated_ResourceNames_FileLevelChildTypeRefMethod_sync]
