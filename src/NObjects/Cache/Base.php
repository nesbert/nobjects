<?php
namespace NObjects\Cache;

abstract class Base
{
    private $keyGlue = '.';
    private $keySpecial = array(' ', '/', '\\');
    private $keySpecialGlue = '_';

    /**
     * Helper to build a key string. Takes any number of parameters and will
     * build a string gluing them together by $glue. Also, special characters
     * will be replaced with underscores.
     *
     * @return string
     */
    public function buildKey()
    {
        // get args
        $args = func_get_args();
        // remove empty vars
        foreach ($args as $k => $v) {
            if (empty($v)) {
                unset($args[$k]);
            }
        }

        return urldecode(
            str_replace(
                $this->getKeySpecial(),
                $this->getKeySpecialGlue(),
                implode($this->getKeyGlue(), $args)
            )
        );
    }

    /**
     * Convert string keywords to the there timestamp equivalent.
     *
     * For example:
     *
     *      "midnite", "tomorrow",
     *      "1 min", "2 minutes", "3mins", "1hr", "2 hours", "3 hrs",
     *      "1day", "2 days", "3 day", "1wk", "2 weeks", "3 wks"
     *
     * If no keywords are found runs the string through the strtotime method.
     *
     * @param string $string
     * @param bool $seconds
     * @return int
     */
    public function stringToTime($string, $seconds = false)
    {
        if (is_int($string)) {
            return $string;
        }

        // get first integer from string
        $n = (int) preg_replace('/[^0-9.-]/', '', $string);

        switch (true) {
            case $string == 'midnite':
                $time = strtotime(date('Y-m-d 23:59:59'));
                break;

            case $string == 'tomorrow':
                $time = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));
                break;

            // 1 min, 2 minutes, 3mins
            case preg_match('/min/i', $string):
                $time = strtotime('+'.$n.' minutes');
                break;

            // 1hr, 2 hours, 3 hrs
            case preg_match('/hour|hr/i', $string):
                $time = strtotime('+'.$n.' hours');
                break;

            // 1day, 2 days, 3 day
            case preg_match('/day|dy/i', $string):
                $time = strtotime('+'.$n.' days');
                break;

            // 1wk, 2 weeks, 3 wks
            case preg_match('/week|wk/i', $string):
                $time = strtotime('+'.$n.' weeks');
                break;

            default:
                $time = strtotime($string);
                break;
        }

        return $seconds ? $time - time() : $time;
    }

    public function setKeyGlue($keyGlue)
    {
        $this->keyGlue = $keyGlue;
        return $this;
    }

    public function getKeyGlue()
    {
        return $this->keyGlue;
    }

    public function setKeySpecial($keySpecial)
    {
        $this->keySpecial = $keySpecial;
        return $this;
    }

    public function getKeySpecial()
    {
        return $this->keySpecial;
    }

    public function setKeySpecialGlue($keySpecialGlue)
    {
        $this->keySpecialGlue = $keySpecialGlue;
        return $this;
    }

    public function getKeySpecialGlue()
    {
        return $this->keySpecialGlue;
    }
}
