<?php

namespace Mcfedr\ResqueBundle\Manager;

use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class ResqueManager
 * @package Mcfedr\ResqueBundle\Manager
 *
 * The main class you will use, puts jobs on the queue
 * Can also delete jobs
 */
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
     * @param string $host
     * @param int $port
     * @param array $kernelOptions
     * @param string $defaultQueue
     * @param bool $debug
     */
    public function __construct($host, $port, $kernelOptions, $defaultQueue = 'default', $debug = false)
    {
        $this->defaultQueue = $defaultQueue;
        $this->setKernelOptions($kernelOptions);
        $this->debug = $debug;
        if (!$debug) {
            \Resque::setBackend("$host:$port");
        }
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
     * @return ResqueManager
     */
    public function setKernelOptions($kernelOptions)
    {
        $this->kernelOptions = $kernelOptions;
        if (array_key_exists('kernel.root_dir', $this->kernelOptions)) {
            $this->kernelOptions['kernel.root_dir'] = $this->getRelativePath(__DIR__, $this->kernelOptions['kernel.root_dir']);
        }
        return $this;
    }

    /**
     * Put a new job on a queue
     *
     * @param string $name The service name of the worker that implements {@link \Mcfedr\ResqueBundle\Worker\WorkerInterface}
     * @param array $options Options to pass to execute - must be serializable
     * @param string $queue Optional queue name, otherwise the default queue will be used
     * @param int $priority
     * @param \DateTime $when Optionally set a time in the future when this task should happen
     * @return JobDescription|null Only jobs queued in the future can be deleted
     */
    public function put($name, array $options = null, $queue = null, $priority = null, $when = null)
    {
        if ($this->debug) {
            return;
        }

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
     * @return int number of deleted jobs
     */
    public function delete(JobDescription $job)
    {
        if ($this->debug) {
            return 0;
        }

        return \ResqueScheduler::removeDelayedJobFromTimestamp($job->getWhen(), $job->getQueue(), $job->getClass(),
            $job->getArgs());
    }

    private function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }
}
