<?php
namespace NObjects\Tests;

use NObjects\Network;

/**
 * Unit tests for Network object.
 *
 * @access private
 **/
class NetworkTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $_SERVER['HTTP_HOST'] = 'www.example.org';
        $_SERVER['REMOTE_ADDR'] = '192.168.0.1';
        $_SERVER['REQUEST_URI'] = '/index.php';
    }

    protected function tearDown()
    {
        putenv('HTTPS=off');
    }

    public function testCurlRequest()
    {
        $response = Network::curlRequest('http://www.google.com');

        $this->assertTrue($response !== false);
        $this->assertTrue(isset($response->statusCode));
        $this->assertTrue(isset($response->contentType));
        $this->assertTrue(isset($response->body));
        $this->assertTrue(strstr($response->body, 'Google') !== false);

        $response = Network::curlRequest('http://badrequest');
        $this->assertFalse($response);

        $response = Network::curlRequest('http://www.google.com', 'BLAH');
        $this->assertFalse($response);
    }

    public function testDomain()
    {
        $this->assertEquals('example.org', Network::domain());
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->assertEquals('localhost', Network::domain());
    }

    public function testHost()
    {
        $this->assertEquals('www.example.org', Network::host());
    }

    public function testHttp()
    {
        $this->assertEquals('http://', Network::http());
        putenv('HTTPS=on');
        $this->assertEquals('https://', Network::http());
    }

    public function testHttpHost()
    {
        $this->assertEquals('http://www.example.org', Network::httpHost());
        putenv('HTTPS=on');
        $this->assertEquals('https://www.example.org', Network::httpHost());
    }

    public function testIntIp()
    {
        $this->assertEquals('2130706433', Network::intIp('127.0.0.1'));
        $this->assertEquals('3221234342', Network::intIp('192.0.34.166'));
    }

    public function testIsIp()
    {
        $this->assertTrue(Network::isIp($_SERVER['REMOTE_ADDR']));
        $this->assertTrue(Network::isIp('127.0.0.1'));
        $this->assertFalse(Network::isIp('boom'));
    }

    public function testIsSsl()
    {
        $this->assertFalse(Network::isSsl());
        putenv('HTTPS=on');
        $this->assertTrue(Network::isSsl());
    }

    public function testRemoteIp()
    {
        $this->assertEquals($_SERVER['REMOTE_ADDR'], Network::remoteIp());
        $this->assertEquals(ip2long($_SERVER['REMOTE_ADDR']), Network::remoteIp(true));

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.10.10.2';
        $this->assertEquals($_SERVER['HTTP_X_FORWARDED_FOR'], Network::remoteIp());

        $_SERVER['HTTP_CLIENT_IP'] = '10.10.10.1';
        $this->assertEquals($_SERVER['HTTP_CLIENT_IP'], Network::remoteIp());
    }

    public function testTld()
    {
        $this->assertEquals('org', Network::tld());
    }

    public function testUrl()
    {
        $this->assertEquals('http://www.example.org/index.php', Network::url());
    }
}
