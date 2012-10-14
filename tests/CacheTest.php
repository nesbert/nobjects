<?php
namespace NObjects\Tests;
use NObjects\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    private $apc;

    /**
     * @var Cache
     */
    private $mc;

    public function setUp()
    {
        $this->apc = new Cache(new Cache\Apc());

        if ($this->apc->open() || !ini_get('apc.enable_cli')) {
            $this->apc->clear();
        } else {
            $this->apc = false;
        }

        $this->mc = new Cache(new Cache\Memcache());

        if ($this->apc->open()) {
            $this->mc->clear();
        } else {
            $this->mc = false;
        }
    }

    public function tearDown()
    {
        if ($this->apc) $this->apc->clear();
        if ($this->mc) $this->mc->clear();
    }

    public function testBuildKey()
    {
        if ($this->apc) {
            $this->assertEquals('a.b.c', $this->apc->buildKey('a','b','c'));
            $this->assertEquals('My_Class.method.id.123', $this->apc->buildKey('My Class','method','id', 123));
            $this->apc->setKeyGlue('::');
            $this->assertEquals('a::b::c', $this->apc->buildKey('a','b','c'));
            $this->assertEquals('My_Class::method::id::123', $this->apc->buildKey('My Class','method','id', 123));
            $this->apc->setKeySpecialGlue('.');
            $this->assertEquals('a::b::c', $this->apc->buildKey('a','b','c'));
            $this->assertEquals('My.Class::method::id::123', $this->apc->buildKey('My Class','method','id', 123));
        }

        if ($this->mc) {
            $this->assertEquals('a.b.c', $this->mc->buildKey('a','b','c'));
            $this->assertEquals('My_Class.method.id.123', $this->mc->buildKey('My Class','method','id', 123));
            $this->mc->setKeyGlue('::');
            $this->assertEquals('a::b::c', $this->mc->buildKey('a','b','c'));
            $this->assertEquals('My_Class::method::id::123', $this->mc->buildKey('My Class','method','id', 123));
            $this->mc->setKeySpecialGlue('.');
            $this->assertEquals('a::b::c', $this->mc->buildKey('a','b','c'));
            $this->assertEquals('My.Class::method::id::123', $this->mc->buildKey('My Class','method','id', 123));
        }
    }

    public function testStrToTime()
    {
        if ($this->apc) {
            $this->assertEquals(time(), $this->apc->stringToTime('now'));
            $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->apc->stringToTime('1min'));
            $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->apc->stringToTime('1 min'));
            $this->assertEquals(time()+\NObjects\Date::HOUR, $this->apc->stringToTime('1hr'));
            $this->assertEquals(time()+\NObjects\Date::HOUR, $this->apc->stringToTime('1hour'));
            $this->assertEquals(time()+\NObjects\Date::DAY, $this->apc->stringToTime('1dy'));
            $this->assertEquals(time()+\NObjects\Date::DAY, $this->apc->stringToTime('1day'));
            $this->assertEquals(time()+\NObjects\Date::DAY*7, $this->apc->stringToTime('1week'));
            $this->assertEquals(time()+\NObjects\Date::DAY*7, $this->apc->stringToTime('1week'));
        }
        if ($this->mc) {
            $this->assertEquals(time(), $this->mc->stringToTime('now'));
            $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->mc->stringToTime('1min'));
            $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->mc->stringToTime('1 min'));
            $this->assertEquals(time()+\NObjects\Date::HOUR, $this->mc->stringToTime('1hr'));
            $this->assertEquals(time()+\NObjects\Date::HOUR, $this->mc->stringToTime('1hour'));
            $this->assertEquals(time()+\NObjects\Date::DAY, $this->mc->stringToTime('1dy'));
            $this->assertEquals(time()+\NObjects\Date::DAY, $this->mc->stringToTime('1day'));
            $this->assertEquals(time()+\NObjects\Date::DAY*7, $this->mc->stringToTime('1week'));
            $this->assertEquals(time()+\NObjects\Date::DAY*7, $this->mc->stringToTime('1week'));
        }
    }

    public function testExists()
    {
        if ($this->apc) {
            $this->assertFalse($this->apc->exists('exists'));
            $this->assertTrue($this->apc->set('exists', 123));
            $this->assertTrue($this->apc->exists('exists'));

            $keys = array('exists', 'nope1', 'nope2');
            $this->assertEquals(array('exists' => true), $this->apc->exists($keys));
        }
        if ($this->mc) {
            $this->assertFalse($this->mc->exists('exists'));
            $this->assertTrue($this->mc->set('exists', 123));
            $this->assertTrue($this->mc->exists('exists'));

            $keys = array('exists', 'nope1', 'nope2');
            $this->assertEquals(array('exists' => true), $this->mc->exists($keys));
        }
    }

    public function testGet()
    {
        if ($this->apc) {
            $this->assertFalse($this->apc->get('test'));
            $this->assertTrue($this->apc->set('test', 123));
            $this->assertEquals(123, $this->apc->get('test'));
        }
        if ($this->mc) {
            $this->assertFalse($this->mc->get('test'));
            $this->assertTrue($this->mc->set('test', 123));
            $this->assertEquals(123, $this->mc->get('test'));
        }
    }

    public function testSet()
    {
        if ($this->apc) {
            $this->assertTrue($this->apc->set('test', 456));
            $this->assertEquals(456, $this->apc->get('test'));
        }
        if ($this->mc) {
            $this->assertTrue($this->mc->set('test', 456));
            $this->assertEquals(456, $this->mc->get('test'));
        }
    }

    public function testDelete()
    {
        if ($this->apc) {
            $this->assertTrue($this->apc->set('test', 456));
            $this->assertEquals(456, $this->apc->get('test'));
            $this->assertTrue($this->apc->delete('test'));
            $this->assertFalse($this->apc->get('test'));
        }
        if ($this->mc) {
            $this->assertTrue($this->mc->set('test', 456));
            $this->assertEquals(456, $this->mc->get('test'));
            $this->assertTrue($this->mc->delete('test'));
            $this->assertFalse($this->mc->get('test'));
        }
    }

    public function testClear()
    {
        if ($this->apc) {
            $this->assertTrue($this->apc->set('test', 456));
            $this->assertEquals(456, $this->apc->get('test'));
            $this->assertTrue($this->apc->clear());
            $this->assertFalse($this->apc->get('test'));
        }
        if ($this->mc) {
            $this->assertTrue($this->mc->set('test', 456));
            $this->assertEquals(456, $this->mc->get('test'));
            $this->assertTrue($this->mc->clear());
            $this->assertFalse($this->mc->get('test'));
        }
    }

    public function testGettersSetters()
    {
        if ($this->apc) {
            $this->assertEquals($this->apc, $this->apc->setKey('a','b','c'));
            $this->assertEquals('a.b.c', $this->apc->getKey());
        }
        if ($this->mc) {
            $this->assertEquals($this->mc, $this->mc->setKey('a','b','c'));
            $this->assertEquals('a.b.c', $this->mc->getKey());
        }
    }

    public function testKeyExists()
    {
        if ($this->apc) {
            $this->assertFalse($this->apc->keyExists());
            $this->assertEquals($this->apc, $this->apc->setKey('a','b','c'));
            $this->assertTrue($this->apc->setValue(123));
            $this->assertTrue($this->apc->keyExists());
        }
        if ($this->mc) {
            $this->assertFalse($this->mc->keyExists());
            $this->assertEquals($this->mc, $this->mc->setKey('a','b','c'));
            $this->assertTrue($this->mc->setValue(123));
            $this->assertTrue($this->mc->keyExists());
        }
    }

    public function testGetValue()
    {
        if ($this->apc) {
            $this->assertEquals($this->apc, $this->apc->setKey('a','b','c'));
            $this->assertTrue($this->apc->setValue(123));
            $this->assertEquals(123, $this->apc->getValue());
        }
        if ($this->mc) {
            $this->assertEquals($this->mc, $this->mc->setKey('a','b','c'));
            $this->assertTrue($this->mc->setValue(123));
            $this->assertEquals(123, $this->mc->getValue());
        }
    }

    public function testSetValue()
    {
        if ($this->apc) {
            $this->assertEquals($this->apc, $this->apc->setKey('a','b','c'));
            $this->assertTrue($this->apc->setValue(123));
        }
        if ($this->mc) {
            $this->assertEquals($this->mc, $this->mc->setKey('a','b','c'));
            $this->assertTrue($this->mc->setValue(123));
        }
    }

    public function testDeleteKey()
    {
        if ($this->apc) {
            $this->assertFalse($this->apc->keyExists());
            $this->assertEquals($this->apc, $this->apc->setKey('a','b','c'));
            $this->assertTrue($this->apc->setValue(123));
            $this->assertTrue($this->apc->deleteKey());
            $this->assertFalse($this->apc->keyExists());
            $this->assertFalse($this->apc->getValue());
        }
        if ($this->mc) {
            $this->assertFalse($this->mc->keyExists());
            $this->assertEquals($this->mc, $this->mc->setKey('a','b','c'));
            $this->assertTrue($this->mc->setValue(123));
            $this->assertTrue($this->mc->deleteKey());
            $this->assertFalse($this->mc->keyExists());
            $this->assertFalse($this->mc->getValue());
        }
    }

    public function testSetAdapter()
    {
        if ($this->apc) {
            $o = new Cache();
            $this->assertEquals($o, $o->setAdapter(new Cache\Apc()));
        }
        if ($this->mc) {
            $o = new Cache();
            $this->assertEquals($o, $o->setAdapter(new Cache\Memcache()));
        }
    }

    public function testGetAdapter()
    {
        if ($this->apc) {
            $this->assertEquals(new Cache\Apc(), $this->apc->getAdapter());
        }
        if ($this->mc) {
            $adapter = new Cache\Memcache();
            $adapter->open();
            $this->assertEquals($adapter, $this->mc->getAdapter());
        }
    }
}
