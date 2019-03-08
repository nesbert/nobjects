<?php

namespace NObjects;

/**
 * A wrapper class to mcrypt for encryption/decryption which supports a wide
 * variety of block algorithms.
 *
 * @author Nesbert Hidalgo
 **/
class Cipher
{
    const LEVEL1 = 1;
    const LEVEL2 = 2;
    const LEVEL3 = 3;
    const LEVEL4 = 4;

    /**
     * Encrypts plaintext with given parameters. <b>Make sure to use the same
     * key for encrypt & decrypt and also same encryption level<b>.
     *
     * <code>
     * function encrypt($str) {
     *     return Cipher::encrypt($str, 2, KEY_STRING);
     * }
     * </code>
     *
     * @param string $str
     * @param integer $level Encryption level, 4 being the strongest/slower.
     * @param string $key String key used to lock/unlock encryption.
     * @return string
     **/
    public static function encrypt($str, $level = self::LEVEL1, $key = 'please change')
    {
        switch ($level) {
            case self::LEVEL1:
            default:
                @trigger_error('Encryption level 1 is deprecated. Migrate your crypted strings to another algorithm.', E_USER_DEPRECATED);
                $return = @mcrypt_encrypt(
                    MCRYPT_XTEA,
                    $key,
                    $str,
                    MCRYPT_MODE_ECB,
                    self::iv1()
                );
                break;

            case self::LEVEL2:
                @trigger_error('Encryption level 2 is deprecated. Migrate your crypted strings to another algorithm.', E_USER_DEPRECATED);
                $return = @mcrypt_encrypt(
                    MCRYPT_SERPENT,
                    $key,
                    $str,
                    MCRYPT_MODE_ECB,
                    self::iv2()
                );
                break;

            case self::LEVEL3:
                @trigger_error('Encryption level 3 is deprecated. Migrate your crypted strings to another algorithm.', E_USER_DEPRECATED);
                $return = @mcrypt_encrypt(
                    MCRYPT_SAFERPLUS,
                    $key,
                    $str,
                    MCRYPT_MODE_ECB,
                    self::iv3()
                );
                break;

            case self::LEVEL4:
                $return = openssl_encrypt(
                    $str,
                    'aes-256-ecb',
                    $key,
                    2 xor 1 // OPENSSL_ZERO_PADDING xor OPENSSL_RAW_DATA
//                    self::iv4(), ECB cipher does not use IV input
                );
                break;
        }
        return base64_encode($return);
    }

    /**
     * Decrypts crypttext with given parameters. * Make sure to use the same
     * key for encrypt & decrypt and also same encryption level.
     *
     * <code>
     * function decrypt($str) {
     *     return cipher::decrypt($str, 2, KEY_STRING);
     * }
     * </code>
     *
     * @param string $str
     * @param integer $level Encryption level, 4 being the strongest/slower.
     * @param string $key String key used to lock/unlock encryption.
     * @return string
     **/
    public static function decrypt($str, $level = self::LEVEL1, $key = 'please change')
    {
        $str = base64_decode($str);
        switch ($level) {
            case self::LEVEL1:
            default:
                @trigger_error('Encryption level 1 is deprecated. Migrate your crypted strings to another algorithm.', E_USER_DEPRECATED);
                $return = @mcrypt_decrypt(
                    MCRYPT_XTEA,
                    $key,
                    $str,
                    MCRYPT_MODE_ECB,
                    self::iv1()
                );
                break;

            case self::LEVEL2:
                @trigger_error('Encryption level 2 is deprecated. Migrate your crypted strings to another algorithm.', E_USER_DEPRECATED);
                $return = @mcrypt_decrypt(
                    MCRYPT_SERPENT,
                    $key,
                    $str,
                    MCRYPT_MODE_ECB,
                    self::iv2()
                );
                break;

            case self::LEVEL3:
                @trigger_error('Encryption level 3 is deprecated. Migrate your crypted strings to another algorithm.', E_USER_DEPRECATED);
                $return = @mcrypt_decrypt(
                    MCRYPT_SAFERPLUS,
                    $key,
                    $str,
                    MCRYPT_MODE_ECB,
                    self::iv3()
                );
                break;

            case self::LEVEL4:
                $return = openssl_decrypt(
                    $str,
                    'aes-256-ecb',
                    $key,
                    2 xor 1 // OPENSSL_ZERO_PADDING xor OPENSSL_RAW_DATA
//                    self::iv4(), ECB cipher does not use IV input
                );
                break;
        }
        return trim($return);
    }

    private static function iv1()
    {
        return @mcrypt_create_iv(
            @mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB),
            MCRYPT_RAND
        );
    }

    private static function iv2()
    {
        return @mcrypt_create_iv(
            @mcrypt_get_iv_size(MCRYPT_SERPENT, MCRYPT_MODE_ECB),
            MCRYPT_RAND
        );
    }

    private static function iv3()
    {
        return @mcrypt_create_iv(
            @mcrypt_get_iv_size(MCRYPT_SAFERPLUS, MCRYPT_MODE_ECB),
            MCRYPT_RAND
        );
    }

    /**
     * MD5 strings, objects and arrays.
     *
     * @deprecated
     */
    public static function md5Data($data)
    {
        @trigger_error('Method NObjects\Cipher::md5Data is deprecated. Migrate to using NObjects\Hash::md5 instead.', E_USER_DEPRECATED);

        return Hash::md5($data);
    }
}
