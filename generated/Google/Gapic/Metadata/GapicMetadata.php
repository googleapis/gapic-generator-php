<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: gapic/metadata/gapic_metadata.proto

namespace Google\Gapic\Metadata;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Metadata about a GAPIC library for a specific combination of API, version, and
 * computer language.
 *
 * Generated from protobuf message <code>google.gapic.metadata.GapicMetadata</code>
 */
class GapicMetadata extends \Google\Protobuf\Internal\Message
{
    /**
     * Schema version of this proto. Current value: 1.0
     *
     * Generated from protobuf field <code>string schema = 1;</code>
     */
    protected $schema = '';
    /**
     * Any human-readable comments to be included in this file.
     *
     * Generated from protobuf field <code>string comment = 2;</code>
     */
    protected $comment = '';
    /**
     * Computer language of this generated language. This must be
     * spelled out as it spoken in English, with no capitalization or
     * separators (e.g. "csharp", "nodejs").
     *
     * Generated from protobuf field <code>string language = 3;</code>
     */
    protected $language = '';
    /**
     * The proto package containing the API definition for which this
     * GAPIC library was generated.
     *
     * Generated from protobuf field <code>string proto_package = 4;</code>
     */
    protected $proto_package = '';
    /**
     * The language-specific library package for this GAPIC library.
     *
     * Generated from protobuf field <code>string library_package = 5;</code>
     */
    protected $library_package = '';
    /**
     * A map from each proto-defined service to ServiceForTransports,
     * which allows listing information about transport-specific
     * implementations of the service.
     * The key is the name of the service as it appears in the .proto
     * file.
     *
     * Generated from protobuf field <code>map<string, .google.gapic.metadata.GapicMetadata.ServiceForTransport> services = 6;</code>
     */
    private $services;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $schema
     *           Schema version of this proto. Current value: 1.0
     *     @type string $comment
     *           Any human-readable comments to be included in this file.
     *     @type string $language
     *           Computer language of this generated language. This must be
     *           spelled out as it spoken in English, with no capitalization or
     *           separators (e.g. "csharp", "nodejs").
     *     @type string $proto_package
     *           The proto package containing the API definition for which this
     *           GAPIC library was generated.
     *     @type string $library_package
     *           The language-specific library package for this GAPIC library.
     *     @type array|\Google\Protobuf\Internal\MapField $services
     *           A map from each proto-defined service to ServiceForTransports,
     *           which allows listing information about transport-specific
     *           implementations of the service.
     *           The key is the name of the service as it appears in the .proto
     *           file.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Gapic\Metadata\GapicMetadata::initOnce();
        parent::__construct($data);
    }

    /**
     * Schema version of this proto. Current value: 1.0
     *
     * Generated from protobuf field <code>string schema = 1;</code>
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Schema version of this proto. Current value: 1.0
     *
     * Generated from protobuf field <code>string schema = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSchema($var)
    {
        GPBUtil::checkString($var, True);
        $this->schema = $var;

        return $this;
    }

    /**
     * Any human-readable comments to be included in this file.
     *
     * Generated from protobuf field <code>string comment = 2;</code>
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Any human-readable comments to be included in this file.
     *
     * Generated from protobuf field <code>string comment = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setComment($var)
    {
        GPBUtil::checkString($var, True);
        $this->comment = $var;

        return $this;
    }

    /**
     * Computer language of this generated language. This must be
     * spelled out as it spoken in English, with no capitalization or
     * separators (e.g. "csharp", "nodejs").
     *
     * Generated from protobuf field <code>string language = 3;</code>
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Computer language of this generated language. This must be
     * spelled out as it spoken in English, with no capitalization or
     * separators (e.g. "csharp", "nodejs").
     *
     * Generated from protobuf field <code>string language = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setLanguage($var)
    {
        GPBUtil::checkString($var, True);
        $this->language = $var;

        return $this;
    }

    /**
     * The proto package containing the API definition for which this
     * GAPIC library was generated.
     *
     * Generated from protobuf field <code>string proto_package = 4;</code>
     * @return string
     */
    public function getProtoPackage()
    {
        return $this->proto_package;
    }

    /**
     * The proto package containing the API definition for which this
     * GAPIC library was generated.
     *
     * Generated from protobuf field <code>string proto_package = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setProtoPackage($var)
    {
        GPBUtil::checkString($var, True);
        $this->proto_package = $var;

        return $this;
    }

    /**
     * The language-specific library package for this GAPIC library.
     *
     * Generated from protobuf field <code>string library_package = 5;</code>
     * @return string
     */
    public function getLibraryPackage()
    {
        return $this->library_package;
    }

    /**
     * The language-specific library package for this GAPIC library.
     *
     * Generated from protobuf field <code>string library_package = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setLibraryPackage($var)
    {
        GPBUtil::checkString($var, True);
        $this->library_package = $var;

        return $this;
    }

    /**
     * A map from each proto-defined service to ServiceForTransports,
     * which allows listing information about transport-specific
     * implementations of the service.
     * The key is the name of the service as it appears in the .proto
     * file.
     *
     * Generated from protobuf field <code>map<string, .google.gapic.metadata.GapicMetadata.ServiceForTransport> services = 6;</code>
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * A map from each proto-defined service to ServiceForTransports,
     * which allows listing information about transport-specific
     * implementations of the service.
     * The key is the name of the service as it appears in the .proto
     * file.
     *
     * Generated from protobuf field <code>map<string, .google.gapic.metadata.GapicMetadata.ServiceForTransport> services = 6;</code>
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setServices($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Gapic\Metadata\GapicMetadata\ServiceForTransport::class);
        $this->services = $arr;

        return $this;
    }

}

