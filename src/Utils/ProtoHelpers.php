<?php
/*
 * Copyright 2020 Google LLC
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

namespace Google\Generator\Utils;

use Google\Api\HttpRule;
use Google\Api\RoutingParameter;
use Google\Api\RoutingRule;
use Google\ApiCore\ResourceTemplate\Parser;
use Google\ApiCore\ResourceTemplate\Segment;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Protobuf\Internal\CodedInputStream;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FieldDescriptor;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\GPBWire;
use Google\Protobuf\Internal\Message;

class ProtoHelpers
{
    /**
     * Get the PHP namespace of the specified file.
     * This is achieved by reading the PHP namespace option if present, otherwise it uses the proto package name.
     *
     * @param FileDescriptorProto $fileDesc The file for this to get the PHP namespace.
     */
    public static function getNamespace(FileDescriptorProto $fileDesc): string
    {
        if ($fileDesc->hasOptions()) {
            $opts = $fileDesc->getOptions();
            if ($opts->hasPhpNamespace()) {
                return $opts->getPhpNamespace();
            }
        }
        // Fallback to munging the proto package.
        // DO NOT CHANGE THIS LOGIC.
        // Used by devrel_library_tracker/langs/utils.py (as of 2021/06/22).
        return Vector::new(explode('.', $fileDesc->getPackage()))
            ->map(fn ($x) => strtoupper($x[0]) . substr($x, 1))
            ->join('\\');
    }

    /**
     * Whether the specified field is a map.
     *
     * @param ProtoCatalog $catalog The proto catalog.
     * @param FieldDescriptor The field to check.
     *
     * @return bool
     */
    public static function isMap(ProtoCatalog $catalog, FieldDescriptor $desc): bool
    {
        if ($desc->getType() !== GPBType::MESSAGE) {
            return false;
        } else {
            $msg = $catalog->msgsByFullname[$desc->getMessageType()];
            return !is_null($msg->getOptions()) && $msg->getOptions()->getMapEntry();
        }
    }

    /**
     * Processes the RoutingParameters for generation and groups these processed configs by the `field`
     * declared.
     *
     * @param ProtoCatalog $catalog The proto catalog.
     * @param DescriptorProto $msg The request message.
     * @param RoutingRule $routingRule The RoutinRule annotation to process.
     *
     * @return Map A Map of `RoutingParameter.field` to all processed RoutingParameter configs related
     *             to each field.
     */
    public static function routingParameters(ProtoCatalog $catalog, ?DescriptorProto $msg, RoutingRule $routingRule): Map
    {
        return Vector::new($routingRule->getRoutingParameters())
            ->groupBy(
                // Key: The field (or field chain) referenced by the RoutingParamter.
                fn ($x) => $x->getField(),
                // Value: The routing header config the RoutingParameter defines.
                fn ($x) => [
                    'getter' => static::buildGetterChain($catalog, $msg, $x->getField()),
                    'key' => static::fieldOrTemplateVariable($x),
                    'regex' => static::compileRoutingRegex($x),
                    'root' => explode('.', $x->getField())[0],
                ],
            );
    }

    /**
     * Processes the `RoutingParameter.path_template` segment matcher into a PHP regular expression,
     * if defined.
     *
     * @param RoutingParameter $routingParam Contains the path_template to process.
     *
     * @return string The complete regular expression derived from the path_template.
     */
    private static function compileRoutingRegex(RoutingParameter $routingParam)
    {
        // No path_template in RoutingParameter.
        if (empty($routingParam->getPathTemplate())) {
            return null;
        }
        // The path_template only overrides the header key, doesn't define
        // segment matcher.
        if (!str_contains($routingParam->getPathTemplate(), '=')) {
            return null;
        }
        $template = $routingParam->getPathTemplate();

        // Capture the segment matcher from the path_template.
        // Example: /v1/{key_override=projects/*}/foos -> projects/*
        $matches = [];
        if (!preg_match('/\{.+=([^\}]*)\}/', $template, $matches)) {
            return null;
        }
        $match = $matches[1];
        // These mean "accept anything and everything", essentially the same as
        // not including a pattern following the '=' separator.
        if ($match === '*' || $match === '**') {
            return null;
        }

        $key = static::fieldOrTemplateVariable($routingParam);
        
        $original = $matches[0];
        // Replace the entire template variable with just the segment matcher,
        // wrapped in a capture group.
        $pattern = str_replace($original, "(?<".$key.">".$match.")", $template);
        // Replace double wild cards with nameless capture groups.
        $pattern = str_replace('/**', '(?:/.*)?', $pattern);
        // Replace single wild cards with single word matchers.
        $pattern = str_replace('/*', '/[^/]+', $pattern);
        // Escape all forward slashes.
        $pattern = str_replace('/', '\/', $pattern);

        return "/^".$pattern."$/";
    }

    /**
     * Determines the routing header key name for the given RoutingParameter. If the `path_template`
     * declares a key name override, that is returned instead of the `field`.
     *
     * @param RoutingParameter $routingParam The routing parameter to get a header key for.
     *
     * @return string The routing header key.
     */
    private static function fieldOrTemplateVariable(RoutingParameter $routingParam)
    {
        $key = $routingParam->getField();

        $template = $routingParam->getPathTemplate();
        $keySep = strpos($template, '=');
        if ($keySep) {
            // Start from openings "{" and read until the "=".
            // Example: {foo=bar/*/baze/*} -> foo.
            $start = strpos($template, '{') + 1;
            $key = substr($template, $start, $keySep-$start);
        } elseif (!empty($template)) {
            // Start from just beyond the opening "{" and reduce the length
            // by 2 to exclude the "{" and "}".
            // Example: {foo} -> foo.
            $key = substr($template, 1, strlen($template)-2);
        }

        return $key;
    }

    /**
     * Builds a chain of getter methods for the header key/field accessor using dot-notation.
     *
     * @param ProtoCatalog $catalog The proto catalog.
     * @param DescriptorProto $msg The request message.
     * @param string The header key/field accessor using dot-notation.
     *
     * @return Vector Vector of strings that are getter names in call order.
     */
    public static function buildGetterChain(ProtoCatalog $catalog, ?DescriptorProto $msg, string $key)
    {
        $fieldList = Vector::new(explode('.', $key));
        $result = [];
        foreach ($fieldList as $index => $fieldName) {
            if (is_null($msg)) {
                // Cannot verify field name; this occurs when processing service_config.yaml
                $result[] = Helpers::toCamelCase("get_{$fieldName}");
            } else {
                // Verify field names.
                $field = $msg->desc->getFieldByName($fieldName);
                if (is_null($field)) {
                    throw new \Exception("Field '{$fieldName}' does not exist.");
                }
                if ($index !== count($fieldList) - 1) {
                    if ($field->isRepeated()) {
                        throw new \Exception("Field '{$fieldName}' must not be repeated.");
                    }
                    if ($field->getType() !== GPBType::MESSAGE) {
                        throw new \Exception("Field '{$fieldName}' must be of message type.");
                    }
                    $msg = $catalog->msgsByFullname[$field->getMessageType()];
                }
                $result[] = $field->getGetter();
            }
        }
        return Vector::new($result);
    }

    /**
     * Generate REST placeholder getter call information from proto 'httpRule' annotation.
     *
     * @param ProtoCatalog $catalog The proto catalog.
     * @param HttpRule $httpRule The httpRule proto annotation.
     * @param ?DescriptorProto $msg Optional proto message descriptor;
     *        if present then get getter call chain is verified against the proto msg(s).
     *
     * @return Map
     */
    public static function restPlaceholders(ProtoCatalog $catalog, HttpRule $httpRule, ?DescriptorProto $msg): Map
    {
        $uriTemplateGetter = Helpers::toCamelCase("get_{$httpRule->getPattern()}");
        $restUriTemplate = $httpRule->$uriTemplateGetter();
        if ($restUriTemplate === '') {
            throw new \Exception('REST URI must be specified.');
        }
        if ($restUriTemplate[0] !== '/') {
            throw new \Exception("REST URI must be an absolute path starting with '/'");
        }
        $segments = Parser::parseSegments(str_replace(':', '/', substr($restUriTemplate, 1)));
        $placeholders = Vector::new($segments)
            ->filter(fn ($x) => $x->getSegmentType() === Segment::VARIABLE_SEGMENT)
            ->toMap(fn ($x) => $x->getKey(), fn ($x) => static::buildGetterChain($catalog, $msg, $x->getKey()));
        $nestedPlaceholders = Vector::new($httpRule->getAdditionalBindings())->map(fn ($x) => static::restPlaceholders($catalog, $x, $msg));
        return $nestedPlaceholders->append($placeholders)
            ->flatMap(fn ($x) => $x->mapValues(fn ($k, $v) => [$k, $v])->values())
            ->groupBy(fn ($x) => $x[0])
            ->mapValues(fn ($k, $v) => $v[0][1]);
    }

    // Return type is dependant on option type. Either string, int, or Vector of string or int,
    // or null if not repeated and value doesn't exist. Repeated returns empty vector if not exists.
    private static function getCustomOptionRaw(Message $message, int $optionId, bool $repeated)
    {
        static $messageUnknown;
        if (!$messageUnknown) {
            $ref = new \ReflectionClass('Google\Protobuf\Internal\Message');
            $messageUnknown = $ref->getProperty('unknown');
            $messageUnknown->setAccessible(true);
        }

        $values = [];
        if ($message->hasOptions()) {
            $opts = $message->getOptions();
            $unknown = $messageUnknown->getValue($opts);
            if ($unknown) {
                $unknownStream = new CodedInputStream($unknown);
                // Read through the stream of all custom options, looking for
                // the requested option-id. If it's repeated, then all options
                // must be parsed, otherwise return the first found.
                while (($tag = $unknownStream->readTag()) !== 0) {
                    $value = 0;
                    // TODO: Handle extra option types as required.
                    switch (GPBWire::getTagWireType($tag)) {
                        case GPBWire::WIRETYPE_VARINT:
                            $unknownStream->readVarint32($value);
                            break;
                        case GPBWire::WIRETYPE_LENGTH_DELIMITED:
                            $len = 0;
                            $unknownStream->readVarintSizeAsInt($len);
                            $unknownStream->readRaw($len, $value);
                            break;
                        default:
                            throw new \Exception('Cannot read option tag');
                    }
                    if (GPBWire::getTagFieldNumber($tag) === $optionId) {
                        if ($repeated) {
                            $values[] = $value;
                        } else {
                            return $value;
                        }
                    }
                }
            }
        }
        return $repeated ? Vector::new($values) : null;
    }

    private static function conformMessage($message): Message
    {
        if (isset($message->underlyingProto)) {
            $message = $message->underlyingProto;
        }
        if (!($message instanceof Message)) {
            throw new \Exception('Can only get custom option of Message or HasPublicDescriptorTrait');
        }
        return $message;
    }

    /**
     * Get a non-repeated custom option. The option can be of any type, which determines the return type.
     *
     * @param mixed $message The message containing the custom option. Must be of type Message, or
     *     a descriptor with an `underlyingProto` property.
     * @param int $optionId The option-id of the option to get.
     * @param ?string $msgClas Optional; to return a proto msg, set this to the PHP class of the msg.
     *
     * @return mixed Will be null if the option does not exist.
     */
    public static function getCustomOption($message, int $optionId, ?string $msgClass = null)
    {
        $result = static::getCustomOptionRaw(static::conformMessage($message), $optionId, false);
        if (!is_null($msgClass) && !is_null($result)) {
            $msg = new $msgClass();
            $msg->mergeFromString($result);
            $result = $msg;
        }
        return $result;
    }

    /**
     * Get a repeated custom option. The option can be of any type, which determines to content of the returned Vector.
     *
     * @param mixed $message The message containing the custom option. Must be of type Message, or
     *     a descriptor with an `underlyingProto` property.
     * @param int $optionId The option-id of the option to get.
     *
     * @return Vector Will be an empty Vector if the option does not exist.
     */
    public static function getCustomOptionRepeated($message, int $optionId, ?string $msgClass = null): Vector
    {
        $result = static::getCustomOptionRaw(static::conformMessage($message), $optionId, true);
        if (!is_null($msgClass)) {
            $result = $result->map(function ($x) use ($msgClass) {
                $msg = new $msgClass();
                $msg->mergeFromString($x);
                return $msg;
            });
        }
        return $result;
    }
}
