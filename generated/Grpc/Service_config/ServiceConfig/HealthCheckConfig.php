<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: grpc/service_config/service_config.proto

namespace Grpc\Service_config\ServiceConfig;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>grpc.service_config.ServiceConfig.HealthCheckConfig</code>
 */
class HealthCheckConfig extends \Google\Protobuf\Internal\Message
{
    /**
     * Service name to use in the health-checking request.
     *
     * Generated from protobuf field <code>.google.protobuf.StringValue service_name = 1;</code>
     */
    protected $service_name = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Google\Protobuf\StringValue $service_name
     *           Service name to use in the health-checking request.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Grpc\ServiceConfig\ServiceConfig::initOnce();
        parent::__construct($data);
    }

    /**
     * Service name to use in the health-checking request.
     *
     * Generated from protobuf field <code>.google.protobuf.StringValue service_name = 1;</code>
     * @return \Google\Protobuf\StringValue
     */
    public function getServiceName()
    {
        return isset($this->service_name) ? $this->service_name : null;
    }

    public function hasServiceName()
    {
        return isset($this->service_name);
    }

    public function clearServiceName()
    {
        unset($this->service_name);
    }

    /**
     * Returns the unboxed value from <code>getServiceName()</code>

     * Service name to use in the health-checking request.
     *
     * Generated from protobuf field <code>.google.protobuf.StringValue service_name = 1;</code>
     * @return string|null
     */
    public function getServiceNameUnwrapped()
    {
        return $this->readWrapperValue("service_name");
    }

    /**
     * Service name to use in the health-checking request.
     *
     * Generated from protobuf field <code>.google.protobuf.StringValue service_name = 1;</code>
     * @param \Google\Protobuf\StringValue $var
     * @return $this
     */
    public function setServiceName($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\StringValue::class);
        $this->service_name = $var;

        return $this;
    }

    /**
     * Sets the field by wrapping a primitive type in a Google\Protobuf\StringValue object.

     * Service name to use in the health-checking request.
     *
     * Generated from protobuf field <code>.google.protobuf.StringValue service_name = 1;</code>
     * @param string|null $var
     * @return $this
     */
    public function setServiceNameUnwrapped($var)
    {
        $this->writeWrapperValue("service_name", $var);
        return $this;}

}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HealthCheckConfig::class, \Grpc\Service_config\ServiceConfig_HealthCheckConfig::class);

