<?php
namespace NObjects\Cache;

class Memcache extends Base implements Adapter
{
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
        // TODO: Implement exists() method.
    }

    /**
     * Get/check if key exists.
     *
     * @param string/array $key
     * @return mixed
     */
    public function get($key)
    {
        // TODO: Implement get() method.
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
        // TODO: Implement set() method.
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
        // TODO: Implement delete() method.
    }

    /**
     * Clear/flush all cached data.
     *
     * @return bool
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }
}