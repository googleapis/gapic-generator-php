<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: grpc/service_config/service_config.proto

namespace Grpc\Service_config;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Configuration for the cds LB policy.
 *
 * Generated from protobuf message <code>grpc.service_config.CdsConfig</code>
 */
class CdsConfig extends \Google\Protobuf\Internal\Message
{
    /**
     * Required.
     *
     * Generated from protobuf field <code>string cluster = 1;</code>
     */
    protected $cluster = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $cluster
     *           Required.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Grpc\ServiceConfig\ServiceConfig::initOnce();
        parent::__construct($data);
    }

    /**
     * Required.
     *
     * Generated from protobuf field <code>string cluster = 1;</code>
     * @return string
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * Required.
     *
     * Generated from protobuf field <code>string cluster = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setCluster($var)
    {
        GPBUtil::checkString($var, True);
        $this->cluster = $var;

        return $this;
    }

}

