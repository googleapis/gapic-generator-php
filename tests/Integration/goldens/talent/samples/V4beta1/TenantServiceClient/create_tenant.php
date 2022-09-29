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

// [START jobs_v4beta1_generated_TenantService_CreateTenant_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\Talent\V4beta1\Tenant;
use Google\Cloud\Talent\V4beta1\TenantServiceClient;

/**
 * Creates a new tenant entity.
 *
 * @param string $formattedParent  Resource name of the project under which the tenant is created. The format is
 *                                 "projects/{project_id}", for example, "projects/foo". For help formatting this
 *                                 field, please see {@see TenantServiceClient::projectName()}.
 * @param string $tenantExternalId Client side tenant identifier, used to uniquely identify the tenant.
 *
 *                                 The maximum number of allowed characters is 255.
 */
function create_tenant_sample(string $formattedParent, string $tenantExternalId): void
{
    // Create a client.
    $tenantServiceClient = new TenantServiceClient();

    // Prepare any non-scalar elements to be passed along with the request.
    $tenant = (new Tenant())
        ->setExternalId($tenantExternalId);

    // Call the API and handle any network failures.
    try {
        /** @var Tenant $response */
        $response = $tenantServiceClient->createTenant($formattedParent, $tenant);
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
    $formattedParent = TenantServiceClient::projectName('[PROJECT]');
    $tenantExternalId = '[EXTERNAL_ID]';

    create_tenant_sample($formattedParent, $tenantExternalId);
}
// [END jobs_v4beta1_generated_TenantService_CreateTenant_sync]
