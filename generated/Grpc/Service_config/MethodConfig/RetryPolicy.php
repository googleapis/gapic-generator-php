<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: grpc/service_config/service_config.proto

namespace Grpc\Service_config\MethodConfig;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The retry policy for outgoing RPCs.
 *
 * Generated from protobuf message <code>grpc.service_config.MethodConfig.RetryPolicy</code>
 */
class RetryPolicy extends \Google\Protobuf\Internal\Message
{
    /**
     * The maximum number of RPC attempts, including the original attempt.
     * This field is required and must be greater than 1.
     * Any value greater than 5 will be treated as if it were 5.
     *
     * Generated from protobuf field <code>uint32 max_attempts = 1;</code>
     */
    protected $max_attempts = 0;
    /**
     * Exponential backoff parameters. The initial retry attempt will occur at
     * random(0, initial_backoff). In general, the nth attempt will occur at
     * random(0,
     *   min(initial_backoff*backoff_multiplier**(n-1), max_backoff)).
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>.google.protobuf.Duration initial_backoff = 2;</code>
     */
    protected $initial_backoff = null;
    /**
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>.google.protobuf.Duration max_backoff = 3;</code>
     */
    protected $max_backoff = null;
    /**
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>float backoff_multiplier = 4;</code>
     */
    protected $backoff_multiplier = 0.0;
    /**
     * The set of status codes which may be retried.
     * This field is required and must be non-empty.
     *
     * Generated from protobuf field <code>repeated .google.rpc.Code retryable_status_codes = 5;</code>
     */
    private $retryable_status_codes;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $max_attempts
     *           The maximum number of RPC attempts, including the original attempt.
     *           This field is required and must be greater than 1.
     *           Any value greater than 5 will be treated as if it were 5.
     *     @type \Google\Protobuf\Duration $initial_backoff
     *           Exponential backoff parameters. The initial retry attempt will occur at
     *           random(0, initial_backoff). In general, the nth attempt will occur at
     *           random(0,
     *             min(initial_backoff*backoff_multiplier**(n-1), max_backoff)).
     *           Required. Must be greater than zero.
     *     @type \Google\Protobuf\Duration $max_backoff
     *           Required. Must be greater than zero.
     *     @type float $backoff_multiplier
     *           Required. Must be greater than zero.
     *     @type int[]|\Google\Protobuf\Internal\RepeatedField $retryable_status_codes
     *           The set of status codes which may be retried.
     *           This field is required and must be non-empty.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Grpc\ServiceConfig\ServiceConfig::initOnce();
        parent::__construct($data);
    }

    /**
     * The maximum number of RPC attempts, including the original attempt.
     * This field is required and must be greater than 1.
     * Any value greater than 5 will be treated as if it were 5.
     *
     * Generated from protobuf field <code>uint32 max_attempts = 1;</code>
     * @return int
     */
    public function getMaxAttempts()
    {
        return $this->max_attempts;
    }

    /**
     * The maximum number of RPC attempts, including the original attempt.
     * This field is required and must be greater than 1.
     * Any value greater than 5 will be treated as if it were 5.
     *
     * Generated from protobuf field <code>uint32 max_attempts = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setMaxAttempts($var)
    {
        GPBUtil::checkUint32($var);
        $this->max_attempts = $var;

        return $this;
    }

    /**
     * Exponential backoff parameters. The initial retry attempt will occur at
     * random(0, initial_backoff). In general, the nth attempt will occur at
     * random(0,
     *   min(initial_backoff*backoff_multiplier**(n-1), max_backoff)).
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>.google.protobuf.Duration initial_backoff = 2;</code>
     * @return \Google\Protobuf\Duration
     */
    public function getInitialBackoff()
    {
        return isset($this->initial_backoff) ? $this->initial_backoff : null;
    }

    public function hasInitialBackoff()
    {
        return isset($this->initial_backoff);
    }

    public function clearInitialBackoff()
    {
        unset($this->initial_backoff);
    }

    /**
     * Exponential backoff parameters. The initial retry attempt will occur at
     * random(0, initial_backoff). In general, the nth attempt will occur at
     * random(0,
     *   min(initial_backoff*backoff_multiplier**(n-1), max_backoff)).
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>.google.protobuf.Duration initial_backoff = 2;</code>
     * @param \Google\Protobuf\Duration $var
     * @return $this
     */
    public function setInitialBackoff($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\Duration::class);
        $this->initial_backoff = $var;

        return $this;
    }

    /**
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>.google.protobuf.Duration max_backoff = 3;</code>
     * @return \Google\Protobuf\Duration
     */
    public function getMaxBackoff()
    {
        return isset($this->max_backoff) ? $this->max_backoff : null;
    }

    public function hasMaxBackoff()
    {
        return isset($this->max_backoff);
    }

    public function clearMaxBackoff()
    {
        unset($this->max_backoff);
    }

    /**
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>.google.protobuf.Duration max_backoff = 3;</code>
     * @param \Google\Protobuf\Duration $var
     * @return $this
     */
    public function setMaxBackoff($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\Duration::class);
        $this->max_backoff = $var;

        return $this;
    }

    /**
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>float backoff_multiplier = 4;</code>
     * @return float
     */
    public function getBackoffMultiplier()
    {
        return $this->backoff_multiplier;
    }

    /**
     * Required. Must be greater than zero.
     *
     * Generated from protobuf field <code>float backoff_multiplier = 4;</code>
     * @param float $var
     * @return $this
     */
    public function setBackoffMultiplier($var)
    {
        GPBUtil::checkFloat($var);
        $this->backoff_multiplier = $var;

        return $this;
    }

    /**
     * The set of status codes which may be retried.
     * This field is required and must be non-empty.
     *
     * Generated from protobuf field <code>repeated .google.rpc.Code retryable_status_codes = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getRetryableStatusCodes()
    {
        return $this->retryable_status_codes;
    }

    /**
     * The set of status codes which may be retried.
     * This field is required and must be non-empty.
     *
     * Generated from protobuf field <code>repeated .google.rpc.Code retryable_status_codes = 5;</code>
     * @param int[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setRetryableStatusCodes($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::ENUM, \Google\Rpc\Code::class);
        $this->retryable_status_codes = $arr;

        return $this;
    }

}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetryPolicy::class, \Grpc\Service_config\MethodConfig_RetryPolicy::class);

