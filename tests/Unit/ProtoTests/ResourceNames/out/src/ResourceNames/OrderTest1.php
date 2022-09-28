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
 * OrderTest1 contains methods for building and parsing the
 * resourcenames.example.com/OrderTest1 resource names.
 */
class OrderTest1
{
    private static $order1IdItemIdNameTemplate;

    private static $order2IdItemIdNameTemplate;

    private static $pathTemplateMap;

    private static function getOrder1IdItemIdNameTemplate()
    {
        if (self::$order1IdItemIdNameTemplate == null) {
            self::$order1IdItemIdNameTemplate = new PathTemplate('orders1/{order1_id}/items/{item_id}');
        }

        return self::$order1IdItemIdNameTemplate;
    }

    private static function getOrder2IdItemIdNameTemplate()
    {
        if (self::$order2IdItemIdNameTemplate == null) {
            self::$order2IdItemIdNameTemplate = new PathTemplate('orders2/{order2_id}/items/{item_id}');
        }

        return self::$order2IdItemIdNameTemplate;
    }

    private static function getPathTemplateMap()
    {
        if (self::$pathTemplateMap == null) {
            self::$pathTemplateMap = [
                'order1IdItemId' => self::getOrder1IdItemIdNameTemplate(),
                'order2IdItemId' => self::getOrder2IdItemIdNameTemplate(),
            ];
        }

        return self::$pathTemplateMap;
    }

    /**
     * Formats a string containing the fully-qualified path to represent a OrderTest1
     * resource using the order1_id, and item_id.
     *
     * @param string $order1Id
     * @param string $itemId
     *
     * @return string The formatted OrderTest1 resource name.
     */
    public static function fromOrder1IdItemId($order1Id, $itemId)
    {
        return self::getOrder1IdItemIdNameTemplate()->render([
            'order1_id' => $order1Id,
            'item_id' => $itemId,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a OrderTest1
     * resource using the order2_id, and item_id.
     *
     * @param string $order2Id
     * @param string $itemId
     *
     * @return string The formatted OrderTest1 resource name.
     */
    public static function fromOrder2IdItemId($order2Id, $itemId)
    {
        return self::getOrder2IdItemIdNameTemplate()->render([
            'order2_id' => $order2Id,
            'item_id' => $itemId,
        ]);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - order1IdItemId: orders1/{order1_id}/items/{item_id}
     * - order2IdItemId: orders2/{order2_id}/items/{item_id}
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
