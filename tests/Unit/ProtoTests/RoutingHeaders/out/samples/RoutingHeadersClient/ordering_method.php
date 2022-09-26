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

// [START routingheaders_generated_RoutingHeaders_OrderingMethod_sync]
use Google\ApiCore\ApiException;
use Testing\RoutingHeaders\Response;
use Testing\RoutingHeaders\RoutingHeadersClient;

/**
 *
 * @param string $a
 * @param string $b
 * @param string $d
 * @param string $c
 * @param string $e
 */
function ordering_method_sample(
    string $a,
    string $b,
    string $d,
    string $c,
    string $e
): void {
    // Create a client.
    $routingHeadersClient = new RoutingHeadersClient();

    // Call the API and handle any network failures.
    try {
        /** @var Response $response */
        $response = $routingHeadersClient->orderingMethod($a, $b, $d, $c, $e);
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
    $a = '[A]';
    $b = '[B]';
    $d = '[D]';
    $c = '[C]';
    $e = '[E]';

    ordering_method_sample($a, $b, $d, $c, $e);
}
// [END routingheaders_generated_RoutingHeaders_OrderingMethod_sync]
