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

// [START bidi_generated_BasicBidiStreaming_MethodBidi_sync]
use Google\ApiCore\ApiException;
use Testing\BasicBidiStreaming\BasicBidiStreamingClient;


/**
 *
 * @param int $aNumber
 */
function method_bidi_sample(int $aNumber)
{
    $basicBidiStreamingClient = new BasicBidiStreamingClient();
    
    try {
        $request = new Request();
        $request->setANumber($aNumber);
        // Write all requests to the server, then read all responses until the
        // stream is complete
        $requests = [
            $request,
        ];
        $stream = $basicBidiStreamingClient->methodBidi();
        $stream->writeAll($requests);
        foreach ($stream->closeWriteAndReadAll() as $element) {
            printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
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
    $aNumber = 0;
    
    method_bidi_sample($aNumber);
}


// [END bidi_generated_BasicBidiStreaming_MethodBidi_sync]
