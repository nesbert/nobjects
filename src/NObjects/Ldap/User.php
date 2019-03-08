<?php
namespace NObjects\Ldap;

/**
 * LDAP user authentication helper.
 *
 * @author Nesbert Hidalgo
 */
class User extends \NObjects\Nobject
{
    private $data;

    /**
     * @static
     * @param $username
     * @param $password
     * @param Service|array $options Ldap object or an array of Ldap $options
     * @return User
     * @throws UserException
     */
    public static function authenticate($username, $password, $options)
    {
        if (empty($username) || empty($password)) {
            throw new UserException('Missing required credentials.');
        }

        if ($options instanceof Service) {
            $ldap = $options;
        } else {
            $ldap = new Service($options);
        }

        // get formatted name
        $accountName = $ldap->getCanonicalFormUsername($username);

        if (!$ldap->bind($accountName, $password)) {
            throw new UserException('Invalid credentials.');
        }

        $attributes = array(
            'samaccountname',
            'cn',
            'givenname',
            'sn',
            'mail',
            'title',
            'department',
            'memberof',
        );

        if (is_array($options) && !empty($options['attributes'])) {
            $attributes = array_unique(array_merge($attributes, (array) $options['attributes']));
        }

        if ($ldap->getAccountCanonicalForm() == Service::ACCOUNT_NAME_FORM_DN) {
            $filter = "(cn={$username})";
        } else {
            $filter = "(samaccountname={$username})";
        }

        $data = $ldap->search($filter, $attributes);

        return new User(current($data));
    }

    /**
     * Requires memberOf attribute.
     *
     * @static
     * @param $ldapGroup
     * @param $options
     * @return User[]
     */
    public static function getGroupMembers($ldapGroup, $options)
    {
        if ($options instanceof Service) {
            $ldap = $options;
        } else {
            $ldap = new Service($options);
        }

        $filter = "(&(objectCategory=user)(memberOf={$ldapGroup}))";
        $attributes = array(
            'samaccountname',
            'cn',
            'givenname',
            'sn',
            'mail',
            'title',
            'department',
            'memberof',
        );

        $results = $ldap->search($filter, $attributes);

        $users = array();

        foreach ($results as $row) {
            $users[] = new User($row);
        }

        // sort by name TODO use $options to change comparison property
        usort($users, function ($a, $b) {
            $al = strtolower($a->getName());
            $bl = strtolower($b->getName());
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });

        return $users;
    }

    // instance methods

    public function __construct(array $data = null)
    {
        if (is_array($data)) {
            $this->setData($data);
        }
    }

    public function getFirstName()
    {
        return $this->givenname;
    }

    public function getLastName()
    {
        return $this->sn;
    }

    public function getName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    public function getEmail()
    {
        return $this->mail;
    }

    /**
     * Return SAM-Account-Name if not set returns Common-Name.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->samaccountname ? $this->samaccountname : $this->cn;
    }

    public function isMemberOfGroup($group)
    {
        $groups = (array)$this->memberof;

        foreach ($groups as $ldapGroup) {
            if (strpos($ldapGroup, $group) !== false) {
                return true;
            }
        }
        return false;
    }

    // magic functions

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    // getters & setters

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
