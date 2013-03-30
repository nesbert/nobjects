<?php
namespace NObjects;

/**
 * Utility validation helper.
 *
 * @author Nesbert Hidalgo
 */
class Validate
{
    /**
     * Check if string is a valid email address.
     *
     * @param string $email
     * @param bool $checkDNS
     * @return bool
     */
    public static function isEmail($email, $checkDNS = false)
    {
        if ($checkDNS) {
            $domain = explode('@', $email);
            if (!checkdnsrr($domain[1] . '.', 'MX')) {
                return false;
            }
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) === false ? false : true;
    }

    /**
     * Check if string is a valid email address MD5.
     *
     * @param string $email
     * @return bool
     **/
    public static function isEmailMd5($email)
    {
        return is_string($email) && strlen($email) == 32;
    }

    /**
     * Check if string is a valid IP address.
     *
     * @param string $ip
     * @return bool
     *
     */
    public static function isIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP) === false ? false : true;
    }

    /**
     * Check if string is a valid URL address. Note Path is not required by default.
     *
     * @param string $url
     * @param bool $requirePath
     * @return bool
     */
    public static function isUrl($url, $requirePath = false)
    {
        if ($requirePath) {
            return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false ? false : true;
        }
        return filter_var($url, FILTER_VALIDATE_URL) === false ? false : true;
    }

    /**
     * Chech if string is a valid date string. 'YYYY-MM-DD', 'YYYY-MM-DD HH:II:SS', etc.
     *
     * @param string $string
     * @return bool
     **/
    public static function isDateString($string)
    {
        try {
            if (!is_string($string)) return false;

            $date = new \DateTime($string);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if string is a valid datetime 'YYYY-MM-DD HH:II:SS'.
     *
     * @param string $string
     * @return bool
     **/
    public static function isDatetimeString($string)
    {
        if (empty($string) || !is_string($string) || is_numeric($string)) {
            return false;
        }
        return preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $string) > 0;

    }
    
    /**
     * Checks if $var a variable only contains characters A-Z or a-z
     *
     * @param string $var Value to validate
     * @return bool
     **/
    public static function isAlpha($var)
    {
        if (!is_string($var)) return false;
        return preg_match('/^[a-z]+$/i', $var) ? true : false;
    }
    
    /**
     * Checks if $var only contains characters A-Z or a-z or 0-9.
     *
     * @param string $var Value to validate
     * @return bool
     **/
    public static function isAlphaNumeric($var)
    {
        if (!is_string($var) && !is_numeric($var)) return false;
        return preg_match('/^[a-zA-Z0-9]+$/', $var) ? true : false;
    }
    
    /**
     * Checks if $var is a number.
     *
     * @param string $var Value to validate
     * @return bool
     **/
    public static function isNumber($var)
    {
        if (!is_string($var) && !is_numeric($var)) return false;
        return preg_match('/^[0-9]+?[.]?[0-9]*$/', $var) ? true : false;
    }
    
    /**
     * Checks if $var is a positive number.
     *
     * @param string $var Value to validate
     * @return bool
     **/
    public static function isPositiveNumber($var)
    {
        return self::isNumber($var) && $var > 0;
    }
    
    /**
     * Checks if $var1 is equal to $var2.
     *
     * @param string $var1 Value to validate
     * @param string $var2 Value to validate against
     * @return bool
     **/
    public static function isMatch($var1, $var2)
    {
        return $var1 === $var2;
    }
    
    /**
     * Checks if $var is between $min and $max.
     *
     * @param string $var Value to validate
     * @param int $min Minimum number
     * @param int $max Maximum number
     * @return bool
     **/
    public static function isBetween($var, $min, $max)
    {
        return (is_numeric($min) && is_numeric($max))
            && ($var >= $min && $var <= $max);
    }
    
    /**
     * Checks if $var length equals $length.
     *
     * @param string/array $var Value to validate
     * @param int $length
     * @return bool
     **/
    public static function isLength($var, $length)
    {
        if (is_string($var)) {
           return count(str_split($var)) == $length;
        } elseif (is_array($var)) {
          return count($var) == $length;
        }
        return false;
    }
    
    /**
     * Checks if $var length is between $min and $max.
     *
     * @param string/array $var Value to validate
     * @param int $min Minimum length
     * @param int $max Maximum length
     * @return bool
     **/
    public static function isLengthBetween($var, $min, $max)
    {
        if (is_string($var)) {
            $length = strlen($var);
        } elseif (is_array($var)) {
            $length = count($var);
        } else {
            return false;
        }
        return ( $length >= $min ) && ( $length <= $max );
    }
    
    /**
     * Finds whether a $var is a regular expression.
     *
     * @param string $var Value to validate
     * @return bool
     **/
    public static function isRegex($var)
    {
        @preg_match($var, '', $test);
        return is_array($test);
    }
    
    /**
     * Finds whether a $var is an odd number.
     *
     * @param int $var Value to validate
     * @return bool
     **/
    public static function isEven($var)
    {
        if (!self::isNumber($var)) return false;
        return !self::isOdd($var);
    }
    
    /**
     * Finds whether a $var is an odd number.
     *
     * @param int $var Value to validate
     * @return bool
     **/
    public static function isOdd($var)
    {
        if (!self::isNumber($var)) return false;
        return ($var % 2) == 1;
    }
    
    /**
     * Check is request is using AJAX by checking headers.
     *
     * @return bool
     **/
    public static function isAjaxRequest()
    {
        return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ==
                    'xmlhttprequest')
                || @$_REQUEST['_AJAX_']);
    }

    /**
     * Check if an array is an associative array.
     *
     * @param $array
     * @link http://us3.php.net/manual/en/function.is-array.php#85324
     * @return bool
     */
    public static function isAssociativeArray($array)
    {
        if (is_array($array) == false) {
            return false;
        }
        
        foreach (array_keys($array) as $k => $v) {
            if ($k !== $v) return true;
        }
        
        return false;
    }
    
    /**
     * A faster/less memory substitute for strstr() or preg_match
     * used to check the occurrence of a subject in a string.
     *
     * @param string $needle
     * @param array $haystack
     * @return bool
     **/
    public static function inString($needle, $haystack)
    {
        return String::contains($needle, $haystack);
    }    
}
