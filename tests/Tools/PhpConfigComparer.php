<?php
/*
 * Copyright 2021 Google LLC
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
declare(strict_types=1);

namespace Google\Generator\Tests\Tools;

class PhpConfigComparer
{
    /**
     * Compares two GAPIC configs. That is, the contents of api_service_rest_client_config.php or
     * api_service_descriptor_config.php.
     * Assumes the LHS file is from the monolith (aka original or expected), and the RHS one is from the
     * microgenerator (aka actual value).
     * @param configOne the expected key-value array of configs.
     * @param configOne the actual key-value array of configs.
     * @return bool true if the configs are equal, false otherwise.
     */
    public static function compare($configOne, $configTwo): bool
    {
        if (!array_key_exists('interfaces', $configOne)) {
            print("Mono config is not a GAPIC client config\n");
            return false;
        }
        if (!array_key_exists('interfaces', $configTwo)) {
            print("Micro config is not a GAPIC client config\n");
            return true;
        }

        // Check service names (which are array keys).
        $interfacesOne = $configOne['interfaces'];
        $interfacesTwo = $configTwo['interfaces'];
        $serviceNamesOne = array_keys($interfacesOne);
        $serviceNamesTwo = array_keys($interfacesTwo);
        // Service names not in LHS (mono).
        $servicesMissingFromOne = array_diff($serviceNamesTwo, $serviceNamesOne);
        $servicesMissingFromTwo = array_diff($serviceNamesOne, $serviceNamesTwo);
        if (!empty($servicesMissingFromOne)) {
            $diffFindings[] = "Mono missing services " . implode(",", $servicesMissingFromOne);
        }
        if (!empty($servicesMissingFromTwo)) {
            $diffFindings[] = "Micro missing services " . implode(",", $servicesMissingFromTwo);
        }

        // Check services.
        foreach (array_intersect($serviceNamesOne, $serviceNamesTwo) as $serviceName) {
            $serviceOne = $interfacesOne[$serviceName];
            $serviceTwo = $interfacesTwo[$serviceName];
            $rpcNamesOne = array_keys($serviceOne);
            $rpcNamesTwo = array_keys($serviceTwo);
            $rpcsMissingFromOne = array_diff($rpcNamesTwo, $rpcNamesOne);
            $rpcsMissingFromTwo = array_diff($rpcNamesOne, $rpcNamesTwo);
            if (!empty($rpcsMissingFromOne)) {
                $diffFindings[] = "Mono missing RPCs " . implode(",", $rpcsMissingFromOne);
            }
            if (!empty($rpcsMissingFromTwo)) {
                $diffFindings[] = "Micro missing RPCs " . implode(",", $rpcsMissingFromTwo);
            }

            // Check RPCs.
            foreach (array_intersect($rpcNamesOne, $rpcNamesTwo) as $rpcName) {
                $rpcOne = $serviceOne[$rpcName];
                $rpcTwo = $serviceTwo[$rpcName];

                // Check if the keys are the same.
                $rpcAttrNamesOne = array_keys($rpcOne);
                $rpcAttrNamesTwo = array_keys($rpcTwo);
                $attrsMissingFromOne = array_diff($rpcAttrNamesTwo, $rpcAttrNamesOne);
                $attrsMissingFromTwo = array_diff($rpcAttrNamesOne, $rpcAttrNamesTwo);
                if (!empty($attrsMissingFromOne)) {
                    $diffFindings[] = "Mono missing attributes " . implode(",", $attrsMissingFromOne);
                }
                if (!empty($attrsMissingFromTwo)) {
                    $diffFindings[] = "Micro missing attributes " . implode(",", $attrsMissingFromTwo);
                }

                // Check sub-attributes for LROs and pagination.
                // This makes it easier to discern differences in array keys.
                $commonSubattrsNames = array_intersect($rpcAttrNamesOne, $rpcAttrNamesTwo);
                if (in_array('longRunning', $commonSubattrsNames)
                  || in_array('pageStreaming', $commonSubattrsNames)) {
                    $subAttrKey = in_array('longRunning', $commonSubattrsNames) ? 'longRunning' : 'pageStreaming';
                    $subAttrNamesOne = array_keys($rpcOne[$subAttrKey]);
                    $subAttrNamesTwo = array_keys($rpcTwo[$subAttrKey]);
                    $subAttrNamesMissingFromOne = array_diff($subAttrNamesTwo, $subAttrNamesOne);
                    $subAttrNamesMissingFromTwo = array_diff($subAttrNamesOne, $subAttrNamesTwo);
                    $isMissingSubAttrs = false;
                    if (!empty($subAttrNamesMissingFromOne)) {
                        $diffFindings[] = "Mono missing $subAttrKey attributes " . implode(",", $subAttrNamesMissingFromOne);
                        $isMissingSubAttrs = true;
                    }
                    if (!empty($subAttrNamesMissingFromTwo)) {
                        $diffFindings[] = "Micro missing $subAttrKey attributes " . implode(",", $subAttrNamesMissingFromTwo);
                        $isMissingSubAttrs = true;
                    }
                    if ($isMissingSubAttrs) {
                        continue;
                    }
                }

                // Diff the entire config.
                $configOneString = implode("\n", preg_split("/({|}|,)/", json_encode($rpcOne)));
                $configTwoString = implode("\n", preg_split("/({|}|,)/", json_encode($rpcTwo)));
                $configIdentical = SourceComparer::compare($configOneString, $configTwoString);
                if (!$configIdentical) {
                    $diffFindings[] = "Diff found in RPC $rpcName";
                }
            }
        }

        if (!empty($diffFindings)) {
            print(print_r($diffFindings) . "\n\n");
            return false;
        }

        return true;
    }
}
