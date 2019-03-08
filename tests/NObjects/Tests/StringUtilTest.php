<?php
namespace NObjects\Tests;

use NObjects\StringUtil;

/**
 * Unit tests for StringUtil object.
 *
 * @access private
 **/
class StringUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testContains()
    {
        $this->assertTrue(StringUtil::contains('@', '@test'));
        $this->assertTrue(StringUtil::contains('ok', 'test tested ok today'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(StringUtil::endsWith(' foo', 'foo foo'));
        $this->assertTrue(StringUtil::endsWith('ar', 'bar bar'));
        $this->assertFalse(StringUtil::endsWith('o FOO', 'foo foo'));
    }

    public function testGsub()
    {
        $this->assertEquals('1bc1bc', StringUtil::gsub('abcabc', 'a', 1));
        $this->assertEquals('ABcABc', StringUtil::gsub('abcabc', 'ab', 'AB'));
    }

    public function testReplaceByArray()
    {
        $s = 'abcdefghijklmnopqrstuvwxyz';
        $this->assertEquals(
            'abcdEfghijklmNopqrStuvwxyz',
            StringUtil::replaceByArray($s, array('n' => 'N', 'e' => 'E', 's' => 'S'))
        );
        $this->assertEquals(
            'abcdefghijkl | mnopqrstuvwxyz',
            StringUtil::replaceByArray($s, array('mno' => ' | mno'))
        );
    }

    public function testRetrieveNumber()
    {
        $this->assertEquals(2, StringUtil::retrieveNumber('2 records'));
        $this->assertEquals(100, StringUtil::retrieveNumber('there was 100 records'));
    }

    public function testStartsWith()
    {
        $this->assertTrue(StringUtil::startsWith('foo f', 'foo foo'));
        $this->assertTrue(StringUtil::startsWith('bar', 'bar bar'));
        $this->assertFalse(StringUtil::startsWith('foo F', 'foo foo'));
    }

    public function testSub()
    {
        $this->assertEquals('1bcabc', StringUtil::sub('abcabc', 'a', 1));
        $this->assertEquals('1bc1bc', StringUtil::sub('abcabc', 'a', 1, 2));
    }

    public function testTimes()
    {
        $this->assertEquals('aaaaaa', StringUtil::times('a', 6));
        $this->assertEquals('abcabcabc', StringUtil::times('abc', 3));
    }

    public function testToArray()
    {
        $this->assertEquals(array('a','b','c'), StringUtil::toArray('abc'));
    }

    public function testMailTo()
    {
        $to = 'luke@example.com';
        $cc = 'yoda@example.com';
        $bcc = 'han@example.com';
        $subject = 'Use the force...';
        $body = 'Feel the force!';

        $mailto = 'mailto:'.$to;
        $this->assertEquals($mailto, StringUtil::mailTo($to));

        $mailto .= '?subject=' . rawurlencode($subject);
        $this->assertEquals($mailto, StringUtil::mailTo($to, $subject));

        $mailto .= '&body=' . rawurlencode($body);
        $this->assertEquals($mailto, StringUtil::mailTo($to, $subject, $body));

        $mailto = str_replace('?', '?cc=' . $cc . '&', $mailto);
        $this->assertEquals($mailto, StringUtil::mailTo($to, $subject, $body, $cc));

        $mailto = str_replace('&subject=', '&bcc=' . $bcc . '&subject=', $mailto);
        $this->assertEquals($mailto, StringUtil::mailTo($to, $subject, $body, $cc, $bcc));
    }
}
