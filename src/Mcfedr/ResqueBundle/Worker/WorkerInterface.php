<?php

namespace Mcfedr\ResqueBundle\Worker;

interface WorkerInterface
{
    /**
     * Called to start the queued task.
     *
     * @param array $options
     *
     * @throws \Exception
     */
    public function execute(array $options = null);
}
