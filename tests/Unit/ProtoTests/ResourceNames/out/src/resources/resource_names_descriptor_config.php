<?php

return [
    'interfaces' => [
        'testing.resourcenames.ResourceNames' => [
            'FileLevelChildTypeRefMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'FileLevelTypeRefMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'MultiPatternMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'NestedReferenceMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'SinglePatternMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'WildcardChildReferenceMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'WildcardMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'WildcardMultiMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'WildcardReferenceMethod' => [
                'callType' => \Google\ApiCore\Call::UNARY_CALL,
                'responseType' => 'Testing\ResourceNames\PlaceholderResponse',
            ],
            'templateMap' => [
                'deeplyNested' => 'foos/{foo}',
                'fileResDef' => 'items1/{item1_id}',
                'folder' => 'folders/{folder_id}',
                'folder1' => 'folders1/{folder1_id}',
                'folder2' => 'folders2/{folder2_id}',
                'item1Id' => 'items1/{item1_id}',
                'item1IdItem2Id' => 'items1/{item1_id}/items2/{item2_id}',
                'item2Id' => 'items2/{item2_id}',
                'item3Id' => 'items3/{item3_id}',
                'item4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6Id' => 'items4/{item4_id}/items5/{item5a_id}_{item5b_id}-{item5c_id}.{item5d_id}~{item5e_id}/items6/{item6_id}',
                'multiPattern' => 'items1/{item1_id}/items2/{item2_id}',
                'nestedReferenceMessage' => 'nestedReferenceMessages/{nested_reference_message}',
                'order1' => 'orders1/{order1_id}',
                'order2' => 'orders2/{order2_id}',
                'order3' => 'orders3/{order3_id}',
                'otherReferenceResource' => 'otherReferenceResource/{other_reference_resource}',
                'singlePattern' => 'items1/{item1_id}/items2/{item2_id}',
                'wildcardMultiPattern' => 'items1/{item1_id}',
            ],
        ],
    ],
];
