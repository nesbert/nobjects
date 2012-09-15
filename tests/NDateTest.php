<?php
class NDateTest extends PHPUnit_Framework_TestCase
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
        
        $this->assertEquals($now, NDate::datetime());
        $this->assertEquals($tomorrow, NDate::datetime(time() + NDate::DAY));
        $this->assertEquals($yesterday, NDate::datetime(time() - NDate::DAY));
        $this->assertEquals($tomorrow, NDate::datetime(strtotime('+1day')));
        $this->assertEquals($yesterday, NDate::datetime(strtotime('-1day')));
        $this->assertEquals('2010-03-06 12:55:23', NDate::datetime($datetime));
    }

    public function testGmtimestamp()
    {
        $now = strtotime(gmdate('Y-m-d H:i:s'));
        $this->assertEquals($now, NDate::gmtimestamp());
    }

    public function testGmdatetime()
    {
        $now = gmdate('Y-m-d H:i:s');
        $tomorrow = gmdate('Y-m-d H:i:s', strtotime('+1day'));
        $yesterday = gmdate('Y-m-d H:i:s', strtotime('-1day'));
        $this->assertEquals($now, NDate::gmdatetime());
        $this->assertEquals($tomorrow, NDate::gmdatetime(strtotime('+1day')));
        $this->assertEquals($yesterday, NDate::gmdatetime(strtotime('-1day')));
        $this->assertEquals($yesterday, NDate::gmdatetime(NDate::datetime(strtotime('-1day'))));
    }

    public function testTimeAgo()
    {
        $this->assertEquals('1 day', NDate::timeSince(strtotime('-1day')));
        $this->assertEquals('5 days', NDate::timeSince(strtotime('-5day')));
        $this->assertEquals('1 week', NDate::timeSince(strtotime('-8day')));
        $this->assertFalse(NDate::timeSince(strtotime('-4weeks')));
        $this->assertFalse(@NDate::timeSince());
        $this->assertFalse(NDate::timeSince(time() + NDate::MINUTE));
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
        $this->assertEquals($array, NDate::range('2010-05-28', '2010-06-04'));
        $this->assertEquals(array_flip($array), NDate::range('2010-05-28', '2010-06-04', 'D', 'Y-m-d'));
        $this->assertTrue(is_array(NDate::range(strtotime('-1week'))));
    }
}
