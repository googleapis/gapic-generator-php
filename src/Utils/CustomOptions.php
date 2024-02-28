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

namespace Google\Generator\Utils;

class CustomOptions
{
    public const GOOGLE_API_DEFAULTHOST = 1049;
    public const GOOGLE_API_OAUTHSCOPES = 1050;
    public const GOOGLE_API_FIELDBEHAVIOR = 1052;
    public const GOOGLE_API_FIELDINFO = 291403980;
    public const GOOGLE_LONGRUNNING_OPERATIONINFO = 1049;
    public const GOOGLE_API_HTTP = 72295728;
    public const GOOGLE_API_RESOURCEREFERENCE = 1055;
    public const GOOGLE_API_RESOURCEDEFINITION = 1053;

    public const GOOGLE_API_FIELDBEHAVIOR_REQUIRED = 2;

    // Protobuf extension numbers from https://github.com/googleapis/googleapis/blob/master/google/cloud/extended_operations.proto.
    public const GOOGLE_CLOUD_OPERATION_FIELD = 1149;
    public const GOOGLE_CLOUD_OPERATION_REQUEST_FIELD = 1150;
    public const GOOGLE_CLOUD_OPERATION_RESPONSE_FIELD = 1151;
    public const GOOGLE_CLOUD_OPERATION_SERVICE = 1249;
    public const GOOGLE_CLOUD_OPERATION_POLLING_METHOD = 1250;

    // Protobuf extension number for https://github.com/googleapis/googleapis/blob/master/google/api/routing.proto.
    public const GOOGLE_API_ROUTING = 72295729;

    // Protobuf extension number for fhttps://github.com/googleapis/googleapis/blob/master/google/api/client.proto.
    public const GOOGLE_API_METHODSIGNATURE = 1051;
}
