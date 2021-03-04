<?php
/*
 * Copyright 2020 Google LLC
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

namespace Google\Generator\IntegrationTests;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

// Initial integration test.
// Compare output of monolith and micro generators.
// They should match except for whitespace and trailing commas.

$ok = true;
// Generate and compare test APIs.
$ok = processDiff(Invoker::invoke('tests/ProtoTests/Basic/basic.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicLro/basic-lro.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicPaginated/basic-paginated.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicBidiStreaming/basic-bidi-streaming.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicServerStreaming/basic-server-streaming.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/BasicClientStreaming/basic-client-streaming.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/ResourceNames/resource-names.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/ProtoDocs/proto-docs.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/RoutingHeaders/routing-headers.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/Keywords/keywords.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke('tests/ProtoTests/AllTypes/all-types.proto')) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'tests/ProtoTests/GrpcServiceConfig/*.proto',
    'testing.grpcserviceconfig',
    'tests/ProtoTests/GrpcServiceConfig/grpc-service-config_gapic.yaml',
    'tests/ProtoTests/GrpcServiceConfig/grpc-service-config.yaml',
    'tests/ProtoTests/GrpcServiceConfig/grpc-service-config.json'
)) ? $ok : false;

// Generate and compare a real APIs.
// TODO: Real API tests may be more suitable as their own integration test.
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/accessapproval/v1/*.proto',
    'google.cloud.accessapproval.v1',
    'googleapis/google/cloud/accessapproval/v1/accessapproval_gapic.yaml',
    'googleapis/google/cloud/accessapproval/v1/accessapproval_v1.yaml',
    'googleapis/google/cloud/accessapproval/v1/accessapproval_grpc_service_config.json'
)) ? $ok : false;
// TODO: googleapis/google/cloud/apigateway/v1/ doesn't have a gapic_config, so monolith crashes
// $ok = processDiff(Invoker::invoke(
//     'googleapis/google/cloud/apigateway/v1/*.proto',
//     'google.cloud.apigateway.v1',
//     null,
//     'googleapis/google/cloud/apigateway/v1/apigateway_v1.yaml',
//     'googleapis/google/cloud/apigateway/v1/apigateway_grpc_service_config.json'
// )) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/asset/v1/*.proto',
    'google.cloud.asset.v1',
    'googleapis/google/cloud/asset/v1/cloudasset_gapic.yaml',
    'googleapis/google/cloud/asset/v1/cloudasset_v1.yaml',
    'googleapis/google/cloud/asset/v1/cloudasset_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/automl/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.automl.v1',
    'googleapis/google/cloud/automl/v1/automl_gapic.yaml',
    'googleapis/google/cloud/automl/v1/automl_v1.yaml',
    'googleapis/google/cloud/automl/v1/automl_grpc_service_config.json'
)) ? $ok : false;
// TODO: BigQuery
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/billing/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.billing.v1',
    'googleapis/google/cloud/billing/v1/cloud_billing_gapic.yaml',
    'googleapis/google/cloud/billing/v1/cloudbilling.yaml',
    'googleapis/google/cloud/billing/v1/cloud_billing_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/billing/budgets/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.billing.budgets.v1',
    'googleapis/google/cloud/billing/budgets/v1/billingbudget_gapic.yaml',
    'googleapis/google/cloud/billing/budgets/v1/billingbudgets.yaml',
    'googleapis/google/cloud/billing/budgets/v1/billingbudgets_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/channel/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.channel.v1',
    'googleapis/google/cloud/channel/v1/cloudchannel_gapic.yaml',
    'googleapis/google/cloud/channel/v1/cloudchannel_v1.yaml',
    'googleapis/google/cloud/channel/v1/cloudchannel_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/datacatalog/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.datacatalog.v1',
    'googleapis/google/cloud/datacatalog/v1/datacatalog_gapic.yaml',
    'googleapis/google/cloud/datacatalog/v1/datacatalog_v1.yaml',
    'googleapis/google/cloud/datacatalog/v1/datacatalog_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/dataproc/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.dataproc.v1',
    'googleapis/google/cloud/dataproc/v1/dataproc_gapic.yaml',
    'googleapis/google/cloud/dataproc/v1/dataproc_v1.yaml',
    'googleapis/google/cloud/dataproc/v1/dataproc_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/dialogflow/v2/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.dialogflow.v2',
    'googleapis/google/cloud/dialogflow/v2/dialogflow_gapic.yaml',
    'googleapis/google/cloud/dialogflow/v2/dialogflow_v2.yaml',
    'googleapis/google/cloud/dialogflow/v2/dialogflow_grpc_service_config.json'
)) ? $ok : false;
// // TODO: googleapis/google/cloud/dialogflow/cx/v3/ has wrong capitalization somewhere, so monolith crashes
// $ok = processDiff(Invoker::invoke(
//     'googleapis/google/cloud/dialogflow/cx/v3/*.proto googleapis/google/cloud/common_resources.proto',
//     'google.cloud.dialogflow.cx.v3',
//     'googleapis/google/cloud/dialogflow/cx/v3/dialogflow_gapic.yaml',
//     'googleapis/google/cloud/dialogflow/cx/v3/dialogflow_v3.yaml',
//     'googleapis/google/cloud/dialogflow/cx/v3/dialogflow_grpc_service_config.json'
// )) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/functions/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.functions.v1',
    'googleapis/google/cloud/functions/v1/functions_gapic.yaml',
    'googleapis/google/cloud/functions/v1/cloudfunctions_v1.yaml',
    'googleapis/google/cloud/functions/v1/functions_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/language/v1/language_service.proto',
    'google.cloud.language.v1',
    'googleapis/google/cloud/language/v1/language_gapic.yaml',
    'googleapis/google/cloud/language/language_v1.yaml',
    'googleapis/google/cloud/language/v1/language_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/videointelligence/v1/video_intelligence.proto',
    'google.cloud.videointelligence.v1',
    'googleapis/google/cloud/videointelligence/v1/videointelligence_gapic.yaml',
    'googleapis/google/cloud/videointelligence/v1/videointelligence_v1.yaml',
    'googleapis/google/cloud/videointelligence/v1/videointelligence_grpc_service_config.json'
)) ? $ok : false;
$ok = processDiff(Invoker::invoke(
    'googleapis/google/cloud/vision/v1/*.proto googleapis/google/cloud/common_resources.proto',
    'google.cloud.vision.v1',
    'googleapis/google/cloud/vision/v1/vision_gapic.yaml',
    'googleapis/google/cloud/vision/v1/vision_v1.yaml',
    'googleapis/google/cloud/vision/v1/vision_grpc_service_config.json'
)) ? $ok : false;

if (!$ok) {
    print("\nFail\n");
    exit(1);
} else {
    print("\nPass\n");
    exit(0);
}

function processDiff($result)
{
    $mono = $result['mono'];
    $micro = $result['micro'];
    $microProtoc = $result['micro_protoc'];

    print("------ mono <-> micro-standalone ------\n");
    $microOk = processSingleDiff($mono, $micro);
    print("------ mono <-> micro-protoc ------\n");
    $microProtocOk = processSingleDiff($mono, $microProtoc);

    return $microOk && $microProtocOk;
}

function processSingleDiff($mono, $micro)
{
    $ok = true;

    // Find missing files.
    $missing = array_diff(array_keys($mono), array_keys($micro));
    foreach ($missing as $missingPath) {
        $ignoreEnding = 'SmokeTest.php';
        if (substr($missingPath, strlen($missingPath) - strlen($ignoreEnding), strlen($ignoreEnding)) === $ignoreEnding) {
            // Ignore missing smoke-tests. The micro-generator will not be generating them.
            continue;
        }
        print("File missing from micro-generator: '{$missingPath}'\n");
        print($mono[$missingPath]);
        print("\n");
        $ok = false;
    }

    // Find excessive files.
    $excess = array_diff(array_keys($micro), array_keys($mono));
    foreach ($excess as $excessPath) {
        print("File mistakenly generated from micro-generator: '{$excessPath}'\n");
        $ok = false;
    }

    // Find incorrectly generated files.
    foreach (array_intersect(array_keys($mono), array_keys($micro)) as $path) {
        print("Comparing: '{$path}':\n");
        $isJson = substr($path, -5) === '.json';
        $sameContent = $isJson ?
            SourceComparer::compareJson($mono[$path], $micro[$path]) :
            SourceComparer::compare($mono[$path], $micro[$path]);
        $ok = $sameContent ? $ok : false;
    }

    return $ok;
}
