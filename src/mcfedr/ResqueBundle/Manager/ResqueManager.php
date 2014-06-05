<?php

namespace mcfedr\ResqueBundle\Manager;

/**
 * Class ResqueManager
 * @package mcfedr\ResqueBundle\Manager
 *
 * The main class you will use, puts jobs on the queue
 * Can also delete jobs
 */
class ResqueManager
{
    const JOB_CLASS = 'mcfedr\ResqueBundle\Resque\Job';

    /**
     * @var array
     */
    private $kernelOptions;

    /**
     * @var string
     */
    private $defaultQueue;

    /**
     * @param string $host
     * @param int $port
     * @param array $kernelOptions
     * @param string $defaultQueue
     */
    public function __construct($host, $port, $kernelOptions, $defaultQueue = 'default')
    {
        \Resque::setBackend("$host:$port");
        $this->kernelOptions = $kernelOptions;
        $this->defaultQueue = $defaultQueue;
    }

    /**
     * Put a new job on a queue
     *
     * @param string $name The service name of the worker that implements {@link \mcfedr\ResqueBundle\Worker\WorkerInterface}
     * @param array $options Options to pass to execute - must be serializable
     * @param string $queue Optional queue name, otherwise the default queue will be used
     * @param int $priority
     * @param \DateTime $when Optionally set a time in the future when this task should happen
     * @return JobDescription|null Only jobs queued in the future can be deleted
     */
    public function put($name, array $options = null, $queue = null, $priority = null, $when = null)
    {
        if (!$queue) {
            $queue = $this->defaultQueue;
        }

        $args = array_merge([
            'name' => $name,
            'options' => $options
        ], $this->kernelOptions);

        if ($when) {
            \ResqueScheduler::enqueueAt($when, $queue, static::JOB_CLASS, $args);
            return new JobDescription($when, $queue, static::JOB_CLASS, $args);
        } else {
            \Resque::enqueue($queue, static::JOB_CLASS, $args);
            return null;
        }
    }

    /**
     * Remove a job
     *
     * @param JobDescription $job
     * @return mixed
     */
    public function delete(JobDescription $job)
    {
        return \ResqueScheduler::removeDelayedJobFromTimestamp($job->getWhen(), $job->getQueue(), $job->getClass(),
            $job->getArgs());
    }
} 