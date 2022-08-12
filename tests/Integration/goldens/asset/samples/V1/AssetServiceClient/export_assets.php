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

// [START cloudasset_v1_generated_AssetService_ExportAssets_sync]
use Google\Cloud\Asset\V1\AssetServiceClient;

use Google\LongRunning\Operation;


$assetServiceClient = new AssetServiceClient();
$parent = 'parent';
$operationResponse = $assetServiceClient->exportAssets($parent, $outputConfig);
$operationResponse->pollUntilComplete();
if ($operationResponse->operationSucceeded()) {
    $result = $operationResponse->getResult();
// doSomethingWith($result)
} else {
    $error = $operationResponse->getError();
    // handleError($error)
}

// Alternatively:
// start the operation, keep the operation name, and resume later
$operationResponse = $assetServiceClient->exportAssets($parent, $outputConfig);
$operationName = $operationResponse->getName();
// ... do other work
$newOperationResponse = $assetServiceClient->resumeOperation($operationName, 'exportAssets');
while (!$newOperationResponse->isDone()) {
    // ... do other work
    $newOperationResponse->reload();
}

if ($newOperationResponse->operationSucceeded()) {
    $result = $newOperationResponse->getResult();
// doSomethingWith($result)
} else {
    $error = $newOperationResponse->getError();
    // handleError($error)
}


// [END cloudasset_v1_generated_AssetService_ExportAssets_sync]
