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

// [START speech_v1_generated_Adaptation_CreateCustomClass_Basic_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Speech\V1\AdaptationClient;
use Google\Cloud\Speech\V1\CustomClass;
use Google\Cloud\Speech\V1\CustomClass\ClassItem;

/**
 * Shows how to create a custom class
 *
 * @param string $formattedName The custom class parent element. Please see
 *        {@see AdaptationClient::locationName()} for help formatting this field.
 * @param string $customClassId The id for the custom class
 * @param string $classItemValue1
 * @param string $classItemValue2
 */
function create_custom_class_sample(
    string $formattedName,
    string $customClassId,
    string $classItemValue1,
    string $classItemValue2
): void {
    // Create a client.
    $adaptationClient = new AdaptationClient([
        'apiEndpoint' => 'us-speech.googleapis.com:443'
    ]);

    // Prepare any non-scalar elements to be passed along with the request.
    $items = [
        (new ClassItem())
            ->setValue($classItemValue1),
        (new ClassItem())
            ->setValue($classItemValue2)
    ];
    $customClass = (new CustomClass())
        ->setItems($items);

    // Call the API and handle any network failures.
    print('Calling the CreateCustomClass operation.' . PHP_EOL);
    try {
        /** @var CustomClass $createdCustomClass */
        $createdCustomClass = $adaptationClient->createCustomClass(
            $formattedName,
            $customClassId,
            $customClass
        );
        printf(
            'A Custom Class with the following name has been created: %s' . PHP_EOL,
            $createdCustomClass->getName()
        );
        printf(
            'The Custom class contains the following items: %s' . PHP_EOL,
            $createdCustomClass->getItems()->serializeToJsonString()
        );
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
    $formattedName = AdaptationClient::locationName('[PROJECT]', 'us');
    $customClassId = 'passengerships';
    $classItemValue1 = 'Titanic';
    $classItemValue2 = 'RMS Queen Mary';

    create_custom_class_sample($formattedName, $customClassId, $classItemValue1, $classItemValue2);
}
// [END speech_v1_generated_Adaptation_CreateCustomClass_Basic_sync]
