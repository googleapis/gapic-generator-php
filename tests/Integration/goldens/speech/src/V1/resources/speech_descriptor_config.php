<?php

return [
    'interfaces' => [
        'google.cloud.speech.v1.Speech' => [
            'LongRunningRecognize' => [
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'longRunning' => [
                    'operationReturnType' => '\Google\Cloud\Speech\V1\LongRunningRecognizeResponse',
                    'metadataReturnType' => '\Google\Cloud\Speech\V1\LongRunningRecognizeMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
            ],
            'Recognize' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Cloud\Speech\V1\RecognizeResponse',
            ],
            'StreamingRecognize' => [
                'callType' => \Google\ApiCore\Call::BIDI_STREAMING_CALL,
                'responseType' => 'Google\Cloud\Speech\V1\StreamingRecognizeResponse',
                'grpcStreaming' => [
                    'grpcStreamingType' => 'BidiStreaming',
                ],
            ],
        ],
    ],
];
