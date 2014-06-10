<?php
namespace NObjects\Tests\Cache\Memcache;

use NObjects\Cache\Memcache\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Server
     */
    private $o;

    /**
     * @var array
     */
    private $x;

    public function setUp()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped('Memcache extension is not available.');
        }

        $this->o = new Server();
        $this->x = array(
            'scheme' => 'udp',
            'host' => 'xwing',
            'port' => 11212,
            'persistent' => false,
            'weight' => 5,
            'timeout' => 2,
            'retry_interval' => 10,
        );
    }

    public function testGettersSetters()
    {
        $this->assertEquals('tcp', $this->o->scheme);
        $this->assertEquals('127.0.0.1', $this->o->host);
        $this->assertEquals(11211, $this->o->port);
        $this->assertEquals(true, $this->o->persistent);
        $this->assertEquals(1, $this->o->weight);
        $this->assertEquals(1, $this->o->timeout);
        $this->assertEquals(15, $this->o->retry_interval);
        $this->assertNull($this->o->invalid);

        $this->o = new Server($this->x);

        $this->assertEquals($this->x['scheme'], $this->o->scheme);
        $this->assertEquals($this->x['host'], $this->o->host);
        $this->assertEquals($this->x['port'], $this->o->port);
        $this->assertEquals($this->x['persistent'], $this->o->persistent);
        $this->assertEquals($this->x['weight'], $this->o->weight);
        $this->assertEquals($this->x['timeout'], $this->o->timeout);
        $this->assertEquals($this->x['retry_interval'], $this->o->retry_interval);
    }

    public function testLoad()
    {
        $this->o = new Server();
        $this->assertEquals('tcp', $this->o->scheme);
        $this->assertEquals('127.0.0.1', $this->o->host);
        $this->assertEquals(11211, $this->o->port);
        $this->assertEquals(true, $this->o->persistent);
        $this->assertEquals(1, $this->o->weight);
        $this->assertEquals(1, $this->o->timeout);
        $this->assertEquals(15, $this->o->retry_interval);

        $this->assertEquals($this->o, $this->o->load($this->x));
        $this->assertEquals($this->x['scheme'], $this->o->scheme);
        $this->assertEquals($this->x['host'], $this->o->host);
        $this->assertEquals($this->x['port'], $this->o->port);
        $this->assertEquals($this->x['persistent'], $this->o->persistent);
        $this->assertEquals($this->x['weight'], $this->o->weight);
        $this->assertEquals($this->x['timeout'], $this->o->timeout);
        $this->assertEquals($this->x['retry_interval'], $this->o->retry_interval);

    }

    public function testPath()
    {
        $this->o = new Server();
        $this->assertEquals('tcp://127.0.0.1:11211?persistent=1&weight=1&timeout=1&retry_interval=15', $this->o->path());

        $this->o = new Server($this->x);
        $this->assertEquals('udp://xwing:11212?persistent=0&weight=5&timeout=2&retry_interval=10', $this->o->path());
    }

    public function testIsOnline()
    {
        $this->o = new Server(array(
            'port' => 11122
        ));
        $this->assertFalse($this->o->isOnline());

        if (extension_loaded('memcache')) {
            $this->o = new Server(array(
                'port' => 11211
            ));
            if ($this->o->isOnline()) {
                $this->assertTrue($this->o->isOnline());
            }
        }
    }
}
