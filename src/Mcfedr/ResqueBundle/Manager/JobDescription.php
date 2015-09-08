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
     * @var string
     */
    private $id;

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

    public function __construct($when, $queue, $class, $args, $trackStatus = false, $id = null)
    {
        $this->args = $args;
        $this->class = $class;
        $this->queue = $queue;
        $this->when = $when;
        $this->trackStatus = $trackStatus;
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function isFutureJob()
    {
        return !!$this->getWhen();
    }
}
