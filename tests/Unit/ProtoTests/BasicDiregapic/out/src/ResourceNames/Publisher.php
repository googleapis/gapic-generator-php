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

namespace Testing\BasicDiregapic\ResourceNames;

use Google\ApiCore\PathTemplate;
use Google\ApiCore\ValidationException;

/**
 * Publisher contains methods for building and parsing the
 * library.googleapis.com/Publisher resource names.
 */
class Publisher
{
    private static $projectLocationPublisherNameTemplate;

    private static $pathTemplateMap;

    private static function getProjectLocationPublisherNameTemplate()
    {
        if (self::$projectLocationPublisherNameTemplate == null) {
            self::$projectLocationPublisherNameTemplate = new PathTemplate('projects/{project}/locations/{location}/publishers/{publisher}');
        }

        return self::$projectLocationPublisherNameTemplate;
    }

    private static function getPathTemplateMap()
    {
        if (self::$pathTemplateMap == null) {
            self::$pathTemplateMap = [
                'projectLocationPublisher' => self::getProjectLocationPublisherNameTemplate(),
            ];
        }

        return self::$pathTemplateMap;
    }

    /**
     * Formats a string containing the fully-qualified path to represent a Publisher
     * resource using the project, location, and publisher.
     *
     * @param string $project
     * @param string $location
     * @param string $publisher
     *
     * @return string The formatted Publisher resource name.
     */
    public static function fromProjectLocationPublisher($project, $location, $publisher)
    {
        return self::getProjectLocationPublisherNameTemplate()->render([
            'project' => $project,
            'location' => $location,
            'publisher' => $publisher,
        ]);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - projectLocationPublisher: projects/{project}/locations/{location}/publishers/{publisher}
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
