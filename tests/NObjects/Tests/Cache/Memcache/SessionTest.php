<?php
namespace NObjects\Tests\Cache\Memcache;

use NObjects\Cache\Memcache\Session;

/**
 * @runTestsInSeparateProcesses
 * @requires extension memcache
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
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

    public function testSessionOverRide()
    {
        $allowFailOver = true;
        $maxFailOverAttempts = 20;
        $chunkSize = 8192;
        $defaultPort = 11211;
        $hashStrategy = 'consistent';
        $hashFunction = 'crc32';

        $this->o = new Session('tcp://localhost');

        // test defaults
        $this->assertEquals($allowFailOver, $this->o->getAllowFailOver());
        $this->assertEquals($maxFailOverAttempts, $this->o->getMaxFailOverAttempts());
        $this->assertEquals($chunkSize, $this->o->getChunkSize());
        $this->assertEquals($defaultPort, $this->o->getDefaultPort());
        $this->assertEquals($hashStrategy, $this->o->getHashStrategy());
        $this->assertEquals($hashFunction, $this->o->getHashFunction());

        $allowFailOver = false;
        $this->assertEquals($this->o, $this->o->setAllowFailOver($allowFailOver));
        $this->assertEquals($allowFailOver, $this->o->getAllowFailOver());

        $maxFailOverAttempts = 5;
        $this->assertEquals($this->o, $this->o->setMaxFailOverAttempts($maxFailOverAttempts));
        $this->assertEquals($maxFailOverAttempts, $this->o->getMaxFailOverAttempts());

        $chunkSize = 32768;
        $this->assertEquals($this->o, $this->o->setChunkSize($chunkSize));
        $this->assertEquals($chunkSize, $this->o->getChunkSize());

        $defaultPort = 11212;
        $this->assertEquals($this->o, $this->o->setDefaultPort($defaultPort));
        $this->assertEquals($defaultPort, $this->o->getDefaultPort());

        $hashStrategy = 'standard';
        $this->assertEquals($this->o, $this->o->setHashStrategy($hashStrategy));
        $this->assertEquals($hashStrategy, $this->o->getHashStrategy());

        $hashFunction = 'fnv';
        $this->assertEquals($this->o, $this->o->setHashFunction($hashFunction));
        $this->assertEquals($hashFunction, $this->o->getHashFunction());

        // test ini settings

        $this->assertEquals('files', ini_get('session.save_handler'));
        $this->assertNotRegExp('/^tcp/', ini_get('session.save_path'));

        // if no extension return
        if (!class_exists('\Memcache')) {
            return;
        }

        // reset & initialize
        $this->o = new Session('tcp://'.self::$memcachedPath);
        $this->o->init();

        $this->assertEquals('memcache', ini_get('session.save_handler'));
        $this->assertEquals(
            'tcp://'.self::$memcachedPath.'?persistent=1&weight=1&timeout=1&retry_interval=15',
            ini_get('session.save_path')
        );

        $this->assertEquals(1, ini_get('memcache.allow_failover'));
        $this->assertEquals(20, ini_get('memcache.max_failover_attempts'));
        $this->assertEquals(11211, ini_get('memcache.default_port'));
        $this->assertEquals('consistent', ini_get('memcache.hash_strategy'));
        $this->assertEquals('crc32', ini_get('memcache.hash_function'));


        // reset, override & initialize
        $this->o = new Session('tcp://'.self::$memcachedPath);
        $this->o
            ->setAllowFailOver($allowFailOver)
            ->setMaxFailOverAttempts($maxFailOverAttempts)
            ->setChunkSize($chunkSize)
            ->setDefaultPort($defaultPort)
            ->setHashStrategy($hashStrategy)
            ->setHashFunction($hashFunction)
            ->init();

        $this->assertEquals('memcache', ini_get('session.save_handler'));
        $this->assertEquals(
            'tcp://'.self::$memcachedPath . '?persistent=1&weight=1&timeout=1&retry_interval=15',
            ini_get('session.save_path')
        );

        $this->assertEquals($allowFailOver, ini_get('memcache.allow_failover'));
        $this->assertEquals($maxFailOverAttempts, ini_get('memcache.max_failover_attempts'));
        $this->assertEquals($defaultPort, ini_get('memcache.default_port'));
        $this->assertEquals($hashStrategy, ini_get('memcache.hash_strategy'));
        $this->assertEquals($hashFunction, ini_get('memcache.hash_function'));
    }
}
