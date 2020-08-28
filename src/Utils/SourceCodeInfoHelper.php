<?php declare(strict_types=1);

namespace Google\Generator\Utils;

use \Google\Protobuf\Internal\FileDescriptorProto;
use \Google\Protobuf\Internal\DescriptorProto;
use \Google\Generator\Collections\Vector;

class SourceCodeInfoHelper
{
    // Constants taken from:
    // https://github.com/protocolbuffers/protobuf/blob/master/src/google/protobuf/descriptor.proto
    private const MESSAGE = 4;
    private const MESSAGE_FIELD = 2;
    private const MESSAGE_MESSAGE = 3;
    private const MESSAGE_ENUM = 4;
    private const SERVICE = 6;
    private const SERVICE_METHOD = 2;

    /**
     * Recursively merge source-code comments into proto descriptors.
     * This is required because PHP doesn't currently reflect over comments.
     * 
     * All descriptors heva a `leadingComments` property added which contains
     * a Vector of strings containing the proto comment lines.
     * 
     * @param FileDescriptorProto $fileDesc Top-level file descriptor.
     */
    public static function Merge(FileDescriptorProto $fileDesc): void
    {
        $fnLeadingComments = fn($x) => $x->getLeadingComments();

        $sci = $fileDesc->GetSourceCodeInfo();
        $locsByPath = Vector::New($sci->getLocation())
            ->groupBy(fn($x) => Vector::New($x->getPath()));

        // Services
        foreach ($fileDesc->getService() as $serviceIndex => $service) {
            $servicePath = Vector::New([static::SERVICE, $serviceIndex]);
            $serviceLocations = $locsByPath[$servicePath];
            $service->leadingComments = static::getComments($serviceLocations, $fnLeadingComments);
            foreach ($service->getMethod() as $methodIndex => $method) {
                $methodPath = $servicePath->concat(Vector::New([static::SERVICE_METHOD, $methodIndex]));
                $methodLocations = $locsByPath[$methodPath];
                $method->leadingComments = static::getComments($methodLocations, $fnLeadingComments);
            }
        }

        // Messages
        $mergeMessage = null;
        $mergeMessage = function(Vector $messagePath, DescriptorProto $message) use(&$mergeMessage, $locsByPath, $fnLeadingComments)
        {
            $messageLocations = $locsByPath[$messagePath];
            $message->leadingComments = static::getComments($messageLocations, $fnLeadingComments);
            foreach ($message->getField() as $fieldIndex => $field) {
                $fieldPath = $messagePath->concat(Vector::New([static::MESSAGE_FIELD, $fieldIndex]));
                $fieldLocations = $locsByPath[$fieldPath];
                $field->leadingComments = static::getComments($fieldLocations, $fnLeadingComments);
            }
            foreach ($message->getNestedType() as $nestedMessageIndex => $nestedMessage) {
                $nestedMessagePath = $messagePath->concat(Vector::New([static::MESSAGE_MESSAGE, $nestedMessageIndex]));
                $mergeMessage($nestedMessagePath, $nestedMessage);
            }
            // TODO: enums
        };
        foreach ($fileDesc->getMessageType() as $messageIndex => $message) {
            $mergeMessage(Vector::New([static::MESSAGE, $messageIndex]), $message);
        }

        // TODO: enums
    }

    private static function getComments(Vector $locations, Callable $fn): Vector
    {
        return $locations
            ->flatMap(fn($x) => Vector::New(explode("\n", $fn($x))))
            ->map(fn($x) => trim($x))
            ->skipLast(1); // Last line is always empty due to trailing \n
    }
}
