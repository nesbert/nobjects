<?php
namespace NObjects\Cache\Memcache;

/**
 * Data object class for a memcache cluster.
 *
 * @todo Use files for storing group keys
 * @author Nesbert Hidalgo
 **/
class Data extends Cluster
{
    /**
     * Memcache resource object.
     *
     * @var \Memcache
     **/
    private $memcache = null;

    /**
     * Flag to determine whether to encode and decode data as JSON.
     *
     * @var boolean
     **/
    private $jsonEncode = false;

    /**
     * When TRUE, returned objects will be converted into associative arrays.
     *
     * @var boolean
     **/
    private $jsonDecodeAssoc = false;

    /**
     * Build a memcache key node. Takes any number of parameters and will
     * build one string gluing them together by ":". Also, spaces will be
     * replaced with underscores.
     *
     * @return string
     **/
    public static function buildKey()
    {
        // get args
        $args = func_get_args();
        // remove empty vars
        foreach ($args as $k => $v) {
            if (empty($v)) unset($args[$k]);
        }
        return urldecode(str_replace(' ', '_', implode(':', $args)));
    }

    /**
     * Convert string keywords to the there timestamp equivalent. For
     * example: "midnite", "tomorrow", "1 min", "2 minutes", "3mins",
     * "1hr", "2 hours", "3 hrs", "1day", "2 days", "3 day", "1wk",
     * "2 weeks", "3 wks". If no keywords are found runs the string to
     * the strtotime method.
     *
     * @param string $string
     * @return integer
     **/
    public static function strToTime($string)
    {
        // get first integer from string
        $n = (int) preg_replace('/[^0-9.-]/', '', $string);

        switch (true) {
            case $string == 'midnite':
                $string = strtotime(date('Y-m-d 23:59:59'));
                break;

            case $string == 'tomorrow':
                $string = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));
                break;

            // 1 min, 2 minutes, 3mins
            case preg_match('/min/i', $string):
                $string = strtotime('+'.$n.' minutes');
                break;

            // 1hr, 2 hours, 3 hrs
            case preg_match('/hour|hr/i', $string):
                $string = strtotime('+'.$n.' hours');
                break;

            // 1day, 2 days, 3 day
            case preg_match('/day|dy/i', $string):
                $string = strtotime('+'.$n.' days');
                break;

            // 1wk, 2 weeks, 3 wks
            case preg_match('/week|wk/i', $string):
                $string = strtotime('+'.$n.' weeks');
                break;

