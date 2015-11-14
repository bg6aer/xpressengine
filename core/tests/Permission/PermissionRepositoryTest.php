<?php
namespace Xpressengine\Tests\Permission;

use Mockery as m;
use Xpressengine\Permission\PermissionRepository;

class PermissionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testFindByTypeAndNameRetunsRegistered()
    {
        list($conn, $query) = $this->getMocks();

        $conn->shouldReceive('table')->andReturn($query);
        $query->shouldReceive('where')->once()->with('siteKey', 'default')->andReturn($query);
        $query->shouldReceive('where')->once()->with('name', 'board.notice')->andReturn($query);
        $query->shouldReceive('where')->once()->with('type', 'instance')->andReturn($query);
        $query->shouldReceive('first')->once()->withNoArgs()->andReturn((object)[
            'id' => 1,
            'type' => 'instance',
            'name' => 'board.notice',
            'grants' => '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"member"},"read":{"type":"power","value":"guest"},"update":{"type":"group","value":["group_id_1","group_id_2"]},"delete":{"type":"group","value":["group_id_1","group_id_2"]}}',
        ]);

        $instance = new PermissionRepository($conn);
        $registered = $instance->findByTypeAndName('default', 'instance', 'board.notice');

        $this->assertInstanceOf('Xpressengine\Permission\Registered', $registered);
        $this->assertEquals(['type' => 'power', 'value' => 'guest'], $registered['access']);
        $this->assertEquals('board.notice', $registered->name);
        $this->assertTrue(isset($registered['create']));

        $keys = '';
        $comma = '';
        foreach ($registered as $key => $value) {
            $keys .= $comma . $key;
            $comma = ',';
        }

        $this->assertEquals('access,create,read,update,delete', $keys);
    }

    public function testInsert()
    {
        list($conn, $query) = $this->getMocks();

        $mockRegistered = m::mock('Xpressengine\Permission\Registered');
        $mockRegistered->shouldReceive('getOriginal')->andReturn([]);
        $mockRegistered->shouldReceive('getAttributes')->andReturn([
            'type' => 'instance',
            'name' => 'board.notice',
            'grants' => '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"member"}}',
        ]);

        $conn->shouldReceive('table')->andReturn($query);
        $query->shouldReceive('insertGetId')->once()->with(m::on(function ($array) {
            return $array['type'] === 'instance'
            && $array['name'] === 'board.notice'
            && $array['grants'] === '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"member"}}';
        }))->andReturn(1);

        $instance = new PermissionRepository($conn);
        $registered = $instance->insert($mockRegistered);

        $this->assertEquals(1, $registered->id);
        $this->assertEquals(['type' => 'power', 'value' => 'guest'], $registered['access']);
        $this->assertEquals('board.notice', $registered->name);
        $this->assertEquals('instance', $registered->type);
    }

    public function testUpdate()
    {
        list($conn, $query) = $this->getMocks();

        $mockRegistered = m::mock('Xpressengine\Permission\Registered');
        $mockRegistered->id = 1;
        $mockRegistered->shouldReceive('getOriginal')->andReturn([
            'id' => 1,
            'type' => 'instance',
            'name' => 'board.notice',
            'grants' => '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"super"}}',
        ]);
        $mockRegistered->shouldReceive('diff')->andReturn([
            'grants' => '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"member"}}',
        ]);

        $conn->shouldReceive('table')->andReturn($query);
        $query->shouldReceive('where')->once()->with('id', 1)->andReturn($query);

        $query->shouldReceive('update')->once()->with(m::on(function ($array) {
            return $array['grants'] === '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"member"}}';
        }))->andReturnNull();

        $instance = new PermissionRepository($conn);
        $registered = $instance->update($mockRegistered);

        $this->assertEquals(1, $registered->id);
        $this->assertEquals(['type' => 'power', 'value' => 'member'], $registered['create']);
        $this->assertEquals('board.notice', $registered->name);
        $this->assertEquals('instance', $registered->type);
    }

    public function testDelete()
    {
        list($conn, $query) = $this->getMocks();

        $mockRegistered = m::mock('Xpressengine\Permission\Registered');
        $mockRegistered->id = 1;

        $conn->shouldReceive('table')->andReturn($query);
        $query->shouldReceive('where')->once()->with('id', 1)->andReturn($query);
        $query->shouldReceive('delete')->once()->withNoArgs()->andReturnNull();

        $instance = new PermissionRepository($conn);
        $instance->delete($mockRegistered);
    }

    public function testFetchAncestor()
    {
        list($conn, $query) = $this->getMocks();

        $mockRegistered = m::mock('Xpressengine\Permission\Registered');
        $mockRegistered->siteKey = 'default';
        $mockRegistered->type = 'instance';
        $mockRegistered->name = 'board.notice.b1';

        $conn->shouldReceive('table')->andReturn($query);
        $query->shouldReceive('where')->once()->with('siteKey', 'default')->andReturn($query);
        $query->shouldReceive('where')->once()->with('type', 'instance')->andReturn($query);
        $query->shouldReceive('whereRaw')->once()->with("'board.notice.b1' like concat(`name`, '.', '%')")->andReturn($query);
        $query->shouldReceive('where')->once()->with('name', '<>', 'board.notice.b1')->andReturn($query);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn((object)[
            [
                'id' => 1,
                'type' => 'instance',
                'name' => 'board',
                'grants' => '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"member"}}',
            ],
            [
                'id' => 2,
                'type' => 'instance',
                'name' => 'board.notice',
                'grants' => '{"access":{"type":"power","value":"guest"},"create":{"type":"power","value":"super"}}',
            ]
        ]);

        $instance = new PermissionRepository($conn);
        $registereds = $instance->fetchAncestor($mockRegistered);

        $this->assertEquals(2, count($registereds));

        $this->assertEquals(['type' => 'power', 'value' => 'member'], $registereds[0]['create']);
        $this->assertEquals('board', $registereds[0]->name);

        $this->assertEquals(['type' => 'power', 'value' => 'super'], $registereds[1]['create']);
        $this->assertEquals('board.notice', $registereds[1]->name);
    }

    public function testFoster()
    {
        list($conn, $query) = $this->getMocks();
        $instance = new PermissionRepository($conn);

        $mockRegistered = m::mock('Xpressengine\Permission\Registered');
        $mockRegistered->siteKey = 'default';
        $mockRegistered->type = 'instance';
        $mockRegistered->name = 'prev.from';

        $conn->shouldReceive('table')->andReturn($query);
        $query->shouldReceive('where')->twice()->with('siteKey', 'default')->andReturnSelf();
        $query->shouldReceive('where')->twice()->with('type', 'instance')->andReturnSelf();
        $query->shouldReceive('where')->twice()->with(m::on(function ($closure) use ($query) {
            $query->shouldReceive('where')->once()->with('name', 'prev.from')->andReturnSelf();
            $query->shouldReceive('orWhere')->once()->with('name', 'like', 'prev.from.%')->andReturnSelf();

            call_user_func($closure, $query);

            return true;
        }))->andReturnSelf();


        $conn->shouldReceive('raw')->once()->with("substr(`name`, length('prev') + 2)")->andReturn('newName');
        $query->shouldReceive('update')->once();

        $instance->foster($mockRegistered, null);

        $conn->shouldReceive('raw')->once()->with("concat('valid.to', substr(`name`, length('prev') + 1))")->andReturn('newName');
        $query->shouldReceive('update')->once();

        $instance->foster($mockRegistered, 'valid.to');
    }

    public function testAffiliate()
    {
        list($conn, $query) = $this->getMocks();
        $instance = new PermissionRepository($conn);

        $mockRegistered = m::mock('Xpressengine\Permission\Registered');
        $mockRegistered->siteKey = 'default';
        $mockRegistered->type = 'instance';
        $mockRegistered->name = 'prev.from';

        $conn->shouldReceive('table')->andReturn($query);
        $query->shouldReceive('where')->once()->with('siteKey', 'default')->andReturnSelf();
        $query->shouldReceive('where')->once()->with('type', 'instance')->andReturnSelf();
        $query->shouldReceive('where')->once()->with(m::on(function ($closure) use ($query) {
            $query->shouldReceive('where')->once()->with('name', 'prev.from')->andReturnSelf();
            $query->shouldReceive('orWhere')->once()->with('name', 'like', 'prev.from.%')->andReturnSelf();

            call_user_func($closure, $query);

            return true;
        }))->andReturnSelf();

        $conn->shouldReceive('raw')->once()->with("concat('valid.to', '.', `name`)")->andReturn('newName');
        $query->shouldReceive('update')->once();

        $instance->affiliate($mockRegistered, 'valid.to');
    }


    private function getMocks()
    {
        return [
            m::mock('Xpressengine\Database\VirtualConnectionInterface'),
            m::mock('Xpressengine\Database\DynamicQuery')
        ];
    }
}
