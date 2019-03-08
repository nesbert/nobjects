<?php

namespace NObjects;

/**
 * Hash supports flexible hashing of data structures.
 */
class Hash
{
    /**
     * MD5 strings, objects and arrays.
     *
     * @param mixed $data
     * @return string
     * @link http://stackoverflow.com/a/7723730
     */
    public static function md5($data)
    {
        if (is_array($data)) {
            array_multisort($data);
            return md5(json_encode($data));
        } elseif (is_object($data)) {
            return md5(serialize($data));
        } else {
            return md5($data);
        }
    }
}