            default:
                $string = strtotime($string);
                break;
        }

        return $string;
    }

    /**
     * Attempt to save a key value pair to memcache.
     *
     * @param string $key index to use into memcache
     * @param mixed $value data to store for that index
     * @param int  $expire length of time to expire object. 0 = never; x number of seconds; unixtimestamp
     * @return bool - true if data saved
     **/
    public function set($key, $value, $expire = 0)
    {
        if ($this->open()) {

            if (is_string($expire)) {
                $expire = self::strToTime($expire);
            }

            if ($this->jsonEncode) {
                $value = json_encode(array(
                        'created' => date('Y-m-d H:i:s'),
                        'expires' => $expire ? date('Y-m-d H:i:s', $expire) : 0,
                        'data' => $value
                        ));
            }

            if (@$this->memcache->replace($key, $value, null, $expire)) {
                return true;
            } else if (@$this->memcache->set($key, $value, null, $expire)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Attempt to retrieve a key from the memcache cluster.
     *
     * @param string $key index into memcache
     * @param mixed $returnVal if key found, value will be returned
     * @return bool
     *
     */
    public function get($key, &$returnVal)
    {
        if ($this->open()) {
            $result = @$this->memcache->get($key);
            if ($result !== false) {
                $returnVal = $this->decodeJSONValue($result);
                return true;
            }
        }

        return false;
    }

    /**
     * Attempt to remove a key value pair from memcache.
     *
     * @param string $key index to remove from memcache
     * @param int $timeOut - Length of time before deletion. (0 = immediately; n = seconds)
     * @return bool - true if data removed
     **/
    public function delete($key, $timeOut = 0)
    {
        if ($this->open()) {
            if (@$this->memcache->delete($key, $timeOut)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Attempt to retrieve an array of keys from the memcache cluster.
     *
     * @param array $keys array of indexed into memcache
     * @param array $foundKeys key value pair of found indexes
     * @param array $missingKeys array of indexes that did not match in memcache
     * @return bool - true if all keys found.  False if at least one key missing
     **/
    public function getArray($keys, &$foundKeys, &$missingKeys = null)
    {
        if ($this->open()) {
            $result = @$this->memcache->get((array) $keys);
            if (is_array($result)) {
                $foundKeys = $result;
                foreach ($foundKeys as $k => $v) {
                    $foundKeys[$k] = $this->decodeJSONValue($v);
                }
                $missingKeys = array_diff((array) $keys, array_keys($result));
                return count($missingKeys) == 0 ? true : false;
            }
        }
        $missingKeys = $keys;
        return false;
    }

    /**
     * Set key value pair and store key in a group.
     *
     * @param string $group
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return boolean
     */
    public function setGroup($group, $key, $value, $expire = 0)
    {
        $return = $this->set($key, $value, $expire);
        if ($return) {
            // group keys
            $keys = array();
            $this->get($group, $keys);
            $keys = (array) $keys;
            $keys[$key] = $key;
            $this->set($group, $keys, $expire);
        }
        return $return;
    }

    /**
     * Get an array of grouped key and value pairs.
     *
     * @param string $group
     * @param array $foundKeys
     * @param array $missingKeys
     * @return boolean
     **/
    public function getGroup($group, &$foundKeys, &$missingKeys = null)
    {
        if ($this->get($group, $keys)) {
            return $this->getArray($keys, $foundKeys, $missingKeys);
        }
        return false;
    }

    /**
     * Delete grouped key and value pairs.
     *
     * @param string $group
     * @return boolean
     **/
    public function deleteGroup($group)
    {
        if ($this->get($group, $keys)) {
            if (count($keys)) foreach ($keys as $key) {
                $this->delete($key);
            }
            return $this->delete($group);
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
    private function open()
    {
        if (!extension_loaded('memcache')) return false;

        // if this is not a memcache object or the status of the server is failed
        if (!($this->memcache instanceof Memcache)) {
            //create a new memcache object
            $this->memcache = $this->getMemcacheObject();
            if (!$this->memcache || !$this->memcache->connection) {
                $this->memcache = null;
                return false;
            }
        }
        return true;
    }

    /**
     * Check if JSON encode is enabled and decode string
     * accordingly.
     *
     * @param mixed $value
     * @return mixed
     **/
    private function decodeJSONValue($value)
    {
        if ($this->jsonEncode && is_string($value)) {
            $value = json_decode($value, $this->jsonDecodeAssoc);
            $value = $this->jsonDecodeAssoc ? $value['data'] : $value->data;
        }
        return $value;
    }

    // getters & setters

    /**
     * Get the boolean flag to encode cache data in JSON.
     *
     * @return boolean
     **/
    public function getJsonEncode()
    {
        return $this->jsonEncode;
    }

    /**
     * Set the boolean flag to or not to encode cache data in JSON.
     *
     * @param boolean $bool
     * @return Data
     **/
    public function setJsonEncode($bool)
    {
        $this->jsonEncode = (bool)$bool;
        return $this;
    }

    /**
     * Get the boolean flag to decode cache data in JSON.
     *
     * @return boolean
     **/
    public function getJsonDecodeAssoc()
    {
        return $this->jsonDecodeAssoc;
    }

    /**
     * Set the boolean to determine if returned objects will be
     * converted into associative arrays.
     *
     * @param boolean $bool
     * @return Data
     */
    public function setJsonDecodeAssoc($bool)
    {
        $this->jsonDecodeAssoc = (bool) $bool;
        return $this;
    }
}
