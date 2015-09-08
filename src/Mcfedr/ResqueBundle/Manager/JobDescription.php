<?php

namespace Mcfedr\ResqueBundle\Manager;

/**
 * Class JobDescription
 * @package Mcfedr\ResqueBundle\Manager
 *
 * Holds the parameters of a scheduled job
 */
class JobDescription
{
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $args;

    /**
     * @var boolean
     */
    private $trackStatus;

    public function __construct($when, $queue, $class, $args, $trackStatus = false)
    {
        $this->args = $args;
        $this->class = $class;
        $this->queue = $queue;
        $this->when = $when;
        $this->trackStatus = $trackStatus;
    }

    /**
     * @param array $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param \DateTime $when
     */
    public function setWhen($when)
    {
        $this->when = $when;
    }

    /**
     * @return \DateTime
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * @return boolean
     */
    public function getTrackStatus()
    {
        return $this->trackStatus;
    }

    /**
     * @param boolean $trackStatus
     * @return JobDescription
     */
    public function setTrackStatus($trackStatus)
    {
        $this->trackStatus = $trackStatus;
        return $this;
    }
}
