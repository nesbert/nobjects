<?php
namespace NObjects\Memcache;

/**
 * Data object class for a memcache server. Properties match
 * the name of Memcache path arguments.
 *
 * @author Nesbert Hidalgo
 **/
class Server
{
    /**
     * Set which internet protocol/scheme to use tcp or udp.
     *
     * @var string
     **/
    private $scheme = 'tcp';

    /**
     * Point to the host where memcached is listening for connections.
     *
     * @var string
     **/
    private $host = '127.0.0.1';

    /**
     * Point to the port where memcached is listening for connections. This
     * parameter is optional and its default value is 11211.
     *
     * @var integer
     **/
    private $port = 11211;

    /**
     * Controls the use of a persistent connection. Default to TRUE.
     *
     * @var boolean
     **/
    private $persistent = true;

    /**
     * Number of buckets to create for this server which in turn control its
     * probability of it being selected. The probability is relative to the
     * total weight of all servers.
     *
     * @var integer
     **/
    private $weight = 1;

    /**
     * Value in seconds which will be used for connecting to the daemon. Think
     * twice before changing the default value of 1 second - you can lose all
     * the advantages of caching if your connection is too slow.
     *
     * @var integer
     **/
    private $timeout = 1;

    /**
     * Controls how often a failed server will be retried, the default value
     * is 15 seconds. Setting this parameter to -1 disables automatic retry.
     *
     * @var integer
     **/
    private $retry_interval = 15;

    /**
     * Populate memache servers properties if passed.
     *
     * @param array $properties
     * @return Server
     * @see Server::load()
     */
    public function __construct(Array $properties = array())
    {
        if (count($properties)) $this->load($properties);
    }

    /**
     * Magic function to get private properties.
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        }
        return null;
    }

    /**
     * Load server properties and check if valid property.
     *
     * @param array $properties
     * @return void
     **/
    public function load(Array $properties)
    {
        foreach ($properties as $k => $v) {
            if (isset($this->{$k})) {
                $this->{$k} = $v;
            }
        }
    }

    /**
     * Build URI with current memcache server properties.
     *
     * @return string
     **/
    public function path()
    {
        $args = array();
        foreach ($this as $k => $v) {
            if (($k != 'scheme') && ($k != 'host') && ($k != 'port') && isset($this->{$k})) {
                $args[$k] = $v;
            }
        }
        $args = count($args) ? '?' . http_build_query($args) : '';
        return "{$this->scheme}://{$this->host}:{$this->port}{$args}";
    }

    /**
     * Check is a server is available/online.
     *
     * @return boolean
     **/
    public function isOnline()
    {
        try {
            $memcache = new Memcache;
            $online = $memcache->connect($this->host, $this->port);
            $memcache->close();
            return $online;
        } catch (\Exception $e) {
            return false;
        }
    }
}
