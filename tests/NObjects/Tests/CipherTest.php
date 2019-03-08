<?php
namespace NObjects\Tests;

use NObjects\Cipher;

/**
 * Class CipherTest
 * @requires extension mcrypt
 */
class CipherTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (\PHP_MAJOR_VERSION > 5 || (\PHP_MAJOR_VERSION == 5 && \PHP_MINOR_VERSION > 5)) {
            $this->markTestSkipped(
              'MCrypt functionality throws deprecation errors after PHP 5.5'
            );
        }
    }

//    protected function all($s, $l = 1, $k = null)
//    {
//        $this->assertEquals($e, Cipher::encrypt($s, $l, $k));
//
//        $this->assertEquals($s, $d);
//        $this->assertEquals($d, Cipher::decrypt($e, $l, $k));
//        $this->assertEquals($s, Cipher::decrypt($e, $l, $k));
//
//        if ($k) {
//            $this->assertNotEquals($d, Cipher::decrypt($e, $l, $k.'m'));
//            $this->assertNotEquals($s, Cipher::decrypt($e, $l, $k.'m'));
//        }
//    }

    /**
     * @group legacy
     */
    public function testEncrypt()
    {
        $this->assertEquals('cgS4/O5hZXWxLNNo77UhmIZ5vcRmA04svVftYNdVFeU=', Cipher::encrypt('www.creovel.org', 4, '12345678901234567890123456789012'));
    }

    /**
     * @group legacy
     */
    public function testDecrypt()
    {
        $this->assertEquals('www.creovel.org', Cipher::decrypt('cgS4/O5hZXWxLNNo77UhmIZ5vcRmA04svVftYNdVFeU=', 4, '12345678901234567890123456789012'));
    }


    /**
     * @group legacy
     * @expectedDeprecation Method NObjects\Cipher::md5Data is deprecated. Migrate to using NObjects\Hash::md5 instead.
     */
    public function testMd5Data()
    {
        Cipher::md5Data('deprecation');
    }
}
