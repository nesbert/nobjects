<?php
namespace NObjects;

/**
 * Utility DateTime helper.
 *
 * @author Nesbert Hidalgo
 */
class DateTime extends \DateTime
{
    /**
     * Global date format string.
     *
     * @see date()
     * @var string
     */
    private static $globalFormat = 'F j, Y g:i A';

    /**
     * Similar to DateTime construct but accepts unix timestamp as well.
     *
     * @param string $time
     * @param \DateTimeZone $timezone
     * @return \NObjects\DateTime
     */
    public function __construct($time = 'now', \DateTimeZone $timezone = null)
    {
        if (is_numeric($time)) {
            $time = date('Y-m-d H:i:s', $time);
        } elseif (is_string($time)
                  && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\./', $time)) {
            // play nice with DB2 timestamp
            $time = substr($time, 0, 19);
            $time = date('Y-m-d H:i:s', strtotime($time));
        } elseif ($time == '0000-00-00 00:00:00' || $time === false) {
            return null;
        }
        
        parent::__construct($time, $timezone);
    }
    
    /**
     * Print object as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getDateTime();
    }
    
    /**
     * Get unix timestamp.
     *
     * @return integer
     */
    public function getTimestamp()
    {
        return (int) $this->format('U');
    }
    
    /**
     * Get date string (YYYY-MM-DD).
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->format('Y-m-d');
    }
    
    /**
     * Get time string (HH:MM:SS).
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->format('H:i:s');
    }
    
    /**
     * Get date & time string (YYYY-MM-DD HH:MM:SS).
     *
     * @return integer
     */
    public function getDateTime()
    {
        return $this->getDate() . ' ' . $this->getTime();
    }
    
    /**
     * Get a datetime string using the global format.
     *
     * @return string
     */
    public function getGlobal()
    {
        return $this->format(self::getGlobalFormat());
    }

    /**
     * Return difference between $this and $now.
     *
     * @param Datetime|String $now
     * @param bool $absolute
     * @return \DateInterval
     * @link http://www.php.net/manual/en/class.datetime.php#95830
     */
    public function diff($now = 'now', $absolute = false)
    {
        if (!($now instanceof \DateTime)
            || !($now instanceof DateTime)) {
            $now = new DateTime($now);
        }
        return parent::diff($now, $absolute);
    }
    
    /**
     * Return Age in Years.
     *
     * @param \Datetime|Datetime|String $now
     * @return Integer
     * @link http://www.php.net/manual/en/class.datetime.php#95830
     */
    public function getAge($now = 'now')
    {
        return $this->diff($now)->format('%y');
    }
    
    /**
     * Get a formatted string of time since passed $time.
     *
     * @param string $time
     * @param bool $showSeconds
     * @return string
     */
    public function timeSince($time = 'now', $showSeconds = false)
    {
        $interval = $this->diff($time);

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
            if ($interval->$k) {
                $timeSince[$k] = $interval->$k . ' ' . $v;
                $timeSince[$k] .= $interval->$k == 1 ? '' : 's';
            }
        }

        if (!$showSeconds) {
            unset($timeSince['s']);
        }
        
        return trim(implode(', ', $timeSince));
    }
    
    /**
     * Return a date string in the date format of YYYY-MM-DD.
     *
     * @param string $format
     * @return string
     */
    public function toDate($format = 'Y-m-d')
    {
        return $this->format($format);
    }

    /**
     * Return a date string in ISO8601 format.
     *
     * @return string
     */
    public function toISO8601()
    {
        return str_replace('+00:00', 'Z', $this->format('c'));
    }

    // getters & setters

    public static function getGlobalFormat()
    {
        return self::$globalFormat;
    }

    public static function setGlobalFormat($globalFormat)
    {
        self::$globalFormat = (string) $globalFormat;
    }
}
