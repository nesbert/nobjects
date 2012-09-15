<?php
/**
 * Network utility helper.
 *
 * @author Nesbert Hidalgo
 */
class NNetwork
{
    /**
     * Return the http: or https: depending on environment.
     *
     * @return string
     **/
    public static function http()
    {
        return 'http'.(self::isSsl() ? 's' : '').'://';
    }

    /**
     * Returns the current server host.
     *
     * @return string
     **/
    public static function host()
    {
        return @$_SERVER['HTTP_HOST'];
    }

    /**
     * Returns the current server host's URL.
     *
     * @return string
     **/
    public static function http_host()
    {
        return self::http() . self::host();
    }

    /**
     * Returns the current server host's URL.
     *
     * @return string
     **/
    public static function url()
    {
        return self::http_host() . @$_SERVER['REQUEST_URI'];
    }

    /**
     * Returns the current server domain.
     *
     * @return string
     **/
    public static function domain()
    {
        $url = explode('.', self::host());
        $tld = explode(':', $url[count($url) - 1]);
        return $url[count($url) - 2] . '.' . $tld[0];
    }

    /**
     * A top-level domain (TLD), sometimes referred to as a top-level
     * domain name (TLDN), is the last part of an Internet domain
     * name; that is, the letters that follow the final dot of any domain
     * name. For example, in the domain name www.example.com, the
     * top-level domain is "com".
     *
     * @return string
     **/
    public static function tld()
    {
        $url = explode('.', self::domain());
        return isset($url[1]) ? $url[1] : '';
    }

    /**
     * Converts a string IP to and integer and vice versa. If no $ip is
     * passed will convert current remoteIp() to an integer.
     *
     * @param null $ip
     * @return mixed
     */
    public static function intIp($ip = null)
    {
        if (is_numeric($ip)) return long2ip($ip);
        return sprintf("%u", ip2long($ip ? $ip : self::remoteIp()));
    }

    /**
     * Checks if application is using SSL via ENV variable HTTPS.
     *
     * @return boolean
     **/
    public static function isSsl()
    {
        return getenv('HTTPS') == 'on';
    }

    /**
     * Checks if application is using valid IP and also converts a
     * non-complete IP into a proper dotted quad.
     *
     * @param $ip
     * @return boolean
     */
    public static function isIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Get remote clients IP address.
     *
     * @static
     * @param bool $iplong
     * @return string/int
     */
    public static function remoteIp($iplong = false)
    {
        // if it is a shared client
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        // if a proxy address
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $iplong ? ip2long($ip) : $ip;
    }
}
