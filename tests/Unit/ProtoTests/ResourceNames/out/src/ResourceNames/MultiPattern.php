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

namespace Testing\ResourceNames\ResourceNames;

use Google\ApiCore\PathTemplate;
use Google\ApiCore\ValidationException;

/**
 * MultiPattern contains methods for building and parsing the
 * resourcenames.example.com/MultiPattern resource names.
 */
class MultiPattern
{
    private static $item1IdItem2IdNameTemplate;

    private static $item3IdNameTemplate;

    private static $item4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6IdNameTemplate;

    private static $pathTemplateMap;

    private static function getItem1IdItem2IdNameTemplate()
    {
        if (self::$item1IdItem2IdNameTemplate == null) {
            self::$item1IdItem2IdNameTemplate = new PathTemplate('items1/{item1_id}/items2/{item2_id}');
        }

        return self::$item1IdItem2IdNameTemplate;
    }

    private static function getItem3IdNameTemplate()
    {
        if (self::$item3IdNameTemplate == null) {
            self::$item3IdNameTemplate = new PathTemplate('items3/{item3_id}');
        }

        return self::$item3IdNameTemplate;
    }

    private static function getItem4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6IdNameTemplate()
    {
        if (self::$item4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6IdNameTemplate == null) {
            self::$item4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6IdNameTemplate = new PathTemplate('items4/{item4_id}/items5/{item5a_id}_{item5b_id}-{item5c_id}.{item5d_id}~{item5e_id}/items6/{item6_id}');
        }

        return self::$item4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6IdNameTemplate;
    }

    private static function getPathTemplateMap()
    {
        if (self::$pathTemplateMap == null) {
            self::$pathTemplateMap = [
                'item1IdItem2Id' => self::getItem1IdItem2IdNameTemplate(),
                'item3Id' => self::getItem3IdNameTemplate(),
                'item4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6Id' => self::getItem4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6IdNameTemplate(),
            ];
        }

        return self::$pathTemplateMap;
    }

    /**
     * Formats a string containing the fully-qualified path to represent a MultiPattern
     * resource using the item1_id, and item2_id.
     *
     * @param string $item1Id
     * @param string $item2Id
     *
     * @return string The formatted MultiPattern resource name.
     */
    public static function fromItem1IdItem2Id($item1Id, $item2Id)
    {
        return self::getItem1IdItem2IdNameTemplate()->render([
            'item1_id' => $item1Id,
            'item2_id' => $item2Id,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a MultiPattern
     * resource using the item3_id.
     *
     * @param string $item3Id
     *
     * @return string The formatted MultiPattern resource name.
     */
    public static function fromItem3Id($item3Id)
    {
        return self::getItem3IdNameTemplate()->render([
            'item3_id' => $item3Id,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a MultiPattern
     * resource using the item4_id, item5a_id, item5b_id, item5c_id, item5d_id,
     * item5e_id, and item6_id.
     *
     * @param string $item4Id
     * @param string $item5aId
     * @param string $item5bId
     * @param string $item5cId
     * @param string $item5dId
     * @param string $item5eId
     * @param string $item6Id
     *
     * @return string The formatted MultiPattern resource name.
     */
    public static function fromItem4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6Id($item4Id, $item5aId, $item5bId, $item5cId, $item5dId, $item5eId, $item6Id)
    {
        return self::getItem4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6IdNameTemplate()->render([
            'item4_id' => $item4Id,
            'item5a_id' => $item5aId,
            'item5b_id' => $item5bId,
            'item5c_id' => $item5cId,
            'item5d_id' => $item5dId,
            'item5e_id' => $item5eId,
            'item6_id' => $item6Id,
        ]);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - item1IdItem2Id: items1/{item1_id}/items2/{item2_id}
     * - item3Id: items3/{item3_id}
     * - item4IdItem5aIdItem5bIdItem5cIdItem5dIdItem5eIdItem6Id: items4/{item4_id}/items5/{item5a_id}_{item5b_id}-{item5c_id}.{item5d_id}~{item5e_id}/items6/{item6_id}
     *
     * The optional $template argument can be supplied to specify a particular pattern,
     * and must match one of the templates listed above. If no $template argument is
     * provided, or if the $template argument does not match one of the templates
     * listed, then parseName will check each of the supported templates, and return
     * the first match.
     *
     * @param string $formattedName The formatted name string
     * @param string $template      Optional name of template to match
     *
     * @return array An associative array from name component IDs to component values.
     *
     * @throws ValidationException If $formattedName could not be matched.
     */
    public static function parseName($formattedName, $template = null)
    {
        $templateMap = self::getPathTemplateMap();
        if ($template) {
            if (!isset($templateMap[$template])) {
                throw new ValidationException("Template name $template does not exist");
            }

            return $templateMap[$template]->match($formattedName);
        }

        foreach ($templateMap as $templateName => $pathTemplate) {
            try {
                return $pathTemplate->match($formattedName);
            } catch (ValidationException $ex) {
                // Swallow the exception to continue trying other path templates
            }
        }

        throw new ValidationException("Input did not match any known format. Input: $formattedName");
    }
}
