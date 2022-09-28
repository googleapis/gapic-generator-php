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
 * Reader contains methods for building and parsing the
 * library.googleapis.com/Reader resource names.
 */
class Reader
{
    private static $organizationReaderNameTemplate;

    private static $projectReaderNameTemplate;

    private static $projectShelfReaderSurnameReaderFirstNameNameTemplate;

    private static $pathTemplateMap;

    private static function getOrganizationReaderNameTemplate()
    {
        if (self::$organizationReaderNameTemplate == null) {
            self::$organizationReaderNameTemplate = new PathTemplate('organization/{organization}/reader');
        }

        return self::$organizationReaderNameTemplate;
    }

    private static function getProjectReaderNameTemplate()
    {
        if (self::$projectReaderNameTemplate == null) {
            self::$projectReaderNameTemplate = new PathTemplate('projects/{project}/readers/{reader}');
        }

        return self::$projectReaderNameTemplate;
    }

    private static function getProjectShelfReaderSurnameReaderFirstNameNameTemplate()
    {
        if (self::$projectShelfReaderSurnameReaderFirstNameNameTemplate == null) {
            self::$projectShelfReaderSurnameReaderFirstNameNameTemplate = new PathTemplate('projects/{project}/shelves/{shelf}/readers/{reader_surname}.{reader_first_name}');
        }

        return self::$projectShelfReaderSurnameReaderFirstNameNameTemplate;
    }

    private static function getPathTemplateMap()
    {
        if (self::$pathTemplateMap == null) {
            self::$pathTemplateMap = [
                'organizationReader' => self::getOrganizationReaderNameTemplate(),
                'projectReader' => self::getProjectReaderNameTemplate(),
                'projectShelfReaderSurnameReaderFirstName' => self::getProjectShelfReaderSurnameReaderFirstNameNameTemplate(),
            ];
        }

        return self::$pathTemplateMap;
    }

    /**
     * Formats a string containing the fully-qualified path to represent a Reader
     * resource using the organization, and reader.
     *
     * @param string $organization
     *
     * @return string The formatted Reader resource name.
     */
    public static function fromOrganizationReader($organization)
    {
        return self::getOrganizationReaderNameTemplate()->render([
            'organization' => $organization,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a Reader
     * resource using the project, and reader.
     *
     * @param string $project
     * @param string $reader
     *
     * @return string The formatted Reader resource name.
     */
    public static function fromProjectReader($project, $reader)
    {
        return self::getProjectReaderNameTemplate()->render([
            'project' => $project,
            'reader' => $reader,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent a Reader
     * resource using the project, shelf, reader_surname, and reader_first_name.
     *
     * @param string $project
     * @param string $shelf
     * @param string $readerSurname
     * @param string $readerFirstName
     *
     * @return string The formatted Reader resource name.
     */
    public static function fromProjectShelfReaderSurnameReaderFirstName($project, $shelf, $readerSurname, $readerFirstName)
    {
        return self::getProjectShelfReaderSurnameReaderFirstNameNameTemplate()->render([
            'project' => $project,
            'shelf' => $shelf,
            'reader_surname' => $readerSurname,
            'reader_first_name' => $readerFirstName,
        ]);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - organizationReader: organization/{organization}/reader
     * - projectReader: projects/{project}/readers/{reader}
     * - projectShelfReaderSurnameReaderFirstName: projects/{project}/shelves/{shelf}/readers/{reader_surname}.{reader_first_name}
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
