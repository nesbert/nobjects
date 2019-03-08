<?php
namespace NObjects\Tests;

use NObjects\Validate;

/**
 * Unit tests for Validate object.
 *
 * @access private
 **/
class ValidateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validate
     */
    protected $o;

    /**
     * Detects usage of the MUSL C library (Alpine docker images)
     * @var bool
     */
    static $isMusl = false;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        ob_start();
        phpinfo(1);
        $info = ob_get_contents();
        ob_end_clean();

        self::$isMusl = false !== preg_match('/\wmusl\w/', $info);
    }

    protected function setUp()
    {
        $this->o = new Validate;
    }

    public function testIsEmail()
    {
        $this->assertTrue(Validate::isEmail('test@example.org'));
        $this->assertTrue(Validate::isEmail('Test.Test@example.org'));
        $this->assertTrue(Validate::isEmail('Test.Test@test.example.org'));
        $this->assertTrue(Validate::isEmail('test.test@test.example.org'));
        $this->assertTrue(Validate::isEmail('1test.test@test.example.org'));
        $this->assertTrue(Validate::isEmail('test.test1@test.example.org'));
        $this->assertTrue(Validate::isEmail('1test.2test@test.example.org'));
        $this->assertTrue(Validate::isEmail('1test.2test@test1.example.org'));

        $this->assertTrue(Validate::isEmail('test@example.info'));
        $this->assertTrue(Validate::isEmail('Test.Test@example.info'));
        $this->assertTrue(Validate::isEmail('Test.Test@test.example.info'));
        $this->assertTrue(Validate::isEmail('test.test@test.example.info'));
        $this->assertTrue(Validate::isEmail('1test.test@test.example.info'));
        $this->assertTrue(Validate::isEmail('test.test1@test.example.info'));
        $this->assertTrue(Validate::isEmail('1test.2test@test.example.info'));
        $this->assertTrue(Validate::isEmail('1test.2test@test1.example.info'));

        $this->assertTrue(Validate::isEmail('test@example.us'));
        $this->assertTrue(Validate::isEmail('Test.Test@example.us'));
        $this->assertTrue(Validate::isEmail('Test.Test@test.example.us'));
        $this->assertTrue(Validate::isEmail('test.test@test.example.us'));
        $this->assertTrue(Validate::isEmail('1test.test@test.example.us'));
        $this->assertTrue(Validate::isEmail('test.test1@test.example.us'));
        $this->assertTrue(Validate::isEmail('1test.2test@test.example.us'));
        $this->assertTrue(Validate::isEmail('1test.2test@test1.example.us'));

        $this->assertTrue(Validate::isEmail('niceandsimple@example.com'));
        $this->assertTrue(Validate::isEmail('very.common@example.com'));
        $this->assertTrue(Validate::isEmail('a.little.lengthy.but.fine@dept.example.com'));
        $this->assertTrue(Validate::isEmail('disposable.style.email.with+symbol@example.com'));
        $this->assertTrue(Validate::isEmail('user@[IPv6:2001:db8:1ff::a0b:dbd0]'));
//        $this->assertTrue(Validate::isEmail('"much.more unusual"@example.com'));
        $this->assertTrue(Validate::isEmail('"very.unusual.@.unusual.com"@example.com'));
        $this->assertTrue(Validate::isEmail('"very.(),:;<>[]\".VERY.\"very@\\ \"very\".unusual"@strange.example.com'));
//        $this->assertTrue(Validate::isEmail('0@a'));
//        $this->assertTrue(Validate::isEmail('postbox@com'));
        $this->assertTrue(Validate::isEmail('!#$%&\'*+-/=?^_`{}|~@example.org'));
//        $this->assertTrue(Validate::isEmail('"()<>[]:,;@\\\"!#$%&\'*+-/=?^_`{}| ~  ? ^_`{}|~.a"@example.org'));
        $this->assertTrue(Validate::isEmail('""@example.org'));

        $this->assertFalse(Validate::isEmail('Abc.example.com'));
        $this->assertFalse(Validate::isEmail('Abc.@example.com'));
        $this->assertFalse(Validate::isEmail('Abc..123@example.com'));
        $this->assertFalse(Validate::isEmail('a"b(c)d,e:f;g<h>i[j\k]l@example.com'));
        $this->assertFalse(Validate::isEmail('just"not"right@example.com'));
        $this->assertFalse(Validate::isEmail('this is"not\allowed@example.com'));
        $this->assertFalse(Validate::isEmail('this\ still\"not\\allowed@example.com'));
        $this->assertFalse(Validate::isEmail('@_example.org'));
    }

    public function testIsEmailWithDNS()
    {
        if (self::$isMusl) {
            $this->markTestSkipped("checkdnsrr always returns 1 with MUSL C library");
        }
        // check domain name
        $email = 'test@notavaliddomainname.com';
        $this->assertTrue(Validate::isEmail($email));
        $this->assertTrue(Validate::isEmail('test@gmail.com', true));
        $this->assertFalse(Validate::isEmail($email, true));
    }

    public function testIsEmailMd5()
    {
        $this->assertTrue(Validate::isEmailMd5(md5('Test.Test@example.com')));
        $this->assertTrue(Validate::isEmailMd5(md5('han@example.com')));
        $this->assertFalse(Validate::isEmailMd5('Test.Test@example.com'));
        $this->assertFalse(Validate::isEmailMd5('test@example.com'));
    }

    public function testIsIp()
    {
        $ipv4="82.237.3.3";
        $ipv6="2a01:e35:aaa4:6860:a5e7:5ba9:965e:cc93";
        $ipv4Private = "255.255.255.255";
        $ipv6Private = "::1";
        $real = "10.10.10.10";
        $fake = "3342423423";

        $this->assertTrue(Validate::isIp($ipv4));
        $this->assertTrue(Validate::isIp($ipv6));
        $this->assertTrue(Validate::isIp($ipv4Private));
        $this->assertTrue(Validate::isIp($ipv6Private));
        $this->assertTrue(Validate::isIp($real));
        $this->assertFalse(Validate::isIp($fake));
    }
    
    public function testIsUrl()
    {
        $this->assertTrue(Validate::isUrl('http://example.org'));
        $this->assertTrue(Validate::isUrl('http://www.example.org'));
        $this->assertTrue(Validate::isUrl('https://example.org'));
        $this->assertTrue(Validate::isUrl('https://www.example.org'));
        $this->assertTrue(Validate::isUrl('http://localhost'));
        $this->assertTrue(Validate::isUrl('https://localhost'));
        $this->assertTrue(Validate::isUrl('ftp://example.org'));
        $this->assertTrue(Validate::isUrl('ftp://www.example.org'));
        $this->assertTrue(Validate::isUrl('ftp://localhost'));
        $this->assertTrue(Validate::isUrl('http://example.org/'));
        $this->assertTrue(Validate::isUrl('http://example.org/some/stuff'));
        $this->assertTrue(Validate::isUrl('http://localhost/some/stuff'));
        $this->assertTrue(Validate::isUrl('http://localhost/some/stuff', true));

        $this->assertFalse(Validate::isUrl('localhost'));
        $this->assertFalse(Validate::isUrl('example.org'));
        $this->assertFalse(Validate::isUrl('http:example.org'));
        $this->assertFalse(Validate::isUrl('https:example.org'));
        $this->assertFalse(Validate::isUrl('http:///example.org'));
        $this->assertFalse(Validate::isUrl('https:///example.org'));
        $this->assertFalse(Validate::isUrl('ftp:example.org'));
        $this->assertFalse(Validate::isUrl('ftp:example.org'));
        $this->assertFalse(Validate::isUrl('http://example.org', true));
    }

    public function testIsAlpha()
    {
        $this->assertTrue(Validate::isAlpha('Test'));
        $this->assertTrue(Validate::isAlpha('somereallongtexthere'));

        $this->assertFalse(Validate::isAlpha('Test#1'));
        $this->assertFalse(Validate::isAlpha('2Test'));
        $this->assertFalse(Validate::isAlpha('Test Test'));
        $this->assertFalse(Validate::isAlpha('&amp;'));
        $this->assertFalse(Validate::isAlpha(123));
        $this->assertFalse(Validate::isAlpha(true));
        $this->assertFalse(Validate::isAlpha(false));
        $this->assertFalse(Validate::isAlpha(1));
        $this->assertFalse(Validate::isAlpha(0));
    }

    public function testIsAlphaNumeric()
    {
        $this->assertTrue(Validate::isAlphaNumeric('Test'));
        $this->assertTrue(Validate::isAlphaNumeric('somereallongtexthere'));
        $this->assertTrue(Validate::isAlphaNumeric(123));
        $this->assertTrue(Validate::isAlphaNumeric(1));
        $this->assertTrue(Validate::isAlphaNumeric(0));

        $this->assertFalse(Validate::isAlphaNumeric('Test#1'));
        $this->assertFalse(Validate::isAlpha('2Test'));
        $this->assertFalse(Validate::isAlphaNumeric('Test Test'));
        $this->assertFalse(Validate::isAlphaNumeric('&amp;'));
        $this->assertFalse(Validate::isAlphaNumeric(true));
        $this->assertFalse(Validate::isAlphaNumeric(false));
    }

    public function testIsNumber()
    {
        $this->assertTrue(Validate::isNumber('123'));
        $this->assertTrue(Validate::isNumber('123.00'));
        $this->assertTrue(Validate::isNumber('123.001'));
        $this->assertTrue(Validate::isNumber(123));
        $this->assertTrue(Validate::isNumber(1));
        $this->assertTrue(Validate::isNumber(0));

        $this->assertFalse(Validate::isNumber('1e'));
        $this->assertFalse(Validate::isNumber('1,000,00.00'));
        $this->assertFalse(Validate::isNumber(array()));
    }

    public function testIsPositiveNumber()
    {
        $this->assertTrue(Validate::isPositiveNumber('123'));
        $this->assertTrue(Validate::isPositiveNumber('123.00'));
        $this->assertTrue(Validate::isPositiveNumber('123.001'));
        $this->assertTrue(Validate::isPositiveNumber(123));
        $this->assertTrue(Validate::isPositiveNumber(1));

        $this->assertFalse(Validate::isPositiveNumber(0));
        $this->assertFalse(Validate::isPositiveNumber(-15));
        $this->assertFalse(Validate::isPositiveNumber('-15'));
        $this->assertFalse(Validate::isPositiveNumber('-15.00'));
        $this->assertFalse(Validate::isNumber('1e'));
        $this->assertFalse(Validate::isNumber('1,000,00.00'));
        $this->assertFalse(Validate::isNumber(array()));
    }

    public function testIsMatch()
    {
        $this->assertTrue(Validate::isMatch('123', '123'));
        $this->assertTrue(Validate::isMatch(123, 123));
        $this->assertTrue(Validate::isMatch(10.5, 10.50));
        $this->assertTrue(Validate::isMatch('test', 'test'));
        $this->assertTrue(Validate::isMatch(true, true));
        $this->assertTrue(Validate::isMatch(0, 0));
        $this->assertTrue(Validate::isMatch(false, false));

        $this->assertFalse(Validate::isMatch('123', 123));
        $this->assertFalse(Validate::isMatch(true, '1'));
        $this->assertFalse(Validate::isMatch(true, 1));
        $this->assertFalse(Validate::isMatch(true, 'test'));
        $this->assertFalse(Validate::isMatch('123', '1234'));
        $this->assertFalse(Validate::isMatch(123, 1234));
    }

    public function testIsBetween()
    {
        $this->assertTrue(Validate::isBetween('10', '1', '100'));
        $this->assertTrue(Validate::isBetween(10, 1, 100));

        $this->assertFalse(Validate::isBetween('101', '1', '100'));
        $this->assertFalse(Validate::isBetween(101, 1, 100));
    }

    public function testIsDateString()
    {
        $this->assertTrue(Validate::isDateString(\NObjects\Date::toISO8601()));
        $this->assertTrue(Validate::isDateString(\NObjects\Date::datetime()));

        $this->assertFalse(Validate::isDateString('Some date 10/10/2012'));
        $this->assertFalse(Validate::isDateString(101));
        $this->assertFalse(Validate::isDateString(time()));
    }

    public function testIsDatetimeString()
    {
        $this->assertTrue(Validate::isDatetimeString(\NObjects\Date::datetime()));

        $this->assertFalse(Validate::isDatetimeString(\NObjects\Date::toISO8601()));
        $this->assertFalse(Validate::isDatetimeString(time()));
        $this->assertFalse(Validate::isDatetimeString('Some date 10/10/2012'));
        $this->assertFalse(Validate::isDatetimeString(101));
    }

    public function testIsLength()
    {
        $array = array(1,2,3,4);
        $this->assertTrue(Validate::isLength('test', '4'));
        $this->assertTrue(Validate::isLength('test', 4));
        $this->assertTrue(Validate::isLength($array, '4'));
        $this->assertTrue(Validate::isLength($array, 4));

        $this->assertFalse(Validate::isLength('test', '1'));
        $this->assertFalse(Validate::isLength('test', 1));
        $this->assertFalse(Validate::isLength($array, '5'));
        $this->assertFalse(Validate::isLength($array, 5));
        $this->assertFalse(Validate::isLength(null, '1'));
    }

    public function testIsLengthBetween()
    {
        $array = array(1,2,3,4);
        $this->assertTrue(Validate::isLengthBetween('test', '4', '10'));
        $this->assertTrue(Validate::isLengthBetween('test', 4, 10));
        $this->assertTrue(Validate::isLengthBetween($array, '4', '10'));
        $this->assertTrue(Validate::isLengthBetween($array, 4, 10));

        $this->assertFalse(Validate::isLengthBetween('test', '1', '3'));
        $this->assertFalse(Validate::isLengthBetween('test', 1, '3'));
        $this->assertFalse(Validate::isLengthBetween($array, '5', '10'));
        $this->assertFalse(Validate::isLengthBetween($array, 5, 10));
        $this->assertFalse(Validate::isLengthBetween(null, '1', '3'));
    }

    public function testIsRegex()
    {
        $this->assertTrue(Validate::isRegex('/robotech/i'));
        $this->assertTrue(Validate::isRegex('@robotech@'));
        $this->assertTrue(Validate::isRegex('/^[0-9]/'));

        $this->assertFalse(Validate::isRegex('Test#1'));
        $this->assertFalse(Validate::isRegex('2Test'));
        $this->assertFalse(Validate::isRegex('Test Test'));
        $this->assertFalse(Validate::isRegex('123456'));
        $this->assertFalse(Validate::isRegex(123456));
    }

    public function testIsEven()
    {
        $this->assertTrue(Validate::isEven(0));
        $this->assertTrue(Validate::isEven('2'));
        $this->assertTrue(Validate::isEven(2));
        $this->assertTrue(Validate::isEven(4));
        $this->assertTrue(Validate::isEven('6'));
        $this->assertTrue(Validate::isEven(6));
        $this->assertTrue(Validate::isEven(8));
        $this->assertTrue(Validate::isEven(10));
        $this->assertTrue(Validate::isEven(20));
        $this->assertTrue(Validate::isEven(30));
        $this->assertTrue(Validate::isEven(50));
        $this->assertTrue(Validate::isEven(88));
        $this->assertTrue(Validate::isEven(100));
        $this->assertTrue(Validate::isEven(1000000));

        $this->assertFalse(Validate::isEven('1'));
        $this->assertFalse(Validate::isEven(1));
        $this->assertFalse(Validate::isEven(3));
        $this->assertFalse(Validate::isEven(5));
        $this->assertFalse(Validate::isEven(7));
        $this->assertFalse(Validate::isEven('5'));
        $this->assertFalse(Validate::isEven(5));
        $this->assertFalse(Validate::isEven(55));
        $this->assertFalse(Validate::isEven(99));
        $this->assertFalse(Validate::isEven(101));
        $this->assertFalse(Validate::isEven(1000001));
        $this->assertFalse(Validate::isEven('test123'));
    }

    public function testIsOdd()
    {
        $this->assertTrue(Validate::isOdd('1'));
        $this->assertTrue(Validate::isOdd(1));
        $this->assertTrue(Validate::isOdd(3));
        $this->assertTrue(Validate::isOdd(5));
        $this->assertTrue(Validate::isOdd(7));
        $this->assertTrue(Validate::isOdd('5'));
        $this->assertTrue(Validate::isOdd(5));
        $this->assertTrue(Validate::isOdd(55));
        $this->assertTrue(Validate::isOdd(99));
        $this->assertTrue(Validate::isOdd(101));
        $this->assertTrue(Validate::isOdd(1000001));

        $this->assertFalse(Validate::isOdd(0));
        $this->assertFalse(Validate::isOdd('2'));
        $this->assertFalse(Validate::isOdd(2));
        $this->assertFalse(Validate::isOdd(4));
        $this->assertFalse(Validate::isOdd('6'));
        $this->assertFalse(Validate::isOdd(6));
        $this->assertFalse(Validate::isOdd(8));
        $this->assertFalse(Validate::isOdd(10));
        $this->assertFalse(Validate::isOdd(20));
        $this->assertFalse(Validate::isOdd(30));
        $this->assertFalse(Validate::isOdd(50));
        $this->assertFalse(Validate::isOdd(88));
        $this->assertFalse(Validate::isOdd(100));
        $this->assertFalse(Validate::isOdd(1000000));
        $this->assertFalse(Validate::isOdd('test123'));
    }

    public function testIsAjax()
    {
        $this->assertFalse(Validate::isAjaxRequest());
        $_REQUEST['_AJAX_'] = 1;
        $this->assertTrue(Validate::isAjaxRequest());
        unset($_REQUEST['_AJAX_']);

        $this->assertFalse(Validate::isAjaxRequest());
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $this->assertTrue(Validate::isAjaxRequest());
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    public function testIsHash()
    {
        $this->assertTrue(Validate::isAssociativeArray(array('test' => 1234)));
        $this->assertTrue(Validate::isAssociativeArray(array('test' => 1234, '123')));

        $this->assertFalse(Validate::isAssociativeArray(array(1,2,3,4)));
        $this->assertFalse(Validate::isAssociativeArray('1234'));
        $this->assertFalse(Validate::isAssociativeArray(1234));
    }

    public function testIsInString()
    {
        $this->assertTrue(Validate::inString('robo', 'robotech'));
        $this->assertTrue(Validate::inString(1, '123456'));

        $this->assertFalse(Validate::inString('test', 'robotech'));
        $this->assertFalse(Validate::inString(10, '123456'));
    }
}
