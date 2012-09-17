<?php
namespace NObjects\Tests; 

class DateTest extends \PHPUnit_Framework_TestCase
{
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
        $this->assertEquals($tomorrow, Date::datetime(time() + Date::DAY));
        $this->assertEquals($yesterday, Date::datetime(time() - Date::DAY));
        $this->assertEquals($tomorrow, Date::datetime(strtotime('+1day')));
        $this->assertEquals($yesterday, Date::datetime(strtotime('-1day')));
        $this->assertEquals('2010-03-06 12:55:23', Date::datetime($datetime));
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

    public function testTimeAgo()
    {
        $this->assertEquals('1 day', Date::timeSince(strtotime('-1day')));
        $this->assertEquals('5 days', Date::timeSince(strtotime('-5day')));
        $this->assertEquals('1 week', Date::timeSince(strtotime('-8day')));
        $this->assertFalse(Date::timeSince(strtotime('-4weeks')));
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
}
