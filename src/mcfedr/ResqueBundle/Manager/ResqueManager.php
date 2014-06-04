<?php

namespace mcfedr\ResqueBundle\Manager;

class ResqueManager
{
    const JOB_CLASS = 'mcfedr\ResqueBundle\Job\ResqueJob';

    /**
     * @var array
     */
    private $kernelOptions;

    /**
     * @param string $host
     * @param int $port
     * @param array $kernelOptions
     */
    public function __construct($host, $port, $kernelOptions)
    {
        Resque::setBackend("$host:$port");
        $this->kernelOptions = $kernelOptions;
    }

    /**
     * Put a new job on a queue
     *
     * @param string $name The name of the worker
     * @param array $options Options to pass to execute - must be json_encode-able
     * @param string $queue Optional queue name, otherwise the default queue will be used
     * @param int $priority
     * @param \DateTime $when Optionally set a time in the future when this task should happen
     * @return Job
     */
    public function put($name, array $options = null, $queue = null, $priority = null, $when = null)
    {
        if (!$queue) {
            $queue = 'default';
        }

        $args = array_merge([
            'name' => $name,
            'options' => $options
        ], $this->kernelOptions);

        if ($when) {
            \ResqueScheduler::enqueueAt($when, $queue, static::JOB_CLASS, $args);
        } else {
            \Resque::enqueue($queue, static::JOB_CLASS, $args);
        }
    }

    /**
     * Remove a job
     *
     * @param $job
     * @throws WrongJobException
     * @throws NoSuchJobException
     */
    public function delete(Job $job)
    {
        $this->manager->delete($job);
    }
} 