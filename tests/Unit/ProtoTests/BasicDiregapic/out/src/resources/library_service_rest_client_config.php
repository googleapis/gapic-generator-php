<?php

return [
    'interfaces' => [
        'google.example.library.v1.LibraryService' => [
            'AddComments' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=bookShelves/*}/comments',
                'body' => '*',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'AddTag' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{resource=bookShelves/*/books/*}:addTag',
                'body' => '*',
                'placeholders' => [
                    'resource' => [
                        'getters' => [
                            'getResource',
                        ],
                    ],
                ],
            ],
            'ArchiveBooks' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{source=**}:archive',
                'body' => '*',
                'placeholders' => [
                    'source' => [
                        'getters' => [
                            'getSource',
                        ],
                    ],
                ],
            ],
            'CreateBook' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=bookShelves/*}/books',
                'body' => 'book',
                'additionalBindings' => [
                    [
                        'method' => 'post',
                        'uriTemplate' => '/v1/{name=bookShelves/*}/books',
                        'body' => 'book',
                    ],
                ],
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'CreateInventory' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{parent=projects/*/locations/*/publishers/*}',
                'body' => 'inventory',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
                'queryParams' => [
                    'asset',
                    'parent_asset',
                    'assets',
                ],
            ],
            'CreateShelf' => [
                'method' => 'post',
                'uriTemplate' => '/v1/bookShelves',
                'body' => 'shelf',
            ],
            'DeleteBook' => [
                'method' => 'delete',
                'uriTemplate' => '/v1/{name=bookShelves/*/books/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'DeleteShelf' => [
                'method' => 'delete',
                'uriTemplate' => '/v1/bookShelves/{name}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'FindRelatedBooks' => [
                'method' => 'get',
                'uriTemplate' => '/v1/bookShelves',
                'queryParams' => [
                    'names',
                    'shelves',
                ],
            ],
            'GetBigBook' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=bookShelves/*/books/*}:big',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBigNothing' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=bookShelves/*/books/*}:bignothing',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBook' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=bookShelves/*/books/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBookFromAbsolutelyAnywhere' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=archives/*/books/*}',
                'additionalBindings' => [
                    [
                        'method' => 'post',
                        'uriTemplate' => '/v1/{alt_book_name=bookShelves/*/books/*}',
                    ],
                ],
                'placeholders' => [
                    'alt_book_name' => [
                        'getters' => [
                            'getAltBookName',
                        ],
                    ],
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'GetBookFromAnywhere' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=archives/*/books/**}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
                'queryParams' => [
                    'alt_book_name',
                    'place',
                    'folder',
                ],
            ],
            'GetBookFromArchive' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=archives/*/books/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
                'queryParams' => [
                    'parent',
                ],
            ],
            'GetShelf' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=bookShelves/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
                'queryParams' => [
                    'options',
                ],
            ],
            'ListAggregatedShelves' => [
                'method' => 'get',
                'uriTemplate' => '/v1/bookAggregatedShelves',
            ],
            'ListBooks' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=bookShelves/*}/books',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'ListShelves' => [
                'method' => 'get',
                'uriTemplate' => '/v1/bookShelves',
            ],
            'ListStrings' => [
                'method' => 'get',
                'uriTemplate' => '/v1/strings',
            ],
            'LongRunningArchiveBooks' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{source=**}:longrunningmove',
                'body' => '*',
                'placeholders' => [
                    'source' => [
                        'getters' => [
                            'getSource',
                        ],
                    ],
                ],
            ],
            'MergeShelves' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=bookShelves/*}:merge',
                'body' => '*',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'MoveBook' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=bookShelves/*/books/*}:move',
                'body' => '*',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'MoveBooks' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{source=**}:move',
                'body' => '*',
                'placeholders' => [
                    'source' => [
                        'getters' => [
                            'getSource',
                        ],
                    ],
                ],
            ],
            'PrivateListShelves' => [
                'method' => 'get',
                'uriTemplate' => '/v1/bookShelves',
            ],
            'PublishSeries' => [
                'method' => 'post',
                'uriTemplate' => '/v1:publish',
                'body' => '*',
                'additionalBindings' => [
                    [
                        'method' => 'post',
                        'uriTemplate' => '/v1/{shelf.name=shelves/*}:publish',
                        'body' => '*',
                    ],
                ],
                'placeholders' => [
                    'shelf.name' => [
                        'getters' => [
                            'getShelf',
                            'getName',
                        ],
                    ],
                ],
            ],
            'SaveBook' => [
                'method' => 'post',
                'uriTemplate' => '/v1:saveBook',
                'body' => '*',
            ],
            'UpdateBook' => [
                'method' => 'put',
                'uriTemplate' => '/v1/{name=bookShelves/*/books/*}',
                'body' => 'book',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
            'UpdateBookIndex' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=bookShelves/*/books/*}/index',
                'body' => '*',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'numericEnums' => true,
];
