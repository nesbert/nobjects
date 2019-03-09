<?php

namespace NObjects\Tests;

use NObjects\Hash;

/**
 * HashTest provides unit tests.
 */
class HashTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function testMd5()
    {
        $str = 'Lego Star Wars!';
        $this->assertEquals(md5($str), Hash::md5($str));

        $array = array(1,2,3);
        $this->assertEquals(md5(json_encode($array)), Hash::md5($array));

        $array2 = array(3,2,1);
        $this->assertEquals(md5(json_encode($array)), Hash::md5($array2));

        $object = (object)array('id' => 555, 'name' => 'Luke S.');
        $this->assertEquals(md5(serialize($object)), Hash::md5($object));
    }
}
