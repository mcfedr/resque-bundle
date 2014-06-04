<?php

namespace mcfedr\ResqueBundle\Tests\Manager;

use mcfedr\ResqueBundle\Manager\ResqueManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResqueManagerTest extends WebTestCase
{
    /** @var ResqueManager */
    protected $manager;

    public function setUp()
    {
        $this->manager = new ResqueManager('127.0.0.1', 6379, []);
    }

    public function testJobClass()
    {
        $this->assertEquals('mcfedr\ResqueBundle\Job\ResqueJob', ResqueManager::JOB_CLASS);
    }

    /**
     * @dataProvider getValues
     */
    public function testPut($name, $options, $queue, $priority, $when)
    {
        $this->assertInstanceOf('mcfedr\ResqueBundle\Manager\ResqueManager', $this->manager);
        $value = $this->manager->put($name, $options, $queue, $priority, new \DateTime($when));

        $this->assertNull($this->manager->put($name));
        $this->assertInstanceOf('mcfedr\ResqueBundle\Manager\JobDescription', $value);
    }

    public function getValues()
    {
        return [
            ['test', null, null, null, 'next TUE 11:00'],
            ['test1', [], null, null, 'next WED 21:00']
        ];
    }

    /**
     * @dataProvider getValues
     */
    public function testDelete($name, $options, $queue, $priority, $when)
    {
        $job = $this->manager->put($name, $options, $queue, $priority, new \DateTime($when));
        $this->assertEquals(2, $this->manager->delete($job));

        $this->assertEquals(0, $this->manager->delete($job));
    }
}
