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

// [START resumableupload_generated_ResumableUpload_CreateYouTubeVideoUpload_sync]
use Google\ApiCore\ResumableUpload\ResumableUpload;
use GuzzleHttp\Psr7\Utils;
use Testing\Resumableupload\Client\ResumableUploadClient;
use Testing\Resumableupload\CreateYouTubeVideoUploadRequest;

/**
 * A method with media_upload annotation enabled.
 *
 * This sample has been automatically generated and should be regarded as a code
 * template only. It will require modifications to work:
 *  - It may require correct/in-range values for request initialization.
 *  - It may require specifying regional endpoints when creating the service client,
 *    please see the apiEndpoint client configuration option for more details.
 */
function create_you_tube_video_upload_sample(): void
{
    // Create a client.
    $resumableUploadClient = new ResumableUploadClient();

    // Prepare the request message.
    $request = new CreateYouTubeVideoUploadRequest();

    /** @var ResumableUpload $uploader */
    $uploader = $resumableUploadClient->createYouTubeVideoUpload($request);

    $stream = Utils::streamFor(fopen('/path/to/file.txt', 'r'));
    $resumableUploadOptions = [
        'chunkSize' => 8 * 1024 * 1024 /* 8MB */,
        'progressCallback' => function (int $bytesUploaded, ResumableUpload $upload) {
            printf('Committed %d bytes to session: %s' . PHP_EOL, $bytesUploaded, $upload->getUploadUrl());
        },
    ];
    try {
        $result = $uploader->startUpload($stream, $resumableUploadOptions);
    } catch (Exception $ex) {
        // Resuming directly on the existing $uploader object after an interruption
        // in the same process: calling `startUpload()` queries the server for the
        // current byte offset and resumes transmitting remaining chunks.
        $result = $uploader->startUpload($stream, $resumableUploadOptions);
    }

    printf('Operation successful with response data: %s' . PHP_EOL, $result->serializeToJsonString());

    // Resuming across separate processes or restarts (where the original
    // $uploader object in memory is lost): the session URL obtained via
    // `$uploader->getUploadUrl()` can be persisted and loaded later.
    // $resumedUpload = $resumableUploadClient->resumeUpload(
        //     'createYouTubeVideoUpload',
        //     'https://upload.url/session123'
    // );
    // $resumedUpload->startUpload($stream);
}
// [END resumableupload_generated_ResumableUpload_CreateYouTubeVideoUpload_sync]
