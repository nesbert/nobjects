<?php
namespace NObjects\Tests\Ldap;

use NObjects\Ldap\Service,
    NObjects\Ldap\ServiceException,
    NObjects\Ldap\User,
    NObjects\Ldap\UserException;

class UserTest extends ServiceTest
{
    /**
     * @var User
     */
    private static $user;

    public function testAuthenticate()
    {
        self::$user = User::authenticate('john', 'john', $this->ldap1);

        $this->assertEquals('john', self::$user->getUsername());
        $this->assertEquals('John', self::$user->getFirstName());
        $this->assertEquals('Smith', self::$user->getLastName());
        $this->assertEquals('john.smith@testathon.net', self::$user->getEmail());
    }

    /**
     * @depends testAuthenticate
     */
    public function testGetGroupMembers()
    {
        // TODO
    }

    /**
     * @depends testAuthenticate
     */
    public function testIsMemberOf()
    {
        // TODO
    }

    /**
     * @depends testAuthenticate
     */
    public function testMagicGetter()
    {
        $this->assertEquals('john.smith@testathon.net', self::$user->mail);
        $this->assertEquals('John', self::$user->givenname);
        $this->assertEquals('Smith', self::$user->sn);
        $this->assertEquals('john', self::$user->cn);
    }

    /**
     * @depends testAuthenticate
     */
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