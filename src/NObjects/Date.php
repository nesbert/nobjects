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
     * Returns a string YYYY-MM-DD HH:MM:SS. Uses current time if no $datetime.
     *
     * @static
     * @param string/timestamp $time
     * @return string
     */
    public static function datetime($time = 'now')
    {
        if (is_string($time) && $time != '0000-00-00 00:00:00') {
            $time = strtotime($time);
        } elseif (Validate::isNumber($time)) {
            // do nothing
        } elseif (is_array($time)) {
            $time['hour'] = isset($time['hour']) ? $time['hour'] : 0;
            $time['minute'] = isset($time['minute']) ? $time['minute'] : 0;
            $time['second'] = isset($time['second']) ? $time['second'] : 0;
            if (!empty($time['ampm'])
                && strtoupper($time['ampm']) == 'PM'
                && $time['hour'] < 12) {
                $time['hour'] += 12;
            }
            $time = mktime(
                $time['hour'],
                $time['minute'],
                $time['second'],
                $time['month'],
                $time['day'],
                $time['year']
            );
        } else {
            $time = time();
        }

        return date('Y-m-d H:i:s', $time);
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
     * @return string
     **/
    public static function gmdatetime($datetime = null)
    {
        return gmdate('Y-m-d H:i:s', ($datetime
            ? strtotime(self::datetime($datetime))
            : time()
        ));
    }

    /**
     * Return an ISO 8601 datetime string (2012-02-01T15:55:23-07:00) of the
     * current timezone. Uses current time if no $datetime.
     *
     * @static
     * @param null $datetime
     * @return string
     */
    public static function datetimeISO8601($datetime = null)
    {
        $out = date('c', $datetime ? strtotime(self::datetime($datetime)) : time());
        return str_replace('+00:00', 'Z', $out);
    }

    /**
     * Returns ISO 8601 datetime string (2012-02-01T15:55:23Z) of the current
     * time in GMT. Uses current time if no $datetime.
     *
     * @static
     * @param null $datetime
     * @return string
     */
    public static function gmdatetimeISO8601($datetime = null)
    {
        $out = gmdate('c', $datetime ? strtotime(self::datetime($datetime)) : time());
        return str_replace('+00:00', 'Z', $out);
    }

    /**
     * Reformat $datetime to ISO 8601 format to a specific timezone. Uses
     * current time if no $datetime.
     *
     * @static
     * @param null $datetime
     * @param string $timezone
     * @return string
     */
    public static function toISO8601($datetime = null, $timezone = 'GMT')
    {
        $default = date_default_timezone_get();
        date_default_timezone_set($timezone);
        $out = date('c', $datetime ? strtotime(self::datetime($datetime)) : time());
        date_default_timezone_set($default);
        return str_replace('+00:00', 'Z', $out);
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
        if (empty($time)) {
            return false;
        }

        $time = time() - (is_string($time) ? strtotime($time) : $time);

        if ($time <= 0) {
            return false;
        }

        $return = '';

        switch (true) {
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

            default:
                $time = round($time / self::WEEK);
                $return = "{$time} week";
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

    /**
     * Get current time in milliseconds.
     *
     * @static
     * @return float
     */
    public static function milliseconds()
    {
        // TODO string to milliseconds
        return round(microtime(true) * 1000);
    }
}
