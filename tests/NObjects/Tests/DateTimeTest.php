<?php
namespace NObjects\Tests;

use NObjects\DateTime;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTime
     */
    private $o;

    /**
     * @var string
     */
    private $datetime;

    public function setUp()
    {
        date_default_timezone_set('America/Los_Angeles');

        $this->datetime = '2007-05-02 18:43:55';
        $this->o = new DateTime($this->datetime);
    }

    public function testGetTimestamp()
    {
        $this->assertEquals(strtotime($this->datetime), $this->o->getTimestamp());

        $o = new DateTime($time = time());
        $this->assertEquals($time, $o->getTimestamp());

        $o = new DateTime($this->datetime . '.1234');
        $this->assertEquals(strtotime($this->datetime), $o->getTimestamp());

        try {
            $o = new DateTime('0000-00-00 00:00:00');
            $this->assertEquals($time, $o->getTimestamp());
            $this->fail('Exception expected!');
        } catch (\Exception $e) {
            $this->assertEquals(
                'DateTime::format(): The DateTime object has not been correctly initialized by its constructor',
                $e->getMessage()
            );
        }
    }

    public function testGetDate()
    {
        $this->assertEquals(date('Y-m-d', strtotime($this->datetime)), $this->o->getDate());
    }

    public function testGetTime()
    {
        $this->assertEquals(date('H:i:s', strtotime($this->datetime)), $this->o->getTime());
    }

    public function testGetDateTime()
    {
        $this->assertEquals($this->datetime, $this->o->getDateTime());
        $this->assertEquals($this->datetime, (string)$this->o);
    }

    public function testGetGlobal()
    {
        $this->assertEquals(date(DateTime::getGlobalFormat(), strtotime($this->datetime)), $this->o->getGlobal());
    }

    public function testDiff()
    {
        $diff = $this->o->diff();

        $this->assertTrue($diff instanceof \DateInterval);
    }

    public function testGetAge()
    {
        $years = $this->o->getAge();

        $now = new DateTime();
        $int = $now->diff(new DateTime($this->datetime));

        $this->assertEquals($int->y, $years);
    }

    public function testTimeSince()
    {
        $datetime2 = date('Y-m-d H:i:s', strtotime('-1day'));
        $datetime3 = date('Y-m-d H:i:s', strtotime('-2 month 2 days'));
        $datetimes = array(
            $this->datetime => $this->o,
            $datetime2 => new DateTime($datetime2),
            $datetime3 => new DateTime($datetime3),
        );

        foreach ($datetimes as $datetime => $datetimeObj) {
            $now = new \DateTime($datetime);
            $int = $now->diff(new \DateTime());

            $map = array(
                'y' => 'year',
                'm' => 'month',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );
            $timeSince = array();

            foreach ($map as $k => $v) {
                if ($int->$k) {
                    $timeSince[$k] = $int->$k . ' ' . $v . ($int->$k == 1 ? '' : 's');
                }
            }

            $this->assertEquals(implode(', ', $timeSince), $datetimeObj->timeSince('now', true));
            unset($timeSince['s']);
            $this->assertEquals(implode(', ', $timeSince), $datetimeObj->timeSince());
        }
    }

    public function testToDate()
    {
        $this->assertEquals(date('Y-m-d', strtotime($this->datetime)), $this->o->toDate());
        $format = 'Y-m-dTH:i:sP';
        $this->assertEquals(date($format, strtotime($this->datetime)), $this->o->toDate($format));
    }

    public function testToISO8601()
    {
        $this->assertEquals(date('c', strtotime($this->datetime)), $this->o->toISO8601());
        $o = new DateTime('2012-05-02T00:00:00+00:00');
        $this->assertEquals('2012-05-02T00:00:00Z', $o->toISO8601());
    }

    public function testGetGlobalFormat()
    {
        $this->assertEquals('F j, Y g:i A', DateTime::getGlobalFormat());
    }

    public function testSetGlobalFormat()
    {
        DateTime::setGlobalFormat('F j, Y');
        $this->assertEquals('F j, Y', DateTime::getGlobalFormat());
    }
}
