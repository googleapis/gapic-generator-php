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
 * Generated by gapic-generator-php from the file
 * https://github.com/googleapis/googleapis/blob/master/tests/Integration/apis/compute_small/v1/compute_small.proto
 * Updates to the above are reflected here through a refresh process.
 */

namespace Google\Cloud\Compute\V1\Enums\UsableSubnetwork;

/**
 * Purpose contains string constants that represent the names of each value in the
 * google.cloud.compute.v1.UsableSubnetwork.Purpose descriptor.
 */
class Purpose
{
    const UNDEFINED_PURPOSE = 'UNDEFINED_PURPOSE';

    const GLOBAL_MANAGED_PROXY = 'GLOBAL_MANAGED_PROXY';

    const INTERNAL_HTTPS_LOAD_BALANCER = 'INTERNAL_HTTPS_LOAD_BALANCER';

    const PEER_MIGRATION = 'PEER_MIGRATION';

    const PRIVATE = 'PRIVATE';

    const PRIVATE_NAT = 'PRIVATE_NAT';

    const PRIVATE_RFC_1918 = 'PRIVATE_RFC_1918';

    const PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';

    const REGIONAL_MANAGED_PROXY = 'REGIONAL_MANAGED_PROXY';
}
