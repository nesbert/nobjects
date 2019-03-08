<?php

namespace NObjects\Tests;

/**
 * Unit tests for StringUtil object.
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * setUp runs before each test.
     */
    protected function setUp()
    {
        if (version_compare(phpversion(), '7.0.0', '>=')) {
            $this->markTestSkipped('This test cannot run on PHP 7.0+');

        }

        parent::setUp();
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::contains instead.
     */
    public function testContains()
    {
        $this->assertTrue(\NObjects\String::contains('@', '@test'));
        $this->assertTrue(\NObjects\String::contains('ok', 'test tested ok today'));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::endsWith instead.
     */
    public function testEndsWith()
    {
        $this->assertTrue(\NObjects\String::endsWith(' foo', 'foo foo'));
        $this->assertTrue(\NObjects\String::endsWith('ar', 'bar bar'));
        $this->assertFalse(\NObjects\String::endsWith('o FOO', 'foo foo'));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::gsub instead.
     */
    public function testGsub()
    {
        $this->assertEquals('1bc1bc', \NObjects\String::gsub('abcabc', 'a', 1));
        $this->assertEquals('ABcABc', \NObjects\String::gsub('abcabc', 'ab', 'AB'));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::replaceByArray instead.
     */
    public function testReplaceByArray()
    {
        $s = 'abcdefghijklmnopqrstuvwxyz';
        $this->assertEquals(
            'abcdEfghijklmNopqrStuvwxyz',
            \NObjects\String::replaceByArray($s, array('n' => 'N', 'e' => 'E', 's' => 'S'))
        );
        $this->assertEquals(
            'abcdefghijkl | mnopqrstuvwxyz',
            \NObjects\String::replaceByArray($s, array('mno' => ' | mno'))
        );
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::retrieveNumber instead.
     */
    public function testRetrieveNumber()
    {
        $this->assertEquals(2, \NObjects\String::retrieveNumber('2 records'));
        $this->assertEquals(100, \NObjects\String::retrieveNumber('there was 100 records'));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::startsWith instead.
     */
    public function testStartsWith()
    {
        $this->assertTrue(\NObjects\String::startsWith('foo f', 'foo foo'));
        $this->assertTrue(\NObjects\String::startsWith('bar', 'bar bar'));
        $this->assertFalse(\NObjects\String::startsWith('foo F', 'foo foo'));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::sub instead.
     */
    public function testSub()
    {
        $this->assertEquals('1bcabc', \NObjects\String::sub('abcabc', 'a', 1));
        $this->assertEquals('1bc1bc', \NObjects\String::sub('abcabc', 'a', 1, 2));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::times instead.
     */
    public function testTimes()
    {
        $this->assertEquals('aaaaaa', \NObjects\String::times('a', 6));
        $this->assertEquals('abcabcabc', \NObjects\String::times('abc', 3));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::toArray instead.
     */
    public function testToArray()
    {
        $this->assertEquals(array('a','b','c'), \NObjects\String::toArray('abc'));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation NObjects\String is deprecated. Migrate to using NObjects\StringUtil::mailTo instead.
     */
    public function testMailTo()
    {
        $to = 'luke@example.com';
        $cc = 'yoda@example.com';
        $bcc = 'han@example.com';
        $subject = 'Use the force...';
        $body = 'Feel the force!';

        $mailto = 'mailto:'.$to;
        $this->assertEquals($mailto, \NObjects\String::mailTo($to));

        $mailto .= '?subject=' . rawurlencode($subject);
        $this->assertEquals($mailto, \NObjects\String::mailTo($to, $subject));

        $mailto .= '&body=' . rawurlencode($body);
        $this->assertEquals($mailto, \NObjects\String::mailTo($to, $subject, $body));

        $mailto = str_replace('?', '?cc=' . $cc . '&', $mailto);
        $this->assertEquals($mailto, \NObjects\String::mailTo($to, $subject, $body, $cc));

        $mailto = str_replace('&subject=', '&bcc=' . $bcc . '&subject=', $mailto);
        $this->assertEquals($mailto, \NObjects\String::mailTo($to, $subject, $body, $cc, $bcc));
    }
}
