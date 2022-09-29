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
use Testing\ResourceNames\PlaceholderResponse;
use Testing\ResourceNames\ResourceNamesClient;

/**
 *
 * @param string $formattedReqFolderName             For help formatting this field, please see {@see
 *                                                   ResourceNamesClient::folderName()}.
 * @param string $formattedReqFolderMultiName        For help formatting this field, please see {@see
 *                                                   ResourceNamesClient::folder1Name()}.
 * @param string $formattedReqFolderMultiNameHistory For help formatting this field, please see {@see
 *                                                   ResourceNamesClient::folder1Name()}.
 * @param string $formattedReqOrderTest1             For help formatting this field, please see {@see
 *                                                   ResourceNamesClient::order2Name()}.
 * @param string $formattedReqOrderTest2             For help formatting this field, please see {@see
 *                                                   ResourceNamesClient::order2Name()}.
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

    // Call the API and handle any network failures.
    try {
        /** @var PlaceholderResponse $response */
        $response = $resourceNamesClient->fileLevelChildTypeRefMethod(
            $formattedReqFolderName,
            $formattedReqFolderMultiName,
            $formattedReqFolderMultiNameHistory,
            $formattedReqOrderTest1,
            $formattedReqOrderTest2
        );
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
