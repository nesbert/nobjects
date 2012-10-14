<?php
namespace NObjects\Cache;

class Apc extends Base implements Adapter
{
    /**
     * Check if key exists.
     *
     * @param $key
     * @return mixed
     */
    public function exists($key)
    {
        if (!$this->open()) return false;
        return apc_exists($key);
    }

    /**
     * Get/check if key exists.
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->open()) return false;
        return apc_fetch($key);
    }

    /**
     * Set a key, value and ttl.
     *
     * @param $key
     * @param $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 0)
    {
        if (!$this->open()) return false;
        return apc_store($key, $value, $this->stringToTime($ttl, true));
    }

    /**
     * Delete cache by $key. Optional $delay seconds if supported.
     *
     * @param $key
     * @param int $delay
     * @return bool
     */
    public function delete($key, $delay = 0)
    {
        if (!$this->open()) return false;
        return apc_delete($key);
    }

    /**
     * Clear/flush all cached data.
     *
     * @return bool
     */
    public function clear()
    {
        return $this->open() && apc_clear_cache() && apc_clear_cache('user') && apc_clear_cache('opcode');
    }

    /**
     * Check if extension loaded and enabled.
     *
     * @return bool
     */
    public function open()
    {
        static $isOpen;
        if (is_null($isOpen)) {
            $isOpen = extension_loaded('apc') && ini_get('apc.enabled');
        }
        return $isOpen;
    }
}
