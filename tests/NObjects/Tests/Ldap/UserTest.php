<?php
namespace NObjects\Tests\Ldap;

use NObjects\Ldap\Service,
    NObjects\Ldap\ServiceException,
    NObjects\Ldap\User,
    NObjects\Ldap\UserException;

class UserTest extends \PHPUnit_Framework_TestCase
{
    private $server1 = array();

    /**
     * @var User
     */
    private static $user;

    public function setUp()
    {
        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('ldap extension is not available.');
        }

        $this->server1 = array(
            'host' => 'ldap.testathon.net:389',
            'baseDn' => 'OU=users,DC=testathon,DC=net',
            'accountCanonicalForm' => Service::ACCOUNT_NAME_FORM_DN,
        );
    }

    public function testAuthenticate()
    {
        self::$user = User::authenticate('john', 'john', $this->server1);
        
        $this->assertEquals('john', self::$user->getUsername());
        $this->assertEquals('John', self::$user->getFirstName());
        $this->assertEquals('Smith', self::$user->getLastName());
        $this->assertEquals('john.smith@testathon.net', self::$user->getEmail());
    }

    public function testGetGroupMembers()
    {
        // TODO
    }
    public function testIsMemberOf()
    {
        // TODO
    }

    public function testMagicGetter()
    {
        $this->assertEquals('john.smith@testathon.net', self::$user->mail);
        $this->assertEquals('John', self::$user->givenname);
        $this->assertEquals('Smith', self::$user->sn);
        $this->assertEquals('john', self::$user->cn);
    }

    public function testSettersGetters()
    {
        $this->assertEquals('john.smith@testathon.net', self::$user->getEmail());
        $this->assertEquals('John', self::$user->getFirstName());
        $this->assertEquals('Smith', self::$user->getLastName());
        $this->assertEquals('john', self::$user->getUsername());

        $testData = array(1,2,3,4,5);
        $ldapUser = new User($testData);
        $this->assertEquals($testData, $ldapUser->getData());

        $ldapUser = new User();
        $this->assertEquals($ldapUser, $ldapUser->setData($testData));
        $this->assertEquals($testData, $ldapUser->getData());
    }

}
