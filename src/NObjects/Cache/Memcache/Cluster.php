<?php
namespace NObjects\Cache\Memcache;

/**
 * Cluster object for clustering NObjects\Memcache\Server instances.
 *
 * @author Nesbert Hidalgo
 **/
class Cluster
{
    /**
     * Memcache key to prepend all constants.
     *
     * <code>
     * define('MEMCACHE_CLUSTER_FRONTEND', 'tcp:domain1,tcp:domain2,...');
     * define('MEMCACHE_CLUSTER_BACKEND', 'tcp:domain5,tcp:domain6,...');
     * </code>
     *
     * @var string
     **/
    const CLUSTER_KEY_PREFIX = 'MEMCACHE_CLUSTER_';

    /**
     * An array of MemcacheServer data objects used to define session save_path.
     *
     * @var Server[]
     **/
    private $servers = array();

    /**
     * An array of memcache resources used to pool connections. Each similar
     * connection string uses the same resource.
     *
     * @var array
     */
    private static $connections = array();

    /**
     * Initialize memcache servers if passed. Settings are set at the
     * application level for portability.
     *
     * <code>
     * $servers = 'tcp://domain1,tcp://domain2,tcp://domain3';
     * //$servers = array(Server,Server,Server,...)';
     * $cluster = new Cluster($servers);
     * </code>
     *
     * @param mixed $servers Server[] objects or save_path optional
     */
    public function __construct($servers = null)
    {
        if ($servers) {
            $this->load($servers);
        }
    }

    /**
     * Load memcache servers. Acceptable $servers values are an array of
     * Memcache_Server objects or a single Memcache_Server object or
     * comma separated string of URIs (save_path) with params.
     *
     * @param mixed $servers
     * @return Cluster
     **/
    public function load($servers)
    {
        // if uri parse
        if (is_string($servers)) {
            $servers = explode(',', $servers);
            foreach ($servers as $server) {
                if (!$server) {
                    continue;
                }
                $server = parse_url(trim($server));
                // additional args from query string
                if (!empty($server['query'])) {
                    parse_str($server['query'], $args);
                    $server += (array) $args;
                }
                $this->addServer(new Server($server));
            }
        // if array of Memcache_Server objects
        } elseif (is_array($servers)) {
            foreach ($servers as $server) {
                $this->addServer($server);
            }
        } elseif ($servers instanceof Server) {
            $this->addServer($servers);
        }
        return $this;
    }

    /**
     * Create save path based on servers array.
     *
     * @param Server $server
     * @return Cluster
     */
    public function addServer(Server $server)
    {
        $this->servers[] = $server;
        return $this;
    }

    /**
     * Get servers array.
     *
     * @return Server[]
     **/
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * Creates a comma separated of server urls to use for session storage,
     * for example "tcp://host1:11211, tcp://host2:11211".
     *
     * Each url may contain parameters which are applied to that server, they
     * are the same as for the Memcache::addServer() method. For example
     * "tcp://host1:11211?persistent=1&weight=1&timeout=1&retry_interval=15"
     *
     * @return string
     **/
    public function savePath()
    {
        $servers = array();
        foreach ($this->getServers() as $server) {
            $servers[] = $server->path();
        }
        return implode(', ', $servers);
    }

    /**
     * Get a memcache object of initialized with the current $server.
     *
     * @param boolean $poolConnections
     * @return bool|\Memcache
     **/
    public function getMemcacheObject($poolConnections = true)
    {
        if (!class_exists('\Memcache')) {
            return false;
        }

        // check connection pool
        if ($poolConnections) {
            if (isset(self::$connections[$this->savePath()])) {
                return self::$connections[$this->savePath()];
            }
        }

        $memcache = new \Memcache();
        foreach ($this->getServers() as $server) {
            @$memcache->addServer(
                (string) $server->host,
                (int) $server->port,
                (bool) $server->persistent,
                (int) $server->weight,
                (int) $server->timeout,
                (int) $server->retry_interval
            );
        }

        // set connection pool
        if ($poolConnections) {
            self::$connections[$this->savePath()] = $memcache;
        }

        return $memcache;
    }

    /**
     * Immediately invalidate/expire all existing items.
     *
     * @return boolean
     */
    public function flush()
    {
        return $this->getMemcacheObject()->flush();
    }

