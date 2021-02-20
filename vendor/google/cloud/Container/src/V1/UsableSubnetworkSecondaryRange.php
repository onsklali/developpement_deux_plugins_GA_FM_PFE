<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/container/v1/cluster_service.proto

namespace Google\Cloud\Container\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Secondary IP range of a usable subnetwork.
 *
 * Generated from protobuf message <code>google.container.v1.UsableSubnetworkSecondaryRange</code>
 */
class UsableSubnetworkSecondaryRange extends \Google\Protobuf\Internal\Message
{
    /**
     * The name associated with this subnetwork secondary range, used when adding
     * an alias IP range to a VM instance.
     *
     * Generated from protobuf field <code>string range_name = 1;</code>
     */
    private $range_name = '';
    /**
     * The range of IP addresses belonging to this subnetwork secondary range.
     *
     * Generated from protobuf field <code>string ip_cidr_range = 2;</code>
     */
    private $ip_cidr_range = '';
    /**
     * This field is to determine the status of the secondary range programmably.
     *
     * Generated from protobuf field <code>.google.container.v1.UsableSubnetworkSecondaryRange.Status status = 3;</code>
     */
    private $status = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $range_name
     *           The name associated with this subnetwork secondary range, used when adding
     *           an alias IP range to a VM instance.
     *     @type string $ip_cidr_range
     *           The range of IP addresses belonging to this subnetwork secondary range.
     *     @type int $status
     *           This field is to determine the status of the secondary range programmably.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Container\V1\ClusterService::initOnce();
        parent::__construct($data);
    }

    /**
     * The name associated with this subnetwork secondary range, used when adding
     * an alias IP range to a VM instance.
     *
     * Generated from protobuf field <code>string range_name = 1;</code>
     * @return string
     */
    public function getRangeName()
    {
        return $this->range_name;
    }

    /**
     * The name associated with this subnetwork secondary range, used when adding
     * an alias IP range to a VM instance.
     *
     * Generated from protobuf field <code>string range_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setRangeName($var)
    {
        GPBUtil::checkString($var, True);
        $this->range_name = $var;

        return $this;
    }

    /**
     * The range of IP addresses belonging to this subnetwork secondary range.
     *
     * Generated from protobuf field <code>string ip_cidr_range = 2;</code>
     * @return string
     */
    public function getIpCidrRange()
    {
        return $this->ip_cidr_range;
    }

    /**
     * The range of IP addresses belonging to this subnetwork secondary range.
     *
     * Generated from protobuf field <code>string ip_cidr_range = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setIpCidrRange($var)
    {
        GPBUtil::checkString($var, True);
        $this->ip_cidr_range = $var;

        return $this;
    }

    /**
     * This field is to determine the status of the secondary range programmably.
     *
     * Generated from protobuf field <code>.google.container.v1.UsableSubnetworkSecondaryRange.Status status = 3;</code>
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * This field is to determine the status of the secondary range programmably.
     *
     * Generated from protobuf field <code>.google.container.v1.UsableSubnetworkSecondaryRange.Status status = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setStatus($var)
    {
        GPBUtil::checkEnum($var, \Google\Cloud\Container\V1\UsableSubnetworkSecondaryRange_Status::class);
        $this->status = $var;

        return $this;
    }

}

