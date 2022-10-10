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
 * FileMultiWildcard contains methods for building and parsing the
 * resourcenames.example.com/FileMultiWildcard resource names.
 */
class FileMultiWildcard
{
    private static $folder3IdFileIdNameTemplate;

    private static $pathTemplateMap;

    private static function getFolder3IdFileIdNameTemplate()
    {
        if (self::$folder3IdFileIdNameTemplate == null) {
            self::$folder3IdFileIdNameTemplate = new PathTemplate('folders3/{folder3_id}/files/{file_id}');
        }

        return self::$folder3IdFileIdNameTemplate;
    }

    private static function getPathTemplateMap()
    {
        if (self::$pathTemplateMap == null) {
            self::$pathTemplateMap = [
                'folder3IdFileId' => self::getFolder3IdFileIdNameTemplate(),
            ];
        }

        return self::$pathTemplateMap;
    }

    /**
     * Formats a string containing the fully-qualified path to represent a
     * FileMultiWildcard resource using the folder3_id, and file_id.
     *
     * @param string $folder3Id
     * @param string $fileId
     *
     * @return string The formatted FileMultiWildcard resource name.
     */
    public static function fromFolder3IdFileId($folder3Id, $fileId)
    {
        return self::getFolder3IdFileIdNameTemplate()->render([
            'folder3_id' => $folder3Id,
            'file_id' => $fileId,
        ]);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - folder3IdFileId: folders3/{folder3_id}/files/{file_id}
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