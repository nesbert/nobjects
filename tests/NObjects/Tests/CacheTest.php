<?php
namespace NObjects\Tests;

use NObjects\Cache;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState false
 */
class CacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Cache
     */
    private $apc;

    public function setUp()
    {
        try {
            $this->apc = new Cache(new Cache\Apc());
        } catch (\RuntimeException $exception) {
            $this->markTestSkipped('APC extension is not available: '. $exception->getMessage());
        }

        if ($this->apc->open() || !ini_get('apc.enable_cli')) {
            $this->apc->clear();
        } else {
            $this->apc = false;
        }
    }

    public function tearDown()
    {
        if ($this->apc) {
            $this->apc->clear();
        }
    }

    public function testBuildKey()
    {
        if ($this->apc) {
            $this->assertEquals('a.b.c', $this->apc->buildKey('a', 'b', 'c'));
            $this->assertEquals('My_Class.method.id.123', $this->apc->buildKey('My Class', 'method', 'id', 123));
            $this->apc->setKeyGlue('::');
            $this->assertEquals('a::b::c', $this->apc->buildKey('a', 'b', 'c'));
            $this->assertEquals('My_Class::method::id::123', $this->apc->buildKey('My Class', 'method', 'id', 123));
            $this->apc->setKeySpecialGlue('.');
            $this->assertEquals('a::b::c', $this->apc->buildKey('a', 'b', 'c'));
            $this->assertEquals('My.Class::method::id::123', $this->apc->buildKey('My Class', 'method', 'id', 123));
            $this->assertEquals($this->apc, $this->apc->setKeySpecial(array('/')));
            $this->assertEquals('My Class::method::id::123', $this->apc->buildKey('My Class', 'method', 'id', 123));
        }
    }

    public function testStrToTime()
    {
        if ($this->apc) {
            $this->assertEquals(time(), $this->apc->stringToTime('now'));
            $this->assertEquals(strtotime(date('Y-m-d 23:59:59')), $this->apc->stringToTime('midnite'));
            $this->assertEquals(
                strtotime(
                    date('Y-m-d 00:00:00', strtotime('+1 day'))
                ),
                $this->apc->stringToTime('tomorrow')
            );
            $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->apc->stringToTime('1min'));
            $this->assertEquals(time()+\NObjects\Date::MINUTE, $this->apc->stringToTime('1 min'));
            $this->assertEquals(time()+\NObjects\Date::HOUR, $this->apc->stringToTime('1hr'));
            $this->assertEquals(time()+\NObjects\Date::HOUR, $this->apc->stringToTime('1hour'));
            // Range is present to make tests resilient to DST changes
            $this->assertThat(
                $this->apc->stringToTime('1dy'),
                $this->logicalAnd(
                    $this->greaterThanOrEqual(time()+\NObjects\Date::DAY - \NObjects\Date::HOUR),
                    $this->lessThanOrEqual(time()+\NObjects\Date::DAY + \NObjects\Date::HOUR)
                )
            );
            $this->assertThat(
                $this->apc->stringToTime('1day'),
                $this->logicalAnd(
                    $this->greaterThanOrEqual(time()+\NObjects\Date::DAY - \NObjects\Date::HOUR),
                    $this->lessThanOrEqual(time()+\NObjects\Date::DAY + \NObjects\Date::HOUR)
                )
            );
            $this->assertThat(
                $this->apc->stringToTime('1week'),
                $this->logicalAnd(
                    $this->greaterThanOrEqual(time()+\NObjects\Date::DAY*7 - \NObjects\Date::HOUR),
                    $this->lessThanOrEqual(time()+\NObjects\Date::DAY*7 + \NObjects\Date::HOUR)
                )
            );
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
    }

    public function testGet()
    {
        if ($this->apc) {
            $this->assertFalse($this->apc->get('test'));
            $this->assertTrue($this->apc->set('test', 123));
            $this->assertEquals(123, $this->apc->get('test'));
        }
    }

    public function testSet()
    {
        if ($this->apc) {
            $this->assertTrue($this->apc->set('test', 456));
            $this->assertEquals(456, $this->apc->get('test'));
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
    }

    public function testClear()
    {
        if ($this->apc) {
            $this->assertTrue($this->apc->set('test', 456));
            $this->assertEquals(456, $this->apc->get('test'));
            $this->assertTrue($this->apc->clear());
            $this->assertFalse($this->apc->get('test'));
        }
    }

    public function testGettersSetters()
    {
        if ($this->apc) {
            $this->assertEquals($this->apc, $this->apc->setKey('a', 'b', 'c'));
            $this->assertEquals('a.b.c', $this->apc->getKey());
        }
    }

    public function testKeyExists()
    {
        if ($this->apc) {
            $this->assertFalse($this->apc->keyExists());
            $this->assertEquals($this->apc, $this->apc->setKey('a', 'b', 'c'));
            $this->assertTrue($this->apc->setValue(123));
            $this->assertTrue($this->apc->keyExists());
        }
    }

    public function testGetValue()
    {
        if ($this->apc) {
            $this->assertEquals($this->apc, $this->apc->setKey('a', 'b', 'c'));
            $this->assertTrue($this->apc->setValue(123));
            $this->assertEquals(123, $this->apc->getValue());
        }
    }

    public function testSetValue()
    {
        if ($this->apc) {
            $this->assertEquals($this->apc, $this->apc->setKey('a', 'b', 'c'));
            $this->assertTrue($this->apc->setValue(123));
        }
    }

    public function testDeleteKey()
    {
        if ($this->apc) {
            $this->assertFalse($this->apc->keyExists());
            $this->assertEquals($this->apc, $this->apc->setKey('a', 'b', 'c'));
            $this->assertTrue($this->apc->setValue(123));
            $this->assertTrue($this->apc->deleteKey());
            $this->assertFalse($this->apc->keyExists());
            $this->assertFalse($this->apc->getValue());
        }
    }

    public function testSetAdapter()
    {
        if ($this->apc) {
            $o = new Cache();
            $this->assertEquals($o, $o->setAdapter(new Cache\Apc()));
        }
    }

    public function testGetAdapter()
    {
        if ($this->apc) {
            $this->assertInstanceOf('NObjects\\Cache\\Apc', $this->apc->getAdapter());
        }
    }

    public function testEmptyAdapter()
    {
        $cache = new Cache();
        $this->assertFalse($cache->exists('test'));
        $this->assertFalse($cache->get('test'));
        $this->assertFalse($cache->set('test', 123));
        $this->assertFalse($cache->delete('test'));
        $this->assertFalse($cache->clear());
        $this->assertFalse($cache->open());

        $this->assertEquals($cache, $cache->setKey('test'));
        $this->assertFalse($cache->keyExists());
        $this->assertFalse($cache->getValue());
        $this->assertFalse($cache->setValue(123));
        $this->assertFalse($cache->deleteKey());
    }
}
