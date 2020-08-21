<?php declare(strict_types=1);

namespace Google\Generator\Utils;

use \Google\Protobuf\Internal\FileDescriptorProto;

class ProtoHelpers
{
    /**
     * Get the PHP namespace of the specified file.
     * This is achieved by reading the PHP namespace option if present, otherwise it uses the proto package name.
     * 
     * @param FileDescriptorProto $fileDesc The file for this to get the PHP namespace.
     */
    public static function GetNamespace(FileDescriptorProto $fileDesc): string
    {
        if ($fileDesc->hasOptions())
        {
            $opts = $fileDesc->getOptions();
            if ($opts->hasPhpNamespace())
            {
                return $opts->getPhpNamespace();
            }
        }
        // Fallback to munging the proto package.
        return implode('\\', explode('.', $fileDesc->getPackage()));
    }

    /**
     * Add an underlying proto to a descriptor.
     * 
     * @param mixed $desc The descriptor to which to add the proto.
     * @param mixed $proto The underlying proto to add.
     * 
     * @return mixed To descriptor passed in.
     */
    public static function AddProto($desc, $proto)
    {
        $desc->underlyingProto = $proto;
        return $desc;
    }
}
