<?php

namespace NObjects\Tests\Ldap;

use NObjects\Ldap\Service;
use NObjects\Ldap\ServiceException;

/**
 * @requires extension ldap
 */
class ServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Service
     */
    protected $ldap1;

    /**
     * @var string
     */
    private static $ldapHost;

    /**
     * @var string
     */
    private static $ldapPort;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $host = getenv('PHPUNIT_LDAP_SERVER_HOST') ? getenv('PHPUNIT_LDAP_SERVER_HOST') : 'localhost';
        $port = getenv('PHPUNIT_LDAP_SERVER_PORT') ? getenv('PHPUNIT_LDAP_SERVER_PORT') : 389;

        self::$ldapHost = $host;
        self::$ldapPort = $port;
    }

    /**
     * setUp runs before each unit test
     */
    protected function setUp()
    {
        $this->ldap1 = new Service($this->getLdapServers()->offsetGet(0));
    }

    /**
     * @return array|\ArrayObject
     */
    protected function getLdapServers()
    {
        static $servers;

        if (is_null($servers)) {
            $servers = new \ArrayObject();

            $servers[] = array(
                'host' => self::$ldapHost . ':' . self::$ldapPort,
                'baseDn' => 'DC=example,DC=org',
                'accountCanonicalForm' => Service::ACCOUNT_NAME_FORM_DN,
            );
        }

        return $servers;
    }

    /**
     * @group ldap_integration
     */
    public function testLink()
    {
        $this->assertTrue(is_resource($this->ldap1->link()));
        $this->assertEquals('ldap link', get_resource_type($this->ldap1->link()));
    }

    /**
     * @group ldap_integration
     */
    public function testConnect()
    {
        $ldap = $this->ldap1->connect();

        $this->assertTrue(is_resource($ldap->link()));
        $this->assertEquals('ldap link', get_resource_type($ldap->link()));

        try {
            new Service(array());
            $this->fail('Exception expected!');
        } catch (\Exception $e) {
            $this->assertEquals('LDAP host required.', $e->getMessage());
        }

        $settings = $this->getLdapServers()->offsetGet(0);
        try {
            $ldap = new Service($settings);
            $this->assertEquals($ldap, $ldap->connect());
        } catch (\Exception $e) {
            $this->assertEquals('Unable to connect to ' . $settings['host'], $e->getMessage());
        }
    }

    /**
     * @group ldap_integration
     */
    public function testDisconnect()
    {
        $this->ldap1->connect()->disconnect();
        $this->assertFalse($this->ldap1->isLive());
    }

    /**
     * @group ldap_integration
     */
    public function testBind()
    {
        try {
            $this->ldap1->bind($this->ldap1->getCanonicalFormUsername('john'), 'wrong password');
            $this->fail('Expected exception');
        } catch (ServiceException $e) {
            $this->assertRegExp('/Unable to BIND RDN/', $e->getMessage());
        }

        $ldap = $this->ldap1->bind($this->ldap1->getCanonicalFormUsername('carol'), 'carol');
        $this->assertTrue($ldap instanceof Service);
    }

    /**
     * @group ldap_integration
     */
    public function testSearch()
    {
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

        $data1 = $this->ldap1->bind($this->ldap1->getCanonicalFormUsername('carol'), 'carol')
            ->search("(cn=john)", $attributes);
        $data1 = current($data1);

        $this->assertEquals('john', $data1['cn']);
        $this->assertEquals('john@example.org', $data1['mail']);
        $this->assertEquals('John', $data1['givenname']);
        $this->assertEquals('Smith', $data1['sn']);
    }

    /**
     * @group ldap_integration
     */
    public function testGetCanonicalFormUsername()
    {
        $this->assertEquals('CN=john,DC=example,DC=org', $this->ldap1->getCanonicalFormUsername('john'));
    }

    /**
     * @group ldap_integration
     */
    public function testIsSsl()
    {
        $this->assertFalse($this->ldap1->isSsl());
    }

    /**
     * @group ldap_integration
     */
    public function testIsLive()
    {
        $this->assertFalse($this->ldap1->isLive());
        $this->ldap1->connect();
        $this->assertTrue($this->ldap1->isLive());
        $this->ldap1->connect()->disconnect();
        $this->assertFalse($this->ldap1->isLive());
    }

    /**
     * @group ldap_integration
     */
    public function testSetOption()
    {
        $this->assertTrue($this->ldap1->getOption(LDAP_OPT_PROTOCOL_VERSION, $retval));
        $this->assertEquals(3, $retval);

        try {
            $this->ldap1->setOption(LDAP_OPT_PROTOCOL_VERSION, 1);
            $this->fail('Expected exception');
        } catch (ServiceException $e) {
            $this->assertEquals('Unable to set LDAP option.', $e->getMessage());
        }

        $this->ldap1->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->assertTrue($this->ldap1->getOption(LDAP_OPT_PROTOCOL_VERSION, $retval));
        $this->assertEquals(3, $retval);
    }

    /**
     * @group ldap_integration
     */
    public function testGetOption()
    {
        $this->assertTrue($this->ldap1->getOption(LDAP_OPT_PROTOCOL_VERSION, $retval));
        $this->assertEquals(3, $retval);
    }

    /**
     * @group ldap_integration
     */
    public function testDefaults()
    {
        $ldap = new Service(array('host' => 'subdomain.domain.tld'));

        $this->assertEquals(Service::ACCOUNT_NAME_FORM_BACKSLASHES, $ldap->getAccountCanonicalForm());
        $this->assertEquals('domain.tld', $ldap->getAccountDomainName());
        $this->assertEquals('domain', $ldap->getAccountDomainNameShort());
        $this->assertEquals('DC=subdomain,DC=domain,DC=tld', $ldap->getBaseDn());
        $this->assertEquals(null, $ldap->getDefaultPassword());
        $this->assertEquals(null, $ldap->getDefaultUsername());
        $this->assertEquals('subdomain.domain.tld', $ldap->getHost());
        $this->assertEquals(Service::DEFAULT_PORT, $ldap->getPort());
        $this->assertEquals(Service::DEFAULT_SEARCH_SIZE_LIMIT, $ldap->getSearchSizeLimit());
        $this->assertEquals(Service::DEFAULT_SEARCH_TIMEOUT, $ldap->getSearchTimeout());
        $this->assertEquals(Service::DEFAULT_NETWORK_TIMEOUT, $ldap->getNetworkTimeout());
        $this->assertEquals(false, $ldap->isSsl());
    }

    /**
     * @group ldap_integration
     */
    public function testSettersGetters()
    {
        $server1 = $this->getLdapServers()->offsetGet(0);
        $h = explode(':', $server1['host']);

        $this->assertEquals($h[0], $this->ldap1->getHost());
        $this->assertEquals($h[1], $this->ldap1->getPort());
        $this->assertEquals($server1['baseDn'], $this->ldap1->getBaseDn());
        $this->assertEquals($server1['accountCanonicalForm'], $this->ldap1->getAccountCanonicalForm());

        $this->assertEquals(
            $this->ldap1,
            $this->ldap1->setAccountCanonicalForm(Service::ACCOUNT_NAME_FORM_BACKSLASHES)
        );
        $this->assertEquals($this->ldap1, $this->ldap1->setAccountDomainName('AccountDomainName'));
        $this->assertEquals($this->ldap1, $this->ldap1->setAccountDomainNameShort('AccountDomainNameShort'));
        $this->assertEquals($this->ldap1, $this->ldap1->setBaseDn('BaseDn'));
        $this->assertEquals($this->ldap1, $this->ldap1->setDefaultPassword('DefaultPassword'));
        $this->assertEquals($this->ldap1, $this->ldap1->setDefaultUsername('DefaultUsername'));
        $this->assertEquals($this->ldap1, $this->ldap1->setHost('Host'));
        $this->assertEquals($this->ldap1, $this->ldap1->setPort(5555));
        $this->assertEquals($this->ldap1, $this->ldap1->setSearchSizeLimit(100));
        $this->assertEquals($this->ldap1, $this->ldap1->setSearchTimeout(600));
        $this->assertEquals($this->ldap1, $this->ldap1->setNetworkTimeout(10));
        $this->assertEquals($this->ldap1, $this->ldap1->setSsl(true));

        $this->assertEquals(Service::ACCOUNT_NAME_FORM_BACKSLASHES, $this->ldap1->getAccountCanonicalForm());
        $this->assertEquals('AccountDomainName', $this->ldap1->getAccountDomainName());
        $this->assertEquals('AccountDomainNameShort', $this->ldap1->getAccountDomainNameShort());
        $this->assertEquals('BaseDn', $this->ldap1->getBaseDn());
        $this->assertEquals('DefaultPassword', $this->ldap1->getDefaultPassword());
        $this->assertEquals('DefaultUsername', $this->ldap1->getDefaultUsername());
        $this->assertEquals('Host', $this->ldap1->getHost());
        $this->assertEquals(5555, $this->ldap1->getPort());
        $this->assertEquals(100, $this->ldap1->getSearchSizeLimit());
        $this->assertEquals(600, $this->ldap1->getSearchTimeout());
        $this->assertEquals(10, $this->ldap1->getNetworkTimeout());
        $this->assertEquals(true, $this->ldap1->isSsl());

        try {
            $this->ldap1->setAccountCanonicalForm('invalid');
            $this->fail('Exception expected!');
        } catch (\Exception $e) {
            $this->assertEquals('Invalid accountCanonicalForm value.', $e->getMessage());
        }
    }
}
