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

use Google\Showcase\V1beta1\ComplianceData;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

// Usage: php validation/DefaultValuesValidator.php

$data = new ComplianceData();
$data->setFString("fstring no presence")
     ->setPString("P_string optional")
     ->setPBool(True);
$dataJson = $data->serializeToJsonString();

if ($dataJson === '{"fString":"fstring no presence","pString":"P_string optional","pBool":true}') {
  print("Test passed" . PHP_EOL);
} else {
  print("Test failed" . PHP_EOL);
}
print("JSON 1: " . $dataJson . PHP_EOL);

$data->setFString("fstring no presence")
     ->setPString("")
     ->setPInt32(0)
     ->setPBool(False);
$dataJson = $data->serializeToJsonString();
if ($dataJson === '{"fString":"fstring no presence","pString":"","pInt32":0,"pBool":false}') {
  print("Test 2 passed" . PHP_EOL);
} else {
  print("Test 2 failed" . PHP_EOL);
}
print("JSON 2: " . $dataJson . PHP_EOL);


