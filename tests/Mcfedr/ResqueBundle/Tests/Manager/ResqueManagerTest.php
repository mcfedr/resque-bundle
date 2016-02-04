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

    /**
     * @dataProvider getValues
     */
    public function testPutFuture($name, $options, $queue, $when)
    {
        $value = $this->manager->put($name, $options, $queue, new \DateTime($when));
        $this->assertInstanceOf('Mcfedr\ResqueBundle\Manager\JobDescription', $value);
        $this->assertTrue($value->isFutureJob());
        $this->assertNull($value->getId());
        $this->assertEquals('Mcfedr\ResqueBundle\Resque\Job', $value->getClass());
    }

    /**
     * @dataProvider getValues
     */
    public function testPutNow($name, $options, $queue, $when)
    {
        $value = $this->manager->put($name, $options, $queue);
        $this->assertInstanceOf('Mcfedr\ResqueBundle\Manager\JobDescription', $value);
        $this->assertFalse($value->isFutureJob());
        $this->assertInternalType('string', $value->getId());
        $this->assertEquals('Mcfedr\ResqueBundle\Resque\Job', $value->getClass());
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

    /**
     * @dataProvider getValues
     */
    public function testDeleteNow($name, $options, $queue, $when)
    {
        $job = $this->manager->put($name, $options, $queue);
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
