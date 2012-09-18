<?php
namespace NObjects\Ldap;
/**
 * LDAP helper class.
 *
 * @author Nesbert Hidalgo
 */
class Service extends \NObjects\Object
{
    private $link;

    private $host;
    private $port;
    private $ssl;

    private $accountDomainName;
    private $accountDomainNameShort;
    private $accountCanonicalForm;

    private $baseDn;

    private $defaultUsername;
    private $defaultPassword;

    private $searchSizeLimit;
    private $searchTimeout;

    const DEFAULT_PORT = 389;
    const DEFAULT_PORT_SSL = 636;
    const DEFAULT_SEARCH_SIZE_LIMIT = 0;
    const DEFAULT_SEARCH_TIMEOUT = 0;

    const ACCOUNT_NAME_FORM_DN = 1;
    const ACCOUNT_NAME_FORM_USERNAME = 2;
    const ACCOUNT_NAME_FORM_BACKSLASHES = 3;
    const ACCOUNT_NAME_FORM_PRINCIPAL = 4;

    /**
     * LDAP options:
     *
     *      host *required
     *      port
     *      baseDn
     *      accountDomainName
     *      accountDomainNameShort
     *      accountCanonicalForm
     *      defaultUsername
     *      defaultPassword
     *      searchSizeLimit - Limit entries.
     *      searchTimeout - Timeout in seconds.
     *
     * @param array $options
     * @throws ServiceException
     */
    public function __construct(Array $options)
    {
        if (empty($options['host'])) {
            throw new ServiceException('LDAP host required.');
        }

        $host = explode(':', $options['host']);
        unset($options['host']);
        $isIp = \NObjects\Validate::isIp($host[0]);

        // set defaults
        $this->setHost($host[0]);
        $this->setPort(self::DEFAULT_PORT);
        $this->setSsl(false);
        if (!empty($host[1])) {
            $this->setPort($host[1]);
            if ($host[1] == self::DEFAULT_PORT_SSL) {
                $this->setSsl(true);
            }
        }
        $this->setSearchSizeLimit(self::DEFAULT_SEARCH_SIZE_LIMIT);
        $this->setSearchTimeout(self::DEFAULT_SEARCH_TIMEOUT);

        // if host is domain name parse defaults from name
        if (!$isIp) {
            $domain = explode('.', $host[0]);
            $this->setBaseDn('DC=' . implode(',DC=', $domain));
            $this->setAccountDomainName($domain[count($domain)-2] . '.' . $domain[count($domain)-1]);
            $this->setAccountDomainNameShort($domain[count($domain)-2]);
        }

        // set options/override defaults
        if (!empty($options)) {
            $properties = $this->getProperties();
            foreach ($options as $k => $v) {
                $method = 'set' . $k;
                if (in_array($k, $properties) && method_exists($this, $method)) {
                    $this->{$method}($v);
                }
            }
        }

        // set default accountCanonicalForm if not in options
        if (!$this->getAccountCanonicalForm()) {
            if ($this->getAccountDomainNameShort()) {
                $this->setAccountCanonicalForm(self::ACCOUNT_NAME_FORM_BACKSLASHES);
            } else {
                $this->setAccountCanonicalForm(self::ACCOUNT_NAME_FORM_PRINCIPAL);
            }
        }
    }

    /**
     * Returns current LDAP resource or attempts connect if not present
     *
     * @return resource
     */
    public function link()
    {
        if (is_resource($this->link)) return $this->link;

        $link = $this->connect()->link();

        // set ldap version 3 as default
        $this->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);

