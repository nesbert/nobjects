<?php
namespace NObjects\Cache;

use NObjects\Cache\Memcache\Data;

class Memcache extends Base implements Adapter
{
    /**
     * NObjects\Cache\Memcache\Data resource object.
     *
     * @var Data
     **/
    private $data;

    /**
     * @param mixed $servers
     * @param bool $compress
     * @see Cluster::__construct
     */
    public function __construct($servers = 'tcp://localhost', $compress = false)
    {
        $this->setData(new Data($servers, $compress));
    }

    /**
     * Check if key exists. $key a string, or an array of strings,
     * that contain keys. If an array was passed to $key, then an array
     * is returned that contains all existing keys, or an empty array
     * if no matches
     *
     * @param string/array $key
     * @return mixed
     */
    public function exists($key)
    {
        if (!$this->open()) {
            return false;
        }
        return $this->getData()->exists($key);
    }

    /**
     * Get/check if key exists.
     *
     * @param string/array $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->open()) {
            return false;
        }
        return $this->getData()->get($key);
    }

    /**
     * Set a key, value and ttl.
     *
     * @param string/array $key
     * @param $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 0)
    {
        if (!$this->open()) {
            return false;
        }
        return $this->getData()->set($key, $value, $this->stringToTime($ttl));
    }

    /**
     * Delete cache by $key. Optional $delay seconds if supported.
     *
     * @param string/array $key
     * @param int $delay
     * @return bool
     */
    public function delete($key, $delay = 0)
    {
        if (!$this->open()) {
            return false;
        }
        return $this->getData()->delete($key, $delay);
    }

    /**
     * Clear/flush all cached data.
     *
     * @return bool
     */
    public function clear()
    {
        if (!$this->open()) {
            return false;
        }
        return $this->getData()->flush();
    }

    /**
     * Open a connection to cache.
     *
     * @return bool
     */
    public function open()
    {
        return $this->getData()->open();
    }

    // setters & getters

    /**
     * @param Data $data
     * @return Data
     */
    public function setData(Data $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return Data
     */
    public function getData()
    {
        return $this->data;
    }
}
