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

// [START routingheaders_generated_RoutingHeaders_RoutingRuleWithOutParameters_sync]
use Google\ApiCore\ApiException;
use Testing\RoutingHeaders\Client\RoutingHeadersClient;
use Testing\RoutingHeaders\NestedRequest;
use Testing\RoutingHeaders\NestedRequest\Inner1;
use Testing\RoutingHeaders\NestedRequest\Inner1\Inner2;
use Testing\RoutingHeaders\Response;

/**
 * @param string $nest1Nest2Name
 * @param string $anotherName
 */
function routing_rule_with_out_parameters_sample(
    string $nest1Nest2Name,
    string $anotherName
): void {
    // Create a client.
    $routingHeadersClient = new RoutingHeadersClient();

    // Prepare the request message.
    $nest1Nest2 = (new Inner2())
        ->setName($nest1Nest2Name);
    $nest1 = (new Inner1())
        ->setNest2($nest1Nest2);
    $request = (new NestedRequest())
        ->setNest1($nest1)
        ->setAnotherName($anotherName);

    // Call the API and handle any network failures.
    try {
        /** @var Response $response */
        $response = $routingHeadersClient->routingRuleWithOutParameters($request);
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
    $nest1Nest2Name = '[NAME]';
    $anotherName = '[ANOTHER_NAME]';

    routing_rule_with_out_parameters_sample($nest1Nest2Name, $anotherName);
}
// [END routingheaders_generated_RoutingHeaders_RoutingRuleWithOutParameters_sync]
