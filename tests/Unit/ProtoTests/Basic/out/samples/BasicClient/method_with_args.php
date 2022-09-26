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

// [START basic_generated_Basic_MethodWithArgs_sync]
use Google\ApiCore\ApiException;
use Testing\Basic\BasicClient;
use Testing\Basic\PartOfRequestA;
use Testing\Basic\Response;

/**
 * Test including method args.
 *
 * @param string $aString A required field...
 */
function method_with_args_sample(string $aString): void
{
    // Create a client.
    $basicClient = new BasicClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $partOfRequestA = [new PartOfRequestA()];

    // Call the API and handle any network failures.
    try {
        /** @var Response $response */
        $response = $basicClient->methodWithArgs($aString, $partOfRequestA);
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
    $aString = '[A_STRING]';

    method_with_args_sample($aString);
}
// [END basic_generated_Basic_MethodWithArgs_sync]
