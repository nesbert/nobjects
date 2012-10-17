<?php
namespace NObjects;

/**
 * A wrapper class to NObjects\Cache adapters.
 *
 * @author Nesbert Hidalgo
 **/
class Cache extends Cache\Base implements Cache\Adapter
{
    /**
     * @var Cache\Adapter
     */
    private $adapter;

    /**
     * @var string
     */
    private $key;

    /**
     * Initialize class with an adapter class.
     *
     *
     * @param Cache\Adapter $adapter
     */
    public function __construct($adapter = null)
    {
        if ($adapter) $this->setAdapter($adapter);
    }

    /**
     * Overloading adapter object methods
     *
     * @param $method
     * @param $arguments
     * @throws \Exception
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->getAdapter(), $method)) {
            return call_user_func_array(array($this->getAdapter(), $method), $arguments);
        }
        throw new \Exception("Call to undefined method " . __CLASS__ . "::{$method}()");
    }

    /**
     * Get/check if key exists. $key a string, or an array of strings,
     * that contain keys. If an array was passed to $key, then an array
     * is returned that contains all existing keys, or an empty array
     * if no matches
     *
     * @param string/array $key
     * @return mixed
     */
    public function exists($key)
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->exists($key);
        }
        return false;
    }

    /**
     * Get/check if key exists.
     *
     * @param string/array $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->get($key);
        }
        return false;
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
        if ($this->getAdapter()) {
            return $this->getAdapter()->set($key, $value, $ttl);
        }
        return false;
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
        if ($this->getAdapter()) {
            return $this->getAdapter()->delete($key, $delay);
        }
        return false;
    }

    /**
     * Clear/flush all cached data.
     *
     * @return bool
     */
    public function clear()
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->clear();
        }
        return false;
    }

    /**
     * Open connection resources/check if valid cache resource.
     *
     * @return bool
     */
    public function open()
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->open();
        }
        return false;
    }

    // $key instance methods

    /**
     * Check if current instance $key exists.
     *
     * @param string/array $key
     * @return mixed
     */
    public function keyExists()
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->exists($this->getKey());
        }
        return false;
    }

    /**
     * Get/check if current instance $key.
     *
     * @param string/array $key
     * @return mixed
     */
    public function getValue()
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->get($this->getKey());
        }
        return false;
    }

    /**
     * Set a value and ttl of current instance $key.
     *
     * @param string/array $key
     * @param $value
     * @param int $ttl
     * @return bool
     */
    public function setValue($value, $ttl = 0)
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->set($this->getKey(), $value, $ttl);
        }
        return false;
    }

    /**
     * Delete cache by current instance $key. Optional $delay seconds if supported.
     *
     * @param string/array $key
     * @param int $delay
     * @return bool
     */
    public function deleteKey($delay = 0)
    {
        if ($this->getAdapter()) {
            return $this->getAdapter()->delete($this->getKey(), $delay);
        }
        return false;
    }

    // setters & getters

    /**
     * @param Cache\Adapter $adapter
     * @return Cache
     */
    public function setAdapter(Cache\Adapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return Cache\Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Will build key based all arguments passed.
     *
     * @see buildKey()
     * @return Cache
     */
    public function setKey()
    {
        $key = call_user_func_array(array($this, 'buildKey'), func_get_args());
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
