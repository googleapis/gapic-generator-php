<?php
/*
 * Copyright 2021 Google LLC
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
declare(strict_types=1);

namespace Google\Generator\Validation;

use Google\Validation\Intjson\Numbers;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

// Usage: php validation/IntJsonValidator.php
// Proto classes generated from intjson.proto at
// https://github.com/vchudnov-g/intjson/blob/main/proto/intjson.proto

function checkSerializedMessage($message, $label, $expectedString) {
    if ($message->serializeToJsonString() === $expectedString) {
        print("Got expected output for $label: $expectedString\n");
    } else {
        print("Output of $label does not match expected value.\n" .
            "\tExpected: $expected\n" .
            "\tGot: " . $message->serializeToJsonString() . PHP_EOL);
    }
}


$smallNumbers = new Numbers();
$smallNumbers->setLabel("small numbers")
             ->setSigned64(-10)
             ->setUnsigned64(12)
             ->setSigned32(-210)
             ->setUnsigned32(212);

$largeNumbers = new Numbers();
$largeNumbers->setLabel("large numbers")
             ->setSigned64(-1 << 60)
             ->setUnsigned64(1 << 60)
             ->setSigned32(-1 << 30)
             ->setUnsigned32(1 << 30);

$expectedSmallOutput = '{"label":"small numbers","signed64":"-10","unsigned64":"12","signed32":-210,"unsigned32":212}';
checkSerializedMessage($smallNumbers, "small numbers", $expectedSmallOutput);

$expectedLargeOutput = '{"label":"large numbers","signed64":"-1152921504606846976","unsigned64":"1152921504606846976","signed32":-1073741824,"unsigned32":1073741824}';

checkSerializedMessage($largeNumbers, "large numbers", $expectedLargeOutput);
