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
                $return = mcrypt_encrypt(MCRYPT_XTEA,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv1()
                                        );
                break;

            case self::LEVEL2:
                $return = mcrypt_encrypt(MCRYPT_SERPENT,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv2()
                                        );
                break;

            case self::LEVEL3:
                $return = mcrypt_encrypt(MCRYPT_SAFERPLUS,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv3()
                                        );
                break;

            case self::LEVEL4:
                $return = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv4()
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
                $return = mcrypt_decrypt(MCRYPT_XTEA,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv1()
                                        );
                break;

            case self::LEVEL2:
                $return = mcrypt_decrypt(MCRYPT_SERPENT,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv2()
                                        );
                break;

            case self::LEVEL3:
                $return = mcrypt_decrypt(MCRYPT_SAFERPLUS,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv3()
                                        );
                break;

            case self::LEVEL4:
                $return = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,
                                        $key,
                                        $str,
                                        MCRYPT_MODE_ECB,
                                        self::iv4()
                                        );
                break;
        }
        return trim($return);
    }

    private static function iv1()
    {
        return mcrypt_create_iv(
                    mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB),
                    MCRYPT_RAND);
    }

    private static function iv2()
    {
        return mcrypt_create_iv(
                    mcrypt_get_iv_size(MCRYPT_SERPENT, MCRYPT_MODE_ECB),
                    MCRYPT_RAND);
    }

    private static function iv3()
    {
        return mcrypt_create_iv(
                    mcrypt_get_iv_size(MCRYPT_SAFERPLUS, MCRYPT_MODE_ECB),
                    MCRYPT_RAND);
    }

    private static function iv4()
    {
        return mcrypt_create_iv(
                    mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB),
                    MCRYPT_RAND);
    }
}