        return $link;
    }

    /**
     * Set LDAP resource variable and will bind default user if set.
     *
     * @return Service
     * @throws ServiceException
     */
    public function connect()
    {
        $host = $this->getHost();
        if (substr($host, 0, 4) != 'ldap') {
            $host = 'ldap' . ($this->isSsl() ? 's' : '') . '://' . $host;
        }
        $this->link = ldap_connect($host, $this->getPort());

        if (!is_resource($this->link))  {
            throw new ServiceException("Unable to connect to {$host}:{$this->getPort()}");
        }

        // if default user is set bind to connection
        if ($this->getDefaultUsername()) {
            $defaultUserBind = $this->bind($this->getDefaultUsername(), $this->getDefaultPassword());
            if (!$defaultUserBind) {
                throw new ServiceException('Invalid credentials for default user.');
            }
        }

        return $this;
    }

    /**
     * Unbinds and resets resource link.
     *
     * @return Service
     */
    public function disconnect()
    {
        if (is_resource($this->link)) {
            @ldap_unbind($this->link);
        }

        $this->link = null;

        return $this;
    }

    /**
     * @param $option
     * @param $value
     * @throws ServiceException
     * @return Service
     */
    public function setOption($option, $value)
    {
        if (!ldap_set_option($this->link(), $option, $value)) {
            throw new ServiceException('Unable to set LDAP option.');
        }
        return $this;
    }

    /**
     * @param $option
     * @param $retval
     * @return bool
     */
    public function getOption($option, &$retval)
    {
        return ldap_get_option($this->link() , $option , $retval);
    }

    /**
     * Binds to the LDAP directory with specified RDN and password.
     *
     * @param null/string $rdn
     * @param null/string $password
     * @throws ServiceException
     * @return Service
     */
    public function bind($rdn = null, $password = null)
    {
        if (!@ldap_bind($this->link(), $rdn, $password)) {
            throw new ServiceException('Unable to BIND RDN.');
        }
        return $this;
    }

    /**
     * Search LDAP tree and return a formatted associative array of
     * found entries.
     *
     * @param $filter
     * @param array $attributes
     * @param string $baseDn
     * @return array
     */
    public function search($filter, Array $attributes = array(), $baseDn = '')
    {
        $baseDn = empty($baseDn) ? $this->getBaseDn() : $baseDn;
        $data = array();
        if ($result = @ldap_search($this->link(), $baseDn, $filter, $attributes, 0, $this->getSearchSizeLimit(), $this->getSearchTimeout())) {
            $entries = @ldap_get_entries($this->link(), $result);

            if (!empty($entries['count'])) {
                for ($i=0; $i<=$entries['count'];$i++) {
                    if (!isset($entries[$i])) continue;
                    for ($j=0;$j<=$entries[$i]["count"];$j++) {
                        if (!isset($entries[$i][$j])) continue;
                        $valCount = $entries[$i][$entries[$i][$j]]['count'];
                        if ($valCount <= 1) {
                            $data[$i][$entries[$i][$j]] = $entries[$i][$entries[$i][$j]][0];
                        } else {
                            $data[$i][$entries[$i][$j]] = array();
                            for ($k=0;$k<=$valCount;$k++) {
                                if (!isset($entries[$i][$entries[$i][$j]][$k])) continue;
                                $data[$i][$entries[$i][$j]][] = $entries[$i][$entries[$i][$j]][$k];
                            }
                        }

                    }

                }
            }
        }
        return $data;
    }

    /**
     * Gets the canonical format for a username based current or desired form.
     *
     * @param $username
     * @param int $form
     * @return string
     * @throws ServiceException
     */
    public function getCanonicalFormUsername($username, $form=0)
    {
        if ($form == 0) {
            if (!$form = $this->getAccountCanonicalForm()) {
                $form = self::ACCOUNT_NAME_FORM_DN;
            }
        }

        switch ($form) {
            case self::ACCOUNT_NAME_FORM_DN:
                return "CN={$username},{$this->getBaseDn()}";

            case self::ACCOUNT_NAME_FORM_BACKSLASHES:
                return $this->getAccountDomainNameShort() . '\\' . $username;

            case self::ACCOUNT_NAME_FORM_PRINCIPAL:
                return $username . '@' . $this->getAccountDomainName();

            default:
                throw new ServiceException('Invalid accountCanonicalForm value.');
        }
    }

    /**
     * Checks SSL setting. When SSL is set to true will use 'ldaps://hostname' to connect.
     *
     * @return bool
     */
    public function isSsl()
    {
        return (bool)$this->ssl;
    }

    /**
     * Check if a LDAP resource connected/linked.
     *
     * @return bool
     */
    public function isLive()
    {
        return is_resource($this->link) && 'ldap link' == get_resource_type($this->link);
    }

    // setters & getters

    public function setAccountCanonicalForm($accountCanonicalForm)
    {
        switch ($accountCanonicalForm) {
            case self::ACCOUNT_NAME_FORM_DN:
            case self::ACCOUNT_NAME_FORM_USERNAME:
            case self::ACCOUNT_NAME_FORM_BACKSLASHES:
            case self::ACCOUNT_NAME_FORM_PRINCIPAL:
                break;

            default:
                throw new ServiceException('Invalid accountCanonicalForm value.');

        }

        $this->accountCanonicalForm = $accountCanonicalForm;
        return $this;
    }

    public function getAccountCanonicalForm()
    {
        return $this->accountCanonicalForm;
    }

    public function setAccountDomainName($accountDomainName)
    {
        $this->accountDomainName = $accountDomainName;
        return $this;
    }

    public function getAccountDomainName()
    {
        return $this->accountDomainName;
    }

    public function setAccountDomainNameShort($accountDomainNameShort)
    {
        $this->accountDomainNameShort = $accountDomainNameShort;
        return $this;
    }

    public function getAccountDomainNameShort()
    {
        return $this->accountDomainNameShort;
    }

    public function setBaseDn($baseDn)
    {
        $this->baseDn = $baseDn;
        return $this;
    }

    public function getBaseDn()
    {
        return $this->baseDn;
    }

    public function setDefaultPassword($defaultPassword)
    {
        $this->defaultPassword = $defaultPassword;
        return $this;
    }

    public function getDefaultPassword()
    {
        return $this->defaultPassword;
    }

    public function setDefaultUsername($defaultUsername)
    {
        $this->defaultUsername = $defaultUsername;
        return $this;
    }

    public function getDefaultUsername()
    {
        return $this->defaultUsername;
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setSearchSizeLimit($searchSizeLimit)
    {
        $this->searchSizeLimit = (int)$searchSizeLimit;
        return $this;
    }

    public function getSearchSizeLimit()
    {
        return $this->searchSizeLimit;
    }

    public function setSearchTimeout($searchTimeout)
    {
        $this->searchTimeout = (int)$searchTimeout;
        return $this;
    }

    public function getSearchTimeout()
    {
        return $this->searchTimeout;
    }

    public function setSsl($ssl)
    {
        $this->ssl = (bool)$ssl;
        return $this;
    }
}

class ServiceException extends \Exception
{}
