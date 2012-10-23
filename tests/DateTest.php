<?php
namespace NObjects\Tests;
use NObjects\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        date_default_timezone_set('America/Los_Angeles');
    }

    public function testDatetime()
    {
        $now = date('Y-m-d H:i:s');
        $tomorrow = date('Y-m-d H:i:s', strtotime('+1day'));
        $yesterday = date('Y-m-d H:i:s', strtotime('-1day'));
        $datetime = array();
        $datetime['hour'] = 12;
        $datetime['minute'] = 55;
        $datetime['second'] = 23;
        $datetime['month'] = 3;
        $datetime['day'] = 6;
        $datetime['year'] = 2010;
        
        $this->assertEquals($now, Date::datetime());
        $this->assertEquals($now, Date::datetime(null));
        $this->assertEquals($now, Date::datetime(time()));
        $this->assertEquals($tomorrow, Date::datetime(time() + Date::DAY));
        $this->assertEquals($yesterday, Date::datetime(time() - Date::DAY));
        $this->assertEquals($tomorrow, Date::datetime(strtotime('+1day')));
        $this->assertEquals($yesterday, Date::datetime(strtotime('-1day')));
        $this->assertEquals('2010-03-06 12:55:23', Date::datetime($datetime));
        $datetime['hour'] = 10;
        $datetime['ampm'] = 'pm';
        $this->assertEquals('2010-03-06 22:55:23', Date::datetime($datetime));
        $datetime['hour'] = 1;
        $datetime['ampm'] = 'am';
        $this->assertEquals('2010-03-06 01:55:23', Date::datetime($datetime));
    }

    public function testGmtimestamp()
    {
        $now = strtotime(gmdate('Y-m-d H:i:s'));
        $this->assertEquals($now, Date::gmtimestamp());
    }

    public function testGmdatetime()
    {
        $now = gmdate('Y-m-d H:i:s');
        $tomorrow = gmdate('Y-m-d H:i:s', strtotime('+1day'));
        $yesterday = gmdate('Y-m-d H:i:s', strtotime('-1day'));
        $this->assertEquals($now, Date::gmdatetime());
        $this->assertEquals($tomorrow, Date::gmdatetime(strtotime('+1day')));
        $this->assertEquals($yesterday, Date::gmdatetime(strtotime('-1day')));
        $this->assertEquals($yesterday, Date::gmdatetime(Date::datetime(strtotime('-1day'))));
    }

    public function testDatetimeISO8601()
    {
        $now = date('c');
        $tomorrow = date('c', strtotime('+1day'));
        $yesterday = date('c', strtotime('-1day'));
        $datetime = array();
        $datetime['hour'] = 12;
        $datetime['minute'] = 55;
        $datetime['second'] = 23;
        $datetime['month'] = 3;
        $datetime['day'] = 6;
        $datetime['year'] = 2010;

        $this->assertEquals($now, Date::datetimeISO8601());
        $this->assertEquals($tomorrow, Date::datetimeISO8601(time() + Date::DAY));
        $this->assertEquals($yesterday, Date::datetimeISO8601(time() - Date::DAY));
        $this->assertEquals($tomorrow, Date::datetimeISO8601(strtotime('+1day')));
        $this->assertEquals($yesterday, Date::datetimeISO8601(strtotime('-1day')));
        $this->assertEquals('2010-03-06T12:55:23-08:00', Date::datetimeISO8601($datetime));
    }

    public function testGmdatetimeISO8601()
    {
        $this->assertEquals(str_replace('+00:00', 'Z', gmdate('c')), Date::gmdatetimeISO8601());
        $tomorrow = str_replace(' ', 'T', gmdate('Y-m-d H:i:s', strtotime('+1day'))).'Z';
        $this->assertEquals($tomorrow, Date::gmdatetimeISO8601(strtotime('+1day')));
        $yesterday = str_replace(' ', 'T', gmdate('Y-m-d H:i:s', strtotime('-1day'))).'Z';
        $this->assertEquals($yesterday, Date::gmdatetimeISO8601(strtotime('-1day')));
    }

    public function testToISO8601()
    {
        // default GMT
        $this->assertEquals('2012-10-09T17:35:30Z', Date::toISO8601('2012-10-09 17:35:30'));
        $this->assertEquals('2012-08-17T01:43:00Z', Date::toISO8601('2012-08-16 18:43:00 -0700'));

        // America/Los_Angeles
        $this->assertEquals('2012-10-09T17:35:30-07:00', Date::toISO8601('2012-10-09 17:35:30', 'America/Los_Angeles'));
        $this->assertEquals('2012-08-16T18:43:00-07:00', Date::toISO8601('2012-08-16 18:43:00 -0700', 'America/Los_Angeles'));
        
        // America/Chicago
        $this->assertEquals('2012-10-09T17:35:30-05:00', Date::toISO8601('2012-10-09 17:35:30', 'America/Chicago'));
        $this->assertEquals('2012-08-16T20:43:00-05:00', Date::toISO8601('2012-08-16 18:43:00 -0700', 'America/Chicago'));
        
        // America/New_York
        $this->assertEquals('2012-10-09T17:35:30-04:00', Date::toISO8601('2012-10-09 17:35:30', 'America/New_York'));
        $this->assertEquals('2012-08-16T21:43:00-04:00', Date::toISO8601('2012-08-16 18:43:00 -0700', 'America/New_York'));
    }

    public function testTimeSince()
    {
        $this->assertEquals('30 seconds', Date::timeSince(strtotime('-30 seconds')));
        $this->assertEquals('30 seconds', Date::timeSince('-30 seconds'));
        $this->assertEquals('30 minutes', Date::timeSince(strtotime('-30 minutes')));
        $this->assertEquals('30 minutes', Date::timeSince('-30 minutes'));
        $this->assertEquals('12 hours', Date::timeSince(strtotime('-12 hours')));
        $this->assertEquals('12 hours', Date::timeSince('-12 hours'));
        $this->assertEquals('1 day', Date::timeSince(strtotime('-1day')));
        $this->assertEquals('1 day', Date::timeSince('-1day'));
        $this->assertEquals('5 days', Date::timeSince(strtotime('-5day')));
        $this->assertEquals('5 days', Date::timeSince('-5day'));
        $this->assertEquals('1 week', Date::timeSince(strtotime('-8day')));
        $this->assertEquals('1 week', Date::timeSince('-8day'));
        $this->assertFalse(Date::timeSince(strtotime('-4weeks')));
        $this->assertFalse(Date::timeSince('-4weeks'));
        $this->assertFalse(@Date::timeSince());
        $this->assertFalse(Date::timeSince(time() + Date::MINUTE));
    }

    public function testRange()
    {
        $array = array(
            '2010-05-28' => 'Fri',
            '2010-05-29' => 'Sat',
            '2010-05-30' => 'Sun',
            '2010-05-31' => 'Mon',
            '2010-06-01' => 'Tue',
            '2010-06-02' => 'Wed',
            '2010-06-03' => 'Thu',
            '2010-06-04' => 'Fri',
        );
        $this->assertEquals($array, Date::range('2010-05-28', '2010-06-04'));
        $this->assertEquals(array_flip($array), Date::range('2010-05-28', '2010-06-04', 'D', 'Y-m-d'));
        $this->assertTrue(is_array(Date::range(strtotime('-1week'))));
    }

    public function testMilliseconds()
    {
        $this->assertEquals(round(microtime(true)*1000), Date::milliseconds());
    }
}
