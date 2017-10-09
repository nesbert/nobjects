<?php
namespace NObjects\Tests\Cache\Memcache;

use NObjects\Cache\Memcache\Cluster;
use NObjects\Cache\Memcache\Server;

class ClusterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cluster
     */
    public $o;

    public function setUp()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped('Memcache extension is not available.');
        }

        $this->o = new Cluster('tcp://127.0.0.1?port=11211');

        if (!$this->o->isOnline()) {
            $this->markTestSkipped('Memcache extension is not available.');
        }
    }

    public function testLoad()
    {
        // string
        $this->assertEquals(1, count($this->o->getServers()));
        $this->assertEquals(new Server(), current($this->o->getServers()));

        // single server
        $s = new Server(array(
            'port' => 11212
        ));
        $c = new Cluster($s);
        $c->load($s);
        $this->assertEquals($s, current($c->getServers()));

        // multi servers
        $s1 = new Server(array('host' => 'vader'));
        $s2 = new Server(array('host' => 'maul'));
        $s3 = new Server(array('host' => 'malgus'));
        $c = new Cluster();
        $c->load(array($s1, $s2, $s3));
        $servers = $c->getServers();
        $this->assertEquals(3, count($servers));
        $this->assertEquals($s1, $servers[0]);
        $this->assertEquals($s2, $servers[1]);
        $this->assertEquals($s3, $servers[2]);

        $c = new Cluster();
        $c->load('tcp://luke,tcp://han');
        $servers = $c->getServers();
        $this->assertEquals(2, count($servers));
        $this->assertEquals(new Server(array('host'=>'luke')), $servers[0]);
        $this->assertEquals(new Server(array('host'=>'han')), $servers[1]);
    }

    public function testAddServer()
    {
        $s = new Server(array(
            'port' => 11212
        ));
        $c = new Cluster();
        $this->assertEquals($c, $c->addServer($s));
        $this->assertEquals($s, current($c->getServers()));
    }

    public function testGetServers()
    {
        $this->assertEquals(1, count($this->o->getServers()));
        $this->assertEquals(new Server(), current($this->o->getServers()));
    }

    public function testSavePath()
    {
        $this->assertEquals(
            'tcp://127.0.0.1:11211?persistent=1&weight=1&timeout=1&retry_interval=15',
            $this->o->savePath()
        );

        $s1 = new Server(array('host' => 'vader'));
        $s2 = new Server(array('host' => 'maul'));
        $s3 = new Server(array('host' => 'malgus'));
        $c = new Cluster(array($s1, $s2, $s3));
        $this->assertEquals(
            'tcp://vader:11211?persistent=1&weight=1&timeout=1&retry_interval=15, ' .
            'tcp://maul:11211?persistent=1&weight=1&timeout=1&retry_interval=15, ' .
            'tcp://malgus:11211?persistent=1&weight=1&timeout=1&retry_interval=15',
            $c->savePath()
        );
    }

    public function testGetMemcacheObject()
    {
        if (!extension_loaded('memcache')) {
            return;
        }

        $this->assertTrue($this->o->getMemcacheObject() instanceof \Memcache);
        $this->assertTrue($this->o->getMemcacheObject() === $this->o->getMemcacheObject());
    }

    public function testFlush()
    {
        if (!extension_loaded('memcache')) {
            return;
        }

        $this->assertTrue($this->o->flush());
    }

    public function testIsOnline()
    {
        if (!extension_loaded('memcache')) {
            return;
        }

        $this->assertTrue($this->o->isOnline());
        $this->assertTrue($this->o->isOnline(true));

        $o = new Cluster('tcp://localhost,tcp://invalid');
        $this->assertTrue($o->isOnline());
        $this->assertFalse($o->isOnline(true));
    }

    public function testStatus()
    {
        if (!extension_loaded('memcache')) {
            return;
        }

        $this->assertEquals(array('127.0.0.1:11211' => true), $this->o->status());

        $this->expectOutputString('Memcache on 127.0.0.1:11211 <span style="color:green;">[Ok]</span>');
        $this->o->status(true);
    }

    public function testStats()
    {
        if (!extension_loaded('memcache')) {
            return;
        }

        $stats = $this->o->stats();
        $this->assertTrue(isset($stats['127.0.0.1:11211']));
        $this->assertTrue(is_array($stats['127.0.0.1:11211']));
        $this->greaterThanOrEqual(10, count($stats['127.0.0.1:11211']));
    }

    public function testMonitorStats()
    {
        if (!extension_loaded('memcache')) {
            return;
        }

        $stats = $this->o->monitorStats();
        $this->assertTrue(isset($stats->servers));
        $this->assertTrue(isset($stats->stats));
        $this->assertTrue(isset($stats->totals));
    }

    public function testGetClusterConstants()
    {
        $this->assertEquals(array(), $this->o->getClusterConstants());

        define('MEMCACHE_CLUSTER_LIGHTSIDE', 'tcp://yoda,tcp://obi-wan');
        define('MEMCACHE_CLUSTER_DARKSIDE', 'tcp://sidious,tcp://vader');
        $this->assertEquals(array(
            'MEMCACHE_CLUSTER_LIGHTSIDE' => array('name' => 'LIGHTSIDE', 'value' => 'tcp://yoda,tcp://obi-wan'),
            'MEMCACHE_CLUSTER_DARKSIDE'  => array('name' => 'DARKSIDE', 'value' => 'tcp://sidious,tcp://vader')
        ), $this->o->getClusterConstants());
    }
}
