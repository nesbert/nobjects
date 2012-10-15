<?php
namespace NObjects\Tests;

use NObjects\String;

/**
 * Unit tests for String object.
 *
 * @access private
 **/
class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testContains()
    {
        $this->assertTrue(String::contains('@', '@test'));
        $this->assertTrue(String::contains('ok', 'test tested ok today'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(String::endsWith(' foo', 'foo foo'));
        $this->assertTrue(String::endsWith('ar', 'bar bar'));
        $this->assertFalse(String::endsWith('o FOO', 'foo foo'));
    }

    public function testGsub()
    {
        $this->assertEquals('1bc1bc', String::gsub('abcabc', 'a', 1));
        $this->assertEquals('ABcABc', String::gsub('abcabc', 'ab', 'AB'));
    }

    public function testReplaceByArray()
    {
        $s = 'abcdefghijklmnopqrstuvwxyz';
        $this->assertEquals(
            'abcdEfghijklmNopqrStuvwxyz',
            String::replaceByArray($s, array('n' => 'N', 'e' => 'E', 's' => 'S')));
        $this->assertEquals(
            'abcdefghijkl | mnopqrstuvwxyz',
            String::replaceByArray($s, array('mno' => ' | mno')));
    }

    public function testRetrieveNumber()
    {
        $this->assertEquals(2, String::retrieveNumber('2 records'));
        $this->assertEquals(100, String::retrieveNumber('there was 100 records'));
    }

    public function testStartsWith()
    {
        $this->assertTrue(String::startsWith('foo f', 'foo foo'));
        $this->assertTrue(String::startsWith('bar', 'bar bar'));
        $this->assertFalse(String::startsWith('foo F', 'foo foo'));
    }

    public function testSub()
    {
        $this->assertEquals('1bcabc', String::sub('abcabc', 'a', 1));
        $this->assertEquals('1bc1bc', String::sub('abcabc', 'a', 1, 2));
    }

    public function testTimes()
    {
        $this->assertEquals('aaaaaa', String::times('a', 6));
        $this->assertEquals('abcabcabc', String::times('abc', 3));
    }

    public function testToArray()
    {
        $this->assertEquals(array('a','b','c'), String::toArray('abc'));
    }
}
