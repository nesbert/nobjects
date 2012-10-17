<?php
namespace NObjects\Cache\Memcache;

/**
 * Data object class for a memcache cluster.
 *
 * @author Nesbert Hidalgo
 **/
class Data extends Cluster implements \NObjects\Cache\Adapter
{
    /**
     * Memcache resource object.
     *
     * @var \Memcache
     **/
    private $memcache;

    /**
     *
     * @var bool
     */
    private $compress = false;

    /**
     * @var bool
     */
    private $clusterOnline;

    /**
     * @param mixed $servers
     * @param bool $compress
     * @see Cluster::__construct
     */
    public function __construct($servers = 'tcp://localhost', $compress = false)
    {
        parent::__construct($servers);
        if ($compress) {
            $this->compress = \MEMCACHE_COMPRESSED;
        }
    }

    /**
     * Check if a key exists.
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        if ($this->open()) {
            // if array check each key
            if (is_array($key)) {
                $out = array();
                foreach ($key as $k) {
                    if ($this->exists($k)) {
                        $out[$k] = true;
                    }
                }
                return $out;
            } elseif (empty($key)) {
                return false;
            }

            // try adding if true it does not exists
            if ($return = $this->memcache->add($key, 1)) {
                $this->memcache->delete($key, 0);
            }
            return $return === false;
        }
        return false;
    }

    /**
     * Attempt to save a key value pair to memcache.
     *
     * @param string $key index to use into memcache
     * @param mixed $value data to store for that index
     * @param int $expire
     * @return bool
     */
    public function set($key, $value, $expire = 0)
    {
        if ($this->open()) {
            $compress = is_scalar($value) ? null : $this->compress;
            return $this->memcache->set($key, $value, $compress, $expire);
        }
        return false;
    }

    /**
     * Attempt to retrieve a key from the memcache cluster.
     *
     * @param string $key
     * @return bool
     *
     */
    public function get($key)
    {
        if ($this->open()) {
            return $this->memcache->get($key);
        }
        return false;
    }

    /**
     * Attempt to remove a key value pair from memcache.
     *
     * @param string $key index to remove from memcache
     * @param int $delay - Length of time before deletion. (0 = immediately; n = seconds)
     * @return bool
     **/
    public function delete($key, $delay = 0)
    {
        if ($this->open()) {
            return $this->memcache->delete($key, $delay);
        }
        return false;
    }

    /**
     * Get stats about cluster
     *
     * @param bool $extended detailed stats?
     * @return mixed - array on success; false on failure
     **/
    public function stats($extended = true)
    {
        if ($this->open()) {
            if ($extended) {
                return $this->memcache->getExtendedStats();
            } else {
                return $this->memcache->getStats();
            }
        }
        return false;
    }

    /**
     * Attempt to open the connection.
     *
     * @return bool
     **/
    public function open()
    {
        if (!extension_loaded('memcache')) return false;

        // only check cluster once
        if (!is_null($this->clusterOnline)) {
            return $this->clusterOnline;
        }

        // if this is not a memcache object or the status of the server is failed
        if (empty($this->memcache)) {
            //create a new memcache object
            if ($this->memcache = $this->getMemcacheObject()) {
                // try to set a key to determine if we can connect
                try {
                    // make sure one node is the cluster is online
                    $this->clusterOnline = $this->isOnline();
                } catch (\Exception $e) {
                }
            }
        }

        return $this->clusterOnline;
    }

    /**
     * Clear/flush all cached data.
     *
     * @return bool
     */
    public function clear()
    {
        if ($this->open()) {
            return $this->memcache->flush();
        }
        return false;
    }
}
