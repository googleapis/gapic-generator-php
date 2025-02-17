<?php
/*
 * Copyright 2025 Google LLC
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

// [START container_v1_generated_ClusterManager_CreateCluster_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Container\V1\Cluster;
use Google\Cloud\Container\V1\ClusterManagerClient;
use Google\Cloud\Container\V1\Operation;

/**
 * Creates a cluster, consisting of the specified number and type of Google
 * Compute Engine instances.
 *
 * By default, the cluster is created in the project's
 * [default
 * network](https://cloud.google.com/compute/docs/networks-and-firewalls#networks).
 *
 * One firewall is added for the cluster. After cluster creation,
 * the Kubelet creates routes for each node to allow the containers
 * on that node to communicate with all other instances in the
 * cluster.
 *
 * Finally, an entry is added to the project's global metadata indicating
 * which CIDR range the cluster is using.
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function create_cluster_sample(): void
{
    // Create a client.
    $clusterManagerClient = new ClusterManagerClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $cluster = new Cluster();

    // Call the API and handle any network failures.
    try {
        /** @var Operation $response */
        $response = $clusterManagerClient->createCluster($cluster);
        printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}
// [END container_v1_generated_ClusterManager_CreateCluster_sync]
