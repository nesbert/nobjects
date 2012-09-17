<?php
namespace NObjects;

/**
 * String utility helper.
 *
 * @author Nesbert Hidalgo
 */
class String
{
    /**
     * Retrieve a number from a string.
     *
     * @param string $str
     * @return float
     **/
    public static function retrieveNumber($str)
    {
        return floatval(preg_replace('/[^0-9.-]/', '', $str));
    }

    /**
     * Repeat a string N ($count) amount of times.
     *
     * @param string $string
     * @param int $count
     * @return string
     */
    public static function times($string, $count)
    {
        return str_repeat($string, $count);
    }

    /**
     * Checks the occurrence of $needle in $haystack.
     *
     * @param string $needle
     * @param string $haystack
     * @return boolean
     **/
    public static function contains($needle, $haystack)
    {
        return !(strpos($haystack, (string) $needle) === false);
    }

    /**
     * Checks if the string starts with $needle.
     *
     * @param string $needle
     * @param string $haystack
     * @return bool
     **/
    public function startsWith($needle, $haystack)
    {
        return substr($haystack, 0, strlen($needle)) == $needle;
    }

    /**
     * Checks if the string ends with $needle.
     *
     * @param string $needle
     * @param string $haystack
     * @return bool
     **/
    public static function endsWith($needle, $haystack)
    {
        return substr($haystack, -strlen($needle)) == $needle;
    }

    /**
     * Splits the string character-by-character and returns an array with
     * the result.
     *
     * @param string $string
     * @return array
     **/
    public static function toArray($string)
    {
        return preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * String replaces a string using array keys with array values.
     *
     * @param string $string
     * @param array $array
     * @return string
     * @author Nesbert Hidalgo
     **/
    public static function replaceByArray($string, Array $array)
    {
        return str_replace(array_keys($array), array_values($array), $string);
    }

    /**
     * Returns the string with every occurrence of a given pattern replaced by
     * either a regular string, the returned value of a function or a Template
     * string. The pattern can be a string or a regular expression.
     *
     * @param string $string
     * @param string $pattern
     * @param string $replace
     * @param null $count
     * @return string
     */
    public static function gsub($string, $pattern, $replace, &$count = null)
    {
        return self::sub($string, $pattern, $replace, -1, $count);
    }

    /**
     * Returns a string with the first count occurrences of pattern
     * replaced by either a regular string, the returned value of a
     * function or a Template string. Pattern can be a string or a
     * regular expression.
     *
     * @param string $string
     * @param string $pattern
     * @param string $replace
     * @param int $times
     * @param null $count
     * @return string
     */
    public static function sub($string, $pattern, $replace, $times = 1, &$count = null)
    {
        return preg_replace(
                        (NValidate::isRegex($pattern)
                            ? $pattern
                            : "~{$pattern}~"),
                        $replace,
                        $string,
                        $times,
                        $count);
    }
}