    /**
     * Check if servers are online.
     *
     * @param bool $checkAll
     * @return bool
     */
    public function isOnline($checkAll = false)
    {
        $online = false;
        foreach ($this->getServers() as $server) {
            // if one server is offline return false
            if ($checkAll) {
                if ($server->isOnline()) {
                    $online = true;
                } else {
                    $online = false;
                    break;
                }
            } else {
                if ($server->isOnline()) {
                    return true;
                    break;
                }
            }
        }
        return $online;
    }

    /**
     * Get statuses of loaded servers and return an associative array. Will print
     * status to screen if $echo set to true. NOTE: FAILED may also mean an unused
     * server sitting ideal.
     *
     * @param boolean $echo - default is false
     * @return array
     **/
    public function status($echo = false)
    {
        $return = array();
        foreach ($this->getServers() as $server) {
            $return["{$server->host}:{$server->port}"] = $server->isOnline();
            if ($echo) {
                echo "Memcache on {$server->host}:{$server->port} " .
                '<span style="' . ($return["{$server->host}:{$server->port}"] ?
                    'color:green;' :
                    'color:red;font-weight:bold;')
                . '">' .
                ($return["{$server->host}:{$server->port}"] ? '[Ok]' : '[FAIL]') ."</span>";
            }
        }
        return $return;
    }

    /**
     * Get statuses of loaded servers and return an associative array. Will print
     * status to screen if $echo set to true.
     *
     * @return array
     **/
    public function stats()
    {
        static $return;
        if (empty($return)) {
            $memcache = $this->getMemcacheObject();
            $return = @$memcache->getExtendedStats();
            $memcache->close();
        }
        return $return;
    }

    /**
     * Get detailed stats of loaded servers with compiled totals and statues.
     *
     * @return object
     **/
    public function monitorStats()
    {
        $return = array();
        $return['servers'] = $this->status();
        $return['stats'] = $this->stats();

        // total server totals
        $return['totals'] = array();
        $return['totals']['ok_count'] = 0;
        $return['totals']['time'] = 1000000000000000;
        $return['totals']['uptime'] = 0;
        $return['totals']['latest_version'] = 0;
        $return['totals']['earliest_version'] = 100;

        $exclude = array('pid','uptime','time','version','pointer_size');

        // init values
        if (is_array($return['stats'])) {
            foreach ($return['stats'] as $server => $stats) {
                if (is_array($stats)) {
                    foreach ($stats as $k => $v) {
                        if (in_array($k, $exclude)) {
                            continue;
                        }
                                        $return['totals'][$k] = 0;
                    }
                }
            }
        }

        // get totals
        if (is_array($return['servers'])) {
            foreach ($return['servers'] as $ip => $status) {
                if (empty($status)) {
                    continue;
                }
                if ($status) {
                    $return['totals']['ok_count']++;
                }
                $stat = $return['stats'][$ip];
                if (is_array($stat)) {
                    foreach ($stat as $k => $v) {
                        if (in_array($k, $exclude)) {
                            if ($stat['time'] < $return['totals']['time']) {
                                $return['totals']['time'] = $stat['time'];
                            }
                            if ($stat['uptime'] > $return['totals']['uptime']) {
                                $return['totals']['uptime'] = $stat['uptime'];
                            }
                            if ($stat['version'] > $return['totals']['latest_version']) {
                                $return['totals']['latest_version'] = $stat['version'];
                            }
                            if ($stat['version'] < $return['totals']['earliest_version']) {
                                $return['totals']['earliest_version'] = $stat['version'];
                            }
                        } else {
                            $return['totals'][$k] += $stat[$k];
                        }
                    }
                }
            }
        }

        return (object) $return;
    }

    /**
     * Get an array of all constants that use 'MEMCACHE_CLUSTER_'.
     *
     * @return array
     **/
    public static function getClusterConstants()
    {
        $constants = get_defined_constants(true);
        $key = self::CLUSTER_KEY_PREFIX;
        $clusters = array();

        if (empty($constants['user'])) {
            return $clusters;
        }

        foreach ($constants['user'] as $k => $v) {
            if (preg_match('/^'.$key.'/', $k)) {
                $clusters[$k] = array(
                    'name' => str_replace($key, '', $k),
                    'value' => $v
                );
            }
        }

        return $clusters;
    }
}
