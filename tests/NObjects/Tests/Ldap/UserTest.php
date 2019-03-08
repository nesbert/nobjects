<?php
namespace NObjects\Tests\Ldap;

use NObjects\Ldap\User;

/**
 * UserTest provides integration tests for Ldap\User
 * @requires extension ldap
 */
class UserTest extends ServiceTest
{
    /**
     * @var User
     */
    private static $user;

    /**
     * @group ldap_integration
     */
    public function testAuthenticate()
    {
        self::$user = User::authenticate('carol', 'carol', $this->ldap1);

        $this->assertEquals('carol', self::$user->getUsername());
        $this->assertEquals('Carol', self::$user->getFirstName());
        $this->assertEquals('Smith', self::$user->getLastName());
        $this->assertEquals('carol@example.org', self::$user->getEmail());
    }

    /**
     * @group ldap_integration
     *
     * @depends testAuthenticate
     */
    public function testGetGroupMembers()
    {
        // TODO
    }

    /**
     * @group ldap_integration
     *
     * @depends testAuthenticate
     */
    public function testIsMemberOf()
    {
        // TODO
    }

    /**
     * @group ldap_integration
     *
     * @depends testAuthenticate
     */
    public function testMagicGetter()
    {
        $this->assertEquals('carol@example.org', self::$user->mail);
        $this->assertEquals('Carol', self::$user->givenname);
        $this->assertEquals('Smith', self::$user->sn);
        $this->assertEquals('carol', self::$user->cn);
    }

    /**
     * @group ldap_integration
     *
     * @depends testAuthenticate
     */
    public function testSettersGetters()
    {
        $this->assertEquals('carol@example.org', self::$user->getEmail());
        $this->assertEquals('Carol', self::$user->getFirstName());
        $this->assertEquals('Smith', self::$user->getLastName());
        $this->assertEquals('carol', self::$user->getUsername());

        $testData = array(1,2,3,4,5);
        $ldapUser = new User($testData);
        $this->assertEquals($testData, $ldapUser->getData());

        $ldapUser = new User();
        $this->assertEquals($ldapUser, $ldapUser->setData($testData));
        $this->assertEquals($testData, $ldapUser->getData());
    }
}
