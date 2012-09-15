<?php
/**
 * Utility DateTime helper.
 * 
 * @author Nesbert Hidalgo
 */
class NDateTime extends DateTime
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
     * @param DateTimeZone|null $timezone
     * @return NDateTime
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null)
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
     * @return DateInterval
     * @link http://www.php.net/manual/en/class.datetime.php#95830
     */
    public function diff($now = 'now', $absolute = false) {
        if(!($now instanceOf NDateTime)
            || !($now instanceOf DateTime)) {
            $now = new NDateTime($now);
        }
        return parent::diff($now, $absolute);
    }
    
    /**
     * Return Age in Years.
     *
     * @param Datetime|String $now
     * @return Integer
     * @link http://www.php.net/manual/en/class.datetime.php#95830
     */
    public function getAge($now = 'now') {
        return $this->diff($now)->format('%y');
    }
    
    /**
     * Get a formatted strong of time since passed $time.
     * 
     * @param string $time
     * @param bool $showSeconds
     * @return string
     */
    public function timeSince($time = 'now', $showSeconds = false)
    {
        $format = '%y year, %m month, %d day, %h hour, %i minute, %s second';
        $formats = array(
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        
        $diff = $this->diff($time);
        
        if (!$showSeconds) {
            $format = str_replace(', %s second', '', $format);
        }
        
        foreach ($formats as $k => $v) {
            if ($diff->{$k} != 1) {
                if ($diff->{$k} == 0) {
                    $format = str_replace(array("%{$k} {$v},", "%{$k} {$v}") , '', $format);
                } else {
                    $format = str_replace($v, "{$v}s", $format);
                }
            }
        }
        
        return trim($diff->format($format));        
    }
    
    /**
     * Return a date sting in the date format of YYYY-MM-DD.
     * 
     * @param string $format
     * @return string
     */
    public function toDate($format = 'Y-m-d')
    {
        return $this->format($format);
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
