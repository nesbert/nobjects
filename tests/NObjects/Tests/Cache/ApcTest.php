<?php
namespace NObjects\Tests\Cache;

use NObjects\Cache;

class ApcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache\Apc
     */
    private $o;

    public function setUp()
    {
        if (!function_exists('apc_add') && !function_exists('apcu_add')) {
            $this->markTestSkipped('APC extension is not available (apc_add and apcu_add not available)');
        }

        $this->o = new Cache\Apc();

        if (!$this->o->open()) {
            $this->markTestSkipped('APC extension is not available.');
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
}
