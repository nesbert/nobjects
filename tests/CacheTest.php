<?php
namespace NObjects\Tests;
use NObjects\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    private $o;

    public function setUp()
    {
        $this->o = new Cache(new Cache\Apc());

        if (!$this->o->open() && ini_get('apc.enable_cli')) {
            $this->markTestSkipped('APC extension is not available.');
        }
    }

    public function testBuildKey()
    {
        $this->assertEquals('a.b.c', $this->o->buildKey('a','b','c'));
        $this->assertEquals('My_Class.method.id.123', $this->o->buildKey('My Class','method','id', 123));
        $this->o->setKeyGlue('::');
        $this->assertEquals('a::b::c', $this->o->buildKey('a','b','c'));
        $this->assertEquals('My_Class::method::id::123', $this->o->buildKey('My Class','method','id', 123));
        $this->o->setKeySpecialGlue('.');
        $this->assertEquals('a::b::c', $this->o->buildKey('a','b','c'));
        $this->assertEquals('My.Class::method::id::123', $this->o->buildKey('My Class','method','id', 123));
    }

    public function testStringToTime()
    {
        $this->assertEquals(time(), $this->o->stringToTime('now'));
        $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->o->stringToTime('1min'));
        $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->o->stringToTime('1 min'));
        $this->assertEquals(time()+\NObjects\Date::HOUR, $this->o->stringToTime('1hr'));
        $this->assertEquals(time()+\NObjects\Date::HOUR, $this->o->stringToTime('1hour'));
        $this->assertEquals(time()+\NObjects\Date::DAY, $this->o->stringToTime('1dy'));
        $this->assertEquals(time()+\NObjects\Date::DAY, $this->o->stringToTime('1day'));
        $this->assertEquals(time()+\NObjects\Date::DAY*7, $this->o->stringToTime('1week'));
        $this->assertEquals(time()+\NObjects\Date::DAY*7, $this->o->stringToTime('1week'));
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

    public function testGettersSetters()
    {
        $this->assertEquals($this->o, $this->o->setKey('a','b','c'));
        $this->assertEquals('a.b.c', $this->o->getKey());
    }

    public function testKeyExists()
    {
        $this->assertFalse($this->o->keyExists());
        $this->assertEquals($this->o, $this->o->setKey('a','b','c'));
        $this->assertTrue($this->o->setValue(123));
        $this->assertTrue($this->o->keyExists());
    }

    public function testGetValue()
    {
        $this->assertEquals($this->o, $this->o->setKey('a','b','c'));
        $this->assertTrue($this->o->setValue(123));
        $this->assertEquals(123, $this->o->getValue());
    }

    public function testSetValue()
    {
        $this->assertEquals($this->o, $this->o->setKey('a','b','c'));
        $this->assertTrue($this->o->setValue(123));
    }

    public function testDeleteKey()
    {
        $this->assertFalse($this->o->keyExists());
        $this->assertEquals($this->o, $this->o->setKey('a','b','c'));
        $this->assertTrue($this->o->setValue(123));
        $this->assertTrue($this->o->deleteKey());
        $this->assertFalse($this->o->keyExists());
        $this->assertFalse($this->o->getValue());
    }
}
