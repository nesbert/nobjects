<?php
namespace NObjects\Tests\Cache;

use NObjects\Cache\Memcache;

/**
 * @runTestsInSeparateProcesses
 * @requires extension memcache
 */
class MemcacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Memcache
     */
    private $o;

    /**
     * @var string
     */
    private static $memcachedPath;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $host = getenv('PHPUNIT_MEMCACHED_SERVER_HOST') ? getenv('PHPUNIT_MEMCACHED_SERVER_HOST') : 'localhost';
        $port = getenv('PHPUNIT_MEMCACHED_SERVER_PORT') ? getenv('PHPUNIT_MEMCACHED_SERVER_PORT') : 11211;

        static::$memcachedPath = $host.':'.$port;
    }

    public function setUp()
    {
        $this->o = new Memcache('tcp://' . static::$memcachedPath);

        if (!$this->o->open()) {
            $this->markTestSkipped('Memcache server is not available.');
        }
    }

    public function testExists()
    {
        $this->assertFalse($this->o->exists('exists'));
        $this->assertTrue($this->o->set('exists', 123));
        $this->assertTrue($this->o->exists('exists'));

        $keys = array('exists', 'nope1', 'nope2');
        $this->assertEquals(array('exists' => true), $this->o->exists($keys));
    }

    public function testGet()
    {
        $this->assertFalse($this->o->get('test'));
        $this->assertTrue($this->o->set('test', 123));
        $this->assertEquals(123, $this->o->get('test'));
    }

    public function testSet()
    {
        $this->assertTrue($this->o->set('test', 456));
        $this->assertEquals(456, $this->o->get('test'));
    }

    public function testDelete()
    {
        $this->assertEquals(456, $this->o->get('test'));
        $this->assertTrue($this->o->delete('test'));
        $this->assertFalse($this->o->get('test'));
    }

    public function testClear()
    {
        $this->assertTrue($this->o->set('test', 456));
        $this->assertTrue($this->o->clear());
        $this->assertFalse($this->o->get('test'));
    }

    public function testOpen()
    {
        $this->assertTrue($this->o->open());
        $z = new Memcache('tcp://localhost:55415');
        $this->assertFalse($z->open());
    }
}
