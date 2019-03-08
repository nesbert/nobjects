<?php
namespace NObjects\Cache;

interface Adapter
{
    /**
     * Check if key exists. $key a string, or an array of strings,
     * that contain keys. If an array was passed to $key, then an array
     * is returned that contains all existing keys, or an empty array
     * if no matches
     *
     * @abstract
     * @param string|array $key
     * @return bool|array
     */
    public function exists($key);

    /**
     * Get/check if key exists.
     *
     * @abstract
     * @param string|array $key
     * @return mixed
     */
    public function get($key);

    /**
     * Set a key, value and ttl.
     *
     * @abstract
     * @param string|array $key
     * @param $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 0);

    /**
     * Delete cache by $key. Optional $delay seconds if supported.
     *
     * @abstract
     * @param string|array $key
     * @param int $delay
     * @return bool
     */
    public function delete($key, $delay = 0);

    /**
     * Clear/flush all cached data.
     *
     * @abstract
     * @return bool
     */
    public function clear();

    /**
     * Open connection resources/check if valid cache resource.
     *
     * @abstract
     * @return bool
     */
    public function open();
}
