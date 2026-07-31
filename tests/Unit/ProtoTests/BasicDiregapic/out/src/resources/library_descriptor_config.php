<?php
/*
 * Copyright 2026 Google LLC
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

return [
    'interfaces' => [
        'google.example.library.v1.Library' => [
            'GetBigBook' => [
                'longRunning' => [
                    'operationReturnType' => '\Testing\BasicDiregapic\BookResponse',
                    'metadataReturnType' => '\Testing\BasicDiregapic\GetBigBookMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBigNothing' => [
                'longRunning' => [
                    'operationReturnType' => '\Google\Protobuf\GPBEmpty',
                    'metadataReturnType' => '\Testing\BasicDiregapic\GetBigBookMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'LongRunningArchiveBooks' => [
                'longRunning' => [
                    'operationReturnType' => '\Testing\BasicDiregapic\ArchiveBooksResponse',
                    'metadataReturnType' => '\Testing\BasicDiregapic\ArchiveBooksMetadata',
                    'initialPollDelayMillis' => '500',
                    'pollDelayMultiplier' => '1.5',
                    'maxPollDelayMillis' => '5000',
                    'totalPollTimeoutMillis' => '300000',
                ],
                'callType' => \Google\ApiCore\Call::LONGRUNNING_CALL,
                'headerParams' => [
                    [
                        'keyName' => 'source',
                        'fieldAccessors' => [
                            'getSource',
                        ],
                    ],
                ],
            ],
            'AddComments' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'AddTag' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\AddTagResponse',
                'headerParams' => [
                    [
                        'keyName' => 'resource',
                        'fieldAccessors' => [
                            'getResource',
                        ],
                    ],
                ],
            ],
            'ArchiveBooks' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\ArchiveBooksResponse',
                'headerParams' => [
                    [
                        'keyName' => 'source',
                        'fieldAccessors' => [
                            'getSource',
                        ],
                    ],
                ],
            ],
            'CreateBook' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'CreateInventory' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\InventoryResponse',
                'headerParams' => [
                    [
                        'keyName' => 'parent',
                        'fieldAccessors' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'CreateShelf' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\ShelfResponse',
            ],
            'DeleteBook' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'DeleteShelf' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'FindRelatedBooks' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getNames',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Testing\BasicDiregapic\FindRelatedBooksResponse',
            ],
            'GetBook' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBookFromAbsolutelyAnywhere' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookFromAnywhereResponse',
                'headerParams' => [
                    [
                        'keyName' => 'alt_book_name',
                        'fieldAccessors' => [
                            'getAltBookName',
                        ],
                    ],
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBookFromAnywhere' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookFromAnywhereResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBookFromArchive' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookFromArchiveResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetShelf' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\ShelfResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListAggregatedShelves' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getMaxResults',
                    'requestPageSizeSetMethod' => 'setMaxResults',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getShelves',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Testing\BasicDiregapic\ListAggregatedShelvesResponse',
            ],
            'ListBooks' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getBooks',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Testing\BasicDiregapic\ListBooksResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListShelves' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\ListShelvesResponse',
            ],
            'ListStrings' => [
                'pageStreaming' => [
                    'requestPageTokenGetMethod' => 'getPageToken',
                    'requestPageTokenSetMethod' => 'setPageToken',
                    'requestPageSizeGetMethod' => 'getPageSize',
                    'requestPageSizeSetMethod' => 'setPageSize',
                    'responsePageTokenGetMethod' => 'getNextPageToken',
                    'resourcesGetMethod' => 'getStrings',
                ],
                'callType' => \Google\ApiCore\Call::PAGINATED_CALL,
                'responseType' => 'Testing\BasicDiregapic\ListStringsResponse',
            ],
            'MergeShelves' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\ShelfResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'MoveBook' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'MoveBooks' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\MoveBooksResponse',
                'headerParams' => [
                    [
                        'keyName' => 'source',
                        'fieldAccessors' => [
                            'getSource',
                        ],
                    ],
                ],
            ],
            'PrivateListShelves' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookResponse',
            ],
            'PublishSeries' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\PublishSeriesResponse',
                'headerParams' => [
                    [
                        'keyName' => 'shelf.name',
                        'fieldAccessors' => [
                            'getShelf',
                            'getName',
                        ],
                    ],
                ],
            ],
            'SaveBook' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
            ],
            'UpdateBook' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\BasicDiregapic\BookResponse',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'UpdateBookIndex' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Google\Protobuf\GPBEmpty',
                'headerParams' => [
                    [
                        'keyName' => 'name',
                        'fieldAccessors' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'templateMap' => [
                'archive' => 'archives/{archive}',
                'archiveBook' => 'archives/{archive}/books/{book}',
                'archivedBook' => 'archives/{archive}/books/{book}',
                'book' => 'shelves/{shelf}/books/{book_one}~{book_two}',
                'folder' => 'folders/{folder}',
                'inventory' => 'projects/{project}/locations/{location}/publishers/{publisher}/inventory',
                'location' => 'projects/{project}/locations/{location}',
                'organizationReader' => 'organization/{organization}/reader',
                'project' => 'projects/{project}',
                'projectBook' => 'projects/{project}/books/{book}',
                'projectLocationPublisherBook' => 'projects/{project}/locations/{location}/publishers/{publisher}/inventory/books/{book}',
                'projectReader' => 'projects/{project}/readers/{reader}',
                'projectShelfReaderSurnameReaderFirstName' => 'projects/{project}/shelves/{shelf}/readers/{reader_surname}.{reader_first_name}',
                'publisher' => 'projects/{project}/locations/{location}/publishers/{publisher}',
                'reader' => 'projects/{project}/readers/{reader}',
                'shelf' => 'shelves/{shelf}',
                'shelfBookOneBookTwo' => 'shelves/{shelf}/books/{book_one}~{book_two}',
            ],
        ],
    ],
];
