<?php
namespace NObjects;

/**
 * Date utility helper.
 *
 * @author Nesbert Hidalgo
 */
class Date
{
    // time in seconds
    const MINUTE = 60;
    const HOUR   = 3600;
    const DAY    = 86400;
    const WEEK   = 604800;

    /**
     * Returns a string YYYY-MM-DD HH:MM:SS. Uses current time if no time pasted.
     *
     * @static
     * @param string/timestamp $time
     * @return string
     */
    public static function datetime($time = 'now')
    {
        switch (true) {
            case empty($time):
            case is_int($time) && $time <= 0:
            default:
                return date('Y-m-d H:i:s');

            case is_array($time):
                $time['hour'] = isset($time['hour']) ? $time['hour'] : 0;
                $time['minute'] = isset($time['minute']) ? $time['minute'] : 0;
                $time['second'] = isset($time['second']) ? $time['second'] : 0;
                if (!empty($time['ampm'])
                    && strtoupper($time['ampm']) == 'PM'
                    && $time['hour'] < 12) {
                    $time['hour'] += 12;
                }
                return date('Y-m-d H:i:s', mktime(
                    $time['hour'],
                    $time['minute'],
                    $time['second'],
                    $time['month'],
                    $time['day'],
                    $time['year']
                ));
                break;

            case Validate::isNumber($time):
                return date('Y-m-d H:i:s', $time);

            case is_string($time) && $time != '0000-00-00 00:00:00':
                return date('Y-m-d H:i:s', strtotime($time));
        }
    }

    /**
     * Returns the current time measured in the number of seconds since the
     * Unix Epoch (January 1 1970 00:00:00 GMT) in GMT
     *
     * @return integer
     * @author John Faircloth
     **/
    public static function gmtimestamp()
    {
       return strtotime(gmdate('Y-m-d H:i:s'));
    }

    /**
     * MySQL Timestamp of from current time in GMT.
     *
     * @param mixed $datetime Accepts either an array, unix timestamp or string.
     * @see datetime
     * @return string datetime stamp
     **/
    public static function gmdatetime($datetime = null)
    {
        return gmdate('Y-m-d H:i:s', ($datetime
            ? strtotime(self::datetime($datetime))
            : time()
        ));
    }

    /**
     * Returns time passed. Latest activity about "8 hours" ago. Anything
     * over 4 weeks returns false.
     *
     * @param mixed $time Accepts unix timestamp or datetime string.
     * @return string
     **/
    public static function timeSince($time)
    {
        if (empty($time)) return false;

        $time = time() - (is_string($time) ? strtotime($time) : $time);

        switch (true) {
            case $time <= 0:
                return false;

            case ($time < self::MINUTE):
                $time = round(((($time % self::WEEK) % self::DAY) % self::HOUR) % self::MINUTE);
                $return = "{$time} second";
                break;

            case ($time < self::HOUR):
                $time = round(((($time % self::WEEK) % self::DAY) % self::HOUR) / self::MINUTE);
                $return = "{$time} minute";
                break;

            case ($time < self::DAY):
                $time = round((($time % self::WEEK) % self::DAY) / self::HOUR);
                $return = "{$time} hour";
                break;

            case ($time < self::WEEK):
                $time = round(($time % self::WEEK) / self::DAY);
                $return = "{$time} day";
                break;

            case ($time < self::WEEK * 4):
                $time = round($time / self::WEEK);
                $return = "{$time} week";
                break;

            default:
                return false;
                break;
        }

        return $return . ($time == 1 ? '' : 's');
    }

    /**
     * Get an array of dates with key as date (Y-m-d) and value as day (D).
     *
     * @param mixed $start
     * @param mixed $end
     * @param string $keyDateFormat
     * @param string $valueDateFormat
     * @param mixed $end
     * @return array
     */
    public static function range($start, $end = '', $keyDateFormat = 'Y-m-d', $valueDateFormat = 'D')
    {
        $start = strtotime(self::datetime($start));
        $end = strtotime(self::datetime($end));
        $end = mktime(0, 0, 0, date('m', $end), date('d', $end), date('Y', $end));
        $range = array();

        if ($end >= $start) {
            $range[date($keyDateFormat, $start)] = date($valueDateFormat, $start);
            $next_day = $start;
            while ($next_day < $end) {
                $next_day_time = strtotime(date('Y-m-d', $next_day) . ' +1day'); // add a day
                $range[date($keyDateFormat, $next_day_time)] = date($valueDateFormat, $next_day_time);
                $next_day += self::DAY; // add a day
            }
        }

        return $range;
    }
}
