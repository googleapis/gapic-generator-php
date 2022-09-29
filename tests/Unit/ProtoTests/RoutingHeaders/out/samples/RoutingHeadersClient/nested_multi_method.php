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

// [START routingheaders_generated_RoutingHeaders_NestedMultiMethod_sync]
use Google\ApiCore\ApiException;
use Testing\RoutingHeaders\NestedRequest\Inner1;
use Testing\RoutingHeaders\NestedRequest\Inner1\Inner2;
use Testing\RoutingHeaders\Response;
use Testing\RoutingHeaders\RoutingHeadersClient;

/**
 *
 * @param string $nest1Nest2Name
 * @param string $anotherName
 */
function nested_multi_method_sample(string $nest1Nest2Name, string $anotherName): void
{
    // Create a client.
    $routingHeadersClient = new RoutingHeadersClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $nest1Nest2 = (new Inner2())
        ->setName($nest1Nest2Name);
    $nest1 = (new Inner1())
        ->setNest2($nest1Nest2);

    // Call the API and handle any network failures.
    try {
        /** @var Response $response */
        $response = $routingHeadersClient->nestedMultiMethod($nest1, $anotherName);
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
    $nest1Nest2Name = '[NAME]';
    $anotherName = '[ANOTHER_NAME]';

    nested_multi_method_sample($nest1Nest2Name, $anotherName);
}
// [END routingheaders_generated_RoutingHeaders_NestedMultiMethod_sync]
