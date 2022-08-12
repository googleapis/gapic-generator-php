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

// [START basic_generated_BasicOneof_AMethod_sync]
use Google\ApiCore\ApiException;
use Testing\BasicOneof\BasicOneofClient;
use Testing\BasicOneof\Request\Other;
use Testing\BasicOneof\Response;

/**
 * Test including method args with required oneofs.
 *
 * @param string $otherFirst
 * @param string $otherSecond
 * @param string $requiredOptional
 */
function a_method_sample(string $otherFirst, string $otherSecond, string $requiredOptional)
{
    $basicOneofClient = new BasicOneofClient();
    $other = (new Other())->setFirst($otherFirst)->setSecond($otherSecond);
    
    try {
        /** @var Response $response */
        $response = $basicOneofClient->aMethod($other, $requiredOptional);
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
function callSample()
{
    $otherFirst = 'first';
    $otherSecond = 'second';
    $requiredOptional = 'required_optional';
    
    a_method_sample($otherFirst, $otherSecond, $requiredOptional);
}


// [END basic_generated_BasicOneof_AMethod_sync]
