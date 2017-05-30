<?php

namespace Mcfedr\ResqueBundle\Manager;

use Mcfedr\ResqueBundle\Resque\Job;

class ResqueManager
{
    const JOB_CLASS = 'Mcfedr\ResqueBundle\Resque\Job';

    /**
     * @var array
     */
    private $kernelOptions;

    /**
     * @var string
     */
    private $defaultQueue;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var bool
     */
    private $trackStatus;

    /**
     * @param string $host
     * @param int    $port
     * @param array  $kernelOptions
     * @param string $defaultQueue
     * @param bool   $debug         if debug is true then no calls to Resque will be made
     * @param bool   $trackStatus   Set to true to be able to monitor the status of jobs
     */
    public function __construct($host, $port, $kernelOptions, $defaultQueue, $prefix, $debug, $trackStatus)
    {
        $this->defaultQueue = $defaultQueue;
        $this->setKernelOptions($kernelOptions);
        $this->debug = $debug;
        if (!$debug) {
            \Resque::setBackend("$host:$port");
            if ($prefix) {
                \Resque_Redis::prefix($prefix);
            }
        }
        $this->trackStatus = $trackStatus;
    }

    /**
     * @return array
     */
    public function getKernelOptions()
    {
        return $this->kernelOptions;
    }

    /**
     * @param array $kernelOptions
     *
     * @return ResqueManager
     */
    public function setKernelOptions($kernelOptions)
    {
        $this->kernelOptions = $kernelOptions;

        //Convert root_dir to be relative to the resque bundle paths, this makes it possible to deploy workers in different places
        if (array_key_exists('kernel.root_dir', $this->kernelOptions)) {
            $this->kernelOptions['kernel.root_dir'] = $this->getRelativePath(__DIR__, $this->kernelOptions['kernel.root_dir']);
        }

        return $this;
    }

    /**
     * Put a new job on a queue.
     *
     * @param string    $name        The service name of the worker that implements {@link \Mcfedr\ResqueBundle\Worker\WorkerInterface}
     * @param array     $options     Options to pass to execute - must be json serializable
     * @param string    $queue       Optional queue name, otherwise the default queue will be used
     * @param \DateTime $when        Optionally set a time in the future when this task should happen
     * @param bool      $trackStatus Set to true to be able to monitor the status of a job
     *
     * @return JobDescription
     */
    public function put($name, array $options = null, $queue = null, \DateTime $when = null, $trackStatus = false)
    {
        if ($this->debug) {
            return;
        }

        if (!$queue) {
            $queue = $this->defaultQueue;
        }

        $args = array_merge([
            'name' => $name,
            'options' => $options,
        ], $this->kernelOptions);

        $trackJobStatus = $trackStatus || $this->trackStatus;

        if ($when) {
            \ResqueScheduler::enqueueAt($when, $queue, Job::class, $args, $trackJobStatus);

            return new JobDescription($when, $queue, Job::class, $args, $trackJobStatus);
        } else {
            $id = \Resque::enqueue($queue, Job::class, $args, $trackJobStatus);

            return new JobDescription(null, $queue, Job::class, $args, $trackJobStatus, $id);
        }
    }

    /**
     * Remove a scheduled job.
     *
     * @param JobDescription $job
     *
     * @return int number of deleted jobs
     */
    public function delete(JobDescription $job)
    {
        if ($this->debug) {
            return 0;
        }

        if (!$job->isFutureJob()) {
            return 0;
        }

        return \ResqueScheduler::removeDelayedJobFromTimestamp($job->getWhen(), $job->getQueue(), $job->getClass(),
            $job->getArgs(), $job->getTrackStatus());
    }

    private function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/').'/' : $from;
        $to = is_dir($to) ? rtrim($to, '\/').'/' : $to;
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $from = explode('/', $from);
        $to = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './'.$relPath[0];
                }
            }
        }

        return implode('/', $relPath);
    }
}
