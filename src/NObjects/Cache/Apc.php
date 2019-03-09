<?php
namespace NObjects\Cache;

class Apc extends Base implements Adapter
{
    /**
     * Apc constructor.
     */
    public function __construct()
    {
        if (!extension_loaded('apcu')) {
            throw new \RuntimeException("The APCU extension is not enabled.");
        }
    }

    /**
     * Check if key exists.
     *
     * @param $key
     * @return mixed
     */
    public function exists($key)
    {
        if (!$this->open()) {
            return false;
        }

        return apcu_exists($key);
    }

    /**
     * Get/check if key exists.
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->open()) {
            return false;
        }

        return apcu_fetch($key);
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
        if (!$this->open()) {
            return false;
        }

        return apcu_store($key, $value, $this->stringToTime($ttl, true));
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
        if (!$this->open()) {
            return false;
        }

        return apcu_delete($key);
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

        return apcu_clear_cache();
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
            $isOpen = function_exists('apcu_add');
        }
        return $isOpen;
    }
}
