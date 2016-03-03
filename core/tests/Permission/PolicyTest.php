<?php
namespace Xpressengine\Tests\Permission;

use Mockery as m;
use Xpressengine\Permission\Policy;

class PolicyTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCheck()
    {
        $updateGrant = [
            'rating' => 'member',
            'group' => [],
            'user' => [],
            'except' => [],
            'vgroup' => ['virtual']
        ];
        $readGrant = [
            'rating' => 'guest',
            'group' => [],
            'user' => [],
            'except' => [],
            'vgroup' => []
        ];

        $mockPermission = m::mock('Xpressengine\Permission\Permission');
        $mockPermission->shouldReceive('offsetGet')->with('update')->andReturn($updateGrant);
        $mockPermission->shouldReceive('offsetGet')->with('read')->andReturn($readGrant);

        $mockUser = m::mock('Xpressengine\Member\Entities\Guest');
        $mockUser->shouldReceive('getId')->andReturnNull();
        $mockUser->shouldReceive('getGroups')->andReturn([]);

        list($handler, $vgroups) = $this->getMocks();
        $instance = new SamplePolicy($handler, $vgroups);

        $handler->shouldReceive('get')->twice()->with('plugin.dummy', 'default')->andReturn($mockPermission);


        $this->assertTrue($instance->read($mockUser, 'plugin.dummy', 'default'));
        $this->assertFalse($instance->update($mockUser, 'plugin.dummy', 'default'));
    }
    
    private function getMocks()
    {
        return [
            m::mock('Xpressengine\Permission\PermissionHandler'),
            m::mock('Xpressengine\Member\Repositories\VirtualGroupRepositoryInterface'),
        ];
    }
}

class SamplePolicy extends Policy
{
    public function update($user, $name, $siteKey)
    {
        return $this->check($user, $this->get($name, $siteKey), 'update');
    }

    public function read($user, $name, $siteKey)
    {
        return $this->check($user, $this->get($name, $siteKey), 'read');
    }
}
