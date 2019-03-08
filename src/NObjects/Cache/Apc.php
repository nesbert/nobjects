<?php
namespace NObjects\Cache;

class Apc extends Base implements Adapter
{
    /**
     * Cache system to use (apcu vs apc)
     *
     * @var string
     */
    private static $USE_EXTENSION;

    /**
     * Apc constructor.
     */
    public function __construct()
    {
        if (function_exists('apcu_exists')) {
            self::$USE_EXTENSION = 'apcu';
        }

        if (function_exists('apc_exists')) {
            self::$USE_EXTENSION = 'apc';
        }

        if (empty(self::$USE_EXTENSION)) {
         throw new \RuntimeException("Neither APC or APCU is loaded and enabled.");
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


        return $this->invokeApc('exists', array($key));
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

        return $this->invokeApc('fetch', array($key));
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

        return $this->invokeApc('store', array($key, $value, $this->stringToTime($ttl, true)));
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

        return $this->invokeApc('delete', array($key));
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

        $retVal = $this->invokeApc('clear_cache');

        if ('apc' == self::$USE_EXTENSION) {
            $retVal = $retVal && $this->invokeApc('clear_cache', array('user'));
            $retVal = $retVal && $this->invokeApc('clear_cache', array('opcode'));
        }

        return $retVal;
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
            $isOpen = function_exists('apc_add') || function_exists('apcu_add');
        }
        return $isOpen;
    }

    /**
     * Wraps the invocation of the appropriate APC extension
     *
     * @param string $methodName
     * @param array $args
     *
     * @return mixed
     */
    private function invokeApc($methodName, $args = array())
    {
        return call_user_func_array(self::$USE_EXTENSION . '_' . $methodName, $args);
    }
}
