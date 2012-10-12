<?php
namespace NObjects\Memcache;

/**
 * Memcache session class used to set runtime configs to override
 * PHP session handling.
 *
 * <code>
 * $sessionCluster = new Session('tcp://domain1,tcp://domain2,tcp://domain3');
 * $sessionCluster->setDefaultPort(123456)->init();
 * </code>
 *
 * @author Nesbert Hidalgo
 **/
class Session extends Cluster
{
    /**
     * Set whether to transparently fail over to other servers on errors.
     *
     * @var bool
     **/
    private $allowFailOver = true;

    /**
     * Set how many servers to try when setting and getting data. Used only in
     * conjunction with memcache.allow_failover.
     *
     * @var int
     **/
    private $maxFailOverAttempts = 20;

    /**
     * Data will be transferred in chunks of this size, setting the value lower
     * requires more network writes. Try increasing this value to 32768 if noticing
     * otherwise inexplicable slowdowns.
     *
     * @var int
     **/
    private $chunkSize = 8192;

    /**
     * The default TCP port number to use when connecting to the memcached server
     * if no other port is specified.
     *
     * @var int
     **/
    private $defaultPort = 11211;

    /**
     * Controls which strategy to use when mapping keys to servers. Set this value to
     * consistent to enable consistent hashing which allows servers to be added or
     * removed from the pool without causing keys to be remapped. Setting this value
     * to standard results in the old strategy being used.
     *
     * @var string
     **/
    private $hashStrategy = "standard"; // {consistent,standard}

    /**
     * Controls which hash function to apply when mapping keys to servers, crc32 uses the
     * standard CRC32 hash while fnv uses FNV-1a.
     *
     * @var string
     **/
    private $hashFunction = "crc32";

    /**
     * Set the default TCP port number to use when connecting to the memcached server
     * if no other port is specified.
     *
     * @return Session
     **/
    public function init()
    {
        // Override the default session save_handler
        ini_set('session.save_handler', 'memcache');

        // Set session save path
        ini_set('session.save_path', $this->savePath());

        // Use server pool if primary is unavailable
        ini_set('memcache.allow_failover', $this->getAllowFailOver());

        // Additional custom settings
        ini_set('memcache.max_failover_attempts', $this->getMaxFailOverAttempts());
        ini_set('memcache.chunk_size', $this->getChunkSize());
        ini_set('memcache.hash_strategy', $this->getHashStrategy());
        ini_set('memcache.hash_function', $this->getHashFunction());
        ini_set('memcache.default_port', $this->getDefaultPort());

        return $this;
    }

    /**
     * @param bool $allowFailOver
     * @return Session
     */
    public function setAllowFailOver($allowFailOver)
    {
        $this->allowFailOver = (bool)$allowFailOver;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowFailOver()
    {
        return $this->allowFailOver;
    }

    /**
     * @param int $chunkSize
     * @return Session
     */
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = (int)$chunkSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * @param int $defaultPort
     * @return Session
     */
    public function setDefaultPort($defaultPort)
    {
        $this->defaultPort = (int)$defaultPort;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultPort()
    {
        return $this->defaultPort;
    }

    /**
     * @param string $hashFunction
     * @return Session
     */
    public function setHashFunction($hashFunction)
    {
        $this->hashFunction = $hashFunction;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashFunction()
    {
        return $this->hashFunction;
    }

    /**
     * @param string $hashStrategy
     * @return Session
     */
    public function setHashStrategy($hashStrategy)
    {
        $this->hashStrategy = $hashStrategy;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashStrategy()
    {
        return $this->hashStrategy;
    }

    /**
     * @param int $maxFailOverAttempts
     * @return Session
     */
    public function setMaxFailOverAttempts($maxFailOverAttempts)
    {
        $this->maxFailOverAttempts = (int)$maxFailOverAttempts;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxFailOverAttempts()
    {
        return $this->maxFailOverAttempts;
    }
}
