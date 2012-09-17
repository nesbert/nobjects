<?php
namespace NObjects\Tests;
use NObjects\Cipher;

class CipherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cipher
     */
    protected $o;

    protected function setUp()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped(
              'The Mcrypt extension is not available.'
            );
        }

        $this->o = new Cipher;
    }

    protected function tearDown()
    {
        unset($this->o);
    }

    protected function all($s, $l = 1, $k = null)
    {
        $e = $this->o->encrypt($s, $l, $k);
        $this->assertEquals($e, Cipher::encrypt($s, $l, $k));

        $d = $this->o->decrypt($e, $l, $k);
        $this->assertEquals($s, $d);
        $this->assertEquals($d, Cipher::decrypt($e, $l, $k));
        $this->assertEquals($s, Cipher::decrypt($e, $l, $k));

        if ($k) {
            $this->assertNotEquals($d, Cipher::decrypt($e, $l, $k.'m'));
            $this->assertNotEquals($s, Cipher::decrypt($e, $l, $k.'m'));
        }
    }

    public function testEncrypt()
    {
        $s = 'www.creovel.org';
        $e = $this->o->encrypt($s);
        $this->assertEquals($e, Cipher::encrypt($s));
    }

    public function testDecrypt()
    {
        $s = 'www.creovel.org';
        $this->all($s);

        // level tests
        $this->all($s, 2);
        $this->all($s, 3);
        $this->all($s, 4);
        $this->all($s, 5); // will default to 1

        // level tests with key/salt
        $k = 'keepitDRY';
        $this->all($s, 1, $k);
        $this->all($s, 2, $k);
        $this->all($s, 3, $k);
        $this->all($s, 4, $k);
    }
}
