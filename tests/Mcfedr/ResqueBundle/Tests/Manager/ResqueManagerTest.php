<?php

namespace Mcfedr\ResqueBundle\Tests\Manager;

use Mcfedr\ResqueBundle\Manager\ResqueManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResqueManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ResqueManager */
    protected $manager;

    public function setUp()
    {
        $this->manager = new ResqueManager('127.0.0.1', 6379, [], 'default', 'tests:', false, false);
    }

    public function testJobClass()
    {
        $this->assertEquals('Mcfedr\ResqueBundle\Resque\Job', ResqueManager::JOB_CLASS);
    }

    /**
     * @dataProvider getValues
     */
    public function testPutFuture($name, $options, $queue, $when)
    {
        $value = $this->manager->put($name, $options, $queue, new \DateTime($when));
        $this->assertInstanceOf('Mcfedr\ResqueBundle\Manager\JobDescription', $value);
    }

    /**
     * @dataProvider getValues
     */
    public function testPutNow($name, $options, $queue, $when)
    {
        $this->assertNull($this->manager->put($name, $options, $queue));
    }

    public function testRelativeKernel()
    {
        $this->manager->setKernelOptions([
            'kernel.root_dir' => __DIR__
        ]);

        $this->assertEquals('../../../../tests/Mcfedr/ResqueBundle/Tests/Manager/', $this->manager->getKernelOptions()['kernel.root_dir']);
    }

    /**
     * @dataProvider getValues
     */
    public function testDelete($name, $options, $queue, $when)
    {
        $job = $this->manager->put($name, $options, $queue, (new \DateTime($when))->add(new \DateInterval('P1M')));
        $this->assertEquals(1, $this->manager->delete($job));

        $this->assertEquals(0, $this->manager->delete($job));
    }

    public function getValues()
    {
        return [
            ['test', null, null, 'next TUE 11:00'],
            ['test1', [], null, 'next WED 21:00']
        ];
    }
}
