<?php
namespace NObjects;

/**
 * Network utility helper.
 *
 * @author Nesbert Hidalgo
 */
class Network
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
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the current server host's URL.
     *
     * @return string
     **/
    public static function httpHost()
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
        return self::httpHost() . @$_SERVER['REQUEST_URI'];
    }

    /**
     * Returns the current server domain.
     *
     * @return string
     **/
    public static function domain()
    {
        $host = explode('.', self::host());
        $tld = explode(':', $host[count($host) - 1]);
        if (count($host)>1) {
            return $host[count($host) - 2] . '.' . $tld[0];
        } else {
            return $host[0];
        }
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
        if (is_numeric($ip)) {
            return long2ip($ip);
        }
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
        return (bool)filter_var($ip, FILTER_VALIDATE_IP);
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

    /**
     * Simple curl REST client.
     *
     * @param string $url
     * @param string $method GET, POST, PUT, DELETE
     * @param string $data
     * @param array $options
     * @param string $error
     * @return bool|\stdClass
     */
    public static function curlRequest($url, $method = 'GET', $data = '', array $options = array(), &$error = '')
    {
        // validate methods
        if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'PATCH'))) {
            return false;
        }

        $includeHeader = false;
        if (isset($options['includeHeader'])) {
            $includeHeader = (bool)$options['includeHeader'];
            unset($options['includeHeader']);
        }

        $maxRedirects = 10;
        if (isset($options['maxRedirects'])) {
            $maxRedirects = (int)$options['maxRedirects'];
            unset($options['maxRedirects']);
        }

        $out = new \stdClass();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if ($includeHeader) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        if ($maxRedirects) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, (int)$maxRedirects);
        }
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

        if (!$result = curl_exec($ch)) {
            $error = curl_error($ch);
            return false;
        }
        $out->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $out->contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $out->body = $result;
        curl_close($ch);
        return $out;
    }

    /**
     * @static
     * @return bool
     */
    public static function isRemoteIpLocal()
    {
        return in_array(self::remoteIp(), array('127.0.0.1', '::1'));
    }

    /**
     * Force and redirect to https of current URL. Sets header location
     * from http://... to https://... for current url.
     *
     * @return bool
     */
    public static function forceHTTPS()
    {
        if (!self::isSsl()) {
            header('location: https://' . self::host() . @$_SERVER['REQUEST_URI']);
            return true;
        } else {
            return false;
        }
    }
}
