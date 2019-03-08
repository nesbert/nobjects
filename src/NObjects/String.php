<?php

namespace NObjects;

/**
 * String utility helper.
 *
 * @deprecated
 */
class String
{
    /**
     * Retrieve a number from a string.
     *
     * @param string $str
     *
     * @return float
     *
     * @deprecated
     */
    public static function retrieveNumber($str)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::retrieveNumber instead.', E_USER_DEPRECATED);

        return StringUtil::retrieveNumber($str);
    }

    /**
     * Repeat a string N ($count) amount of times.
     *
     * @param string $string
     * @param int $count
     *
     * @return string
     *
     * @deprecated
     */
    public static function times($string, $count)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::retrieveNumber instead.', E_USER_DEPRECATED);

        return StringUtil::times($string, $count);
    }

    /**
     * Checks the occurrence of $needle in $haystack.
     *
     * @param string $needle
     * @param string $haystack
     *
     * @return boolean
     *
     * @deprecated
     */
    public static function contains($needle, $haystack)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::contains instead.', E_USER_DEPRECATED);

        return StringUtil::contains($needle, $haystack);
    }

    /**
     * Checks if the string starts with $needle.
     *
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     *
     * @deprecated
     */
    public static function startsWith($needle, $haystack)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::startsWith instead.', E_USER_DEPRECATED);

        return StringUtil::startsWith($needle, $haystack);
    }

    /**
     * Checks if the string ends with $needle.
     *
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     *
     * @deprecated
     */
    public static function endsWith($needle, $haystack)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::endsWith instead.', E_USER_DEPRECATED);

        return StringUtil::endsWith($needle, $haystack);
    }

    /**
     * Splits the string character-by-character and returns an array with
     * the result.
     *
     * @param string $string
     *
     * @return array
     *
     * @deprecated
     */
    public static function toArray($string)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::toArray instead.', E_USER_DEPRECATED);

        return StringUtil::toArray($string);
    }

    /**
     * String replaces a string using array keys with array values.
     *
     * @param string $string
     * @param array $array
     *
     * @return string
     *
     * @deprecated
     *
     * @author Nesbert Hidalgo
     */
    public static function replaceByArray($string, array $array)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::replaceByArray instead.', E_USER_DEPRECATED);

        return StringUtil::replaceByArray($string, $array);
    }

    /**
     * Returns the string with every occurrence of a given pattern replaced by
     * either a regular string, the returned value of a function or a Template
     * string. The pattern can be a string or a regular expression.
     *
     * @param string $string
     * @param string $pattern
     * @param string $replace
     * @param int|null $count
     *
     * @return string
     *
     * @deprecated
     */
    public static function gsub($string, $pattern, $replace, &$count = null)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::gsub instead.', E_USER_DEPRECATED);

        return StringUtil::gsub($string, $pattern, $replace, $count);
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
     *
     * @return string
     *
     * @deprecated
     */
    public static function sub($string, $pattern, $replace, $times = 1, &$count = null)
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::sub instead.', E_USER_DEPRECATED);

        return StringUtil::sub($string, $pattern, $replace, $times, $count);
    }

    /**
     * Build a link href mailto string.
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $cc
     * @param string $bcc
     *
     * @return string
     *
     * @deprecated
     */
    public static function mailTo($to, $subject = '', $body = '', $cc = '', $bcc = '')
    {
        @trigger_error('NObjects\String is deprecated. Migrate to using NObjects\StringUtil::mailTo instead.', E_USER_DEPRECATED);

        return StringUtil::mailTo($to, $subject, $body, $cc, $bcc);
    }
}