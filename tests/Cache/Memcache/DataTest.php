<?php
namespace NObjects\Tests\Cache\Memcache;

use NObjects\Cache\Memcache\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Data
     */
    private $o;

    public function setUp()
    {
        $this->o = new Data('tcp://localhost', true);

        if (!$this->o->open()) {
            $this->markTestSkipped('Memcache extension is not available.');
        }
    }

    public function testSet()
    {
        $this->o->flush();
        $this->assertTrue($this->o->set('test123', 123));
        $this->assertEquals(123, $this->o->get('test123'));
    }

    public function testGet()
    {
        $this->assertTrue($this->o->set('test456', 456));
        $this->assertEquals(456, $this->o->get('test456'));
    }

    public function testExists()
    {
        $this->assertTrue($this->o->exists('test123'));
        $this->assertTrue($this->o->exists('test456'));
        $this->assertFalse($this->o->exists('test789'));
        $this->assertFalse($this->o->exists(null));

        $this->assertEquals(array('test123' => true,'test456' => true), $this->o->exists(array('test123','test456','test789')));
    }

    public function testDelete()
    {
        $this->assertEquals(123, $this->o->get('test123'));
        $this->assertEquals(456, $this->o->get('test456'));
        $this->assertTrue($this->o->delete('test123'));
        $this->assertTrue($this->o->delete('test456'));
        $this->assertFalse($this->o->delete('test789'));
        $this->assertFalse($this->o->get('test123'));
        $this->assertFalse($this->o->get('test456'));
    }

    public function testOpen()
    {
        $this->assertTrue($this->o->open());
        $z = new Data('tcp://localhost:55415');
        $this->assertFalse($z->open());
    }

    public function testClear()
    {
        $this->assertTrue($this->o->clear());
        $z = new Data('tcp://localhost:55415');
        $this->assertFalse($z->clear());
    }

    public function testStats()
    {
        $stats = $this->o->stats();
        $this->assertTrue(is_array($stats));
        $this->assertEquals(1, count($stats));
        $this->assertTrue(is_array($stats['localhost:11211']));
        $this->assertEquals(48, count($stats['localhost:11211']));

        $stats = $this->o->stats(false);
        $this->assertTrue(is_array($stats));
        $this->assertEquals(48, count($stats));
    }

    public function testClosed()
    {
        $o = new Data('tcp://invalid');
        $this->assertFalse($o->exists('test123'));
        $this->assertFalse($o->set('test123', 123));
        $this->assertFalse($o->get('test123'));
        $this->assertFalse($o->delete('test123'));
        $this->assertFalse($o->clear());
        $this->assertFalse($o->stats());
    }

}
