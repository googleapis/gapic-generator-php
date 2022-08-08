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

// [START customlro_generated_CustomLro_CreateFoo_sync]
use Google\ApiCore\ApiException;
use Testing\CustomLro\CustomLroClient;


/**
 *
 * @param string $project
 * @param string $region
 */
function create_foo_sample(string $project, string $region)
{
    try {
        $customLroClient = new CustomLroClient();
        
        /** @var OperationResponse $response */
        $response = $customLroClient->createFoo($project, $region);
        $response->pollUntilComplete();
        
        if ($response->operationSucceeded()) {
            // if creating/modifying, retrieve the target resource
        } else {
            /** @var Status $error */
            $error = $response->getError();
            printf('Operation failed with data: %s' . PHP_EOL, $error->serializeToJsonString());
        }

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
    $project = 'project';
    $region = 'region';
    
    create_foo_sample($project, $region);
}


// [END customlro_generated_CustomLro_CreateFoo_sync]
