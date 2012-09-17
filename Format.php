<?php
/**
 * Utility format helper.
 *
 * @author Nesbert Hidalgo
 */
class NFormat
{
    private static $numberOfDecimals;
    private static $numberOfDecimalsPercent;

    /**
     * Get a percent from number and total.
     *
     * @static
     * @param int $number
     * @param int $total
     * @param int $decimals
     * @return string
     */
    public static function toPercent($number, $total, $decimals = 1)
    {
        $number = (int) $number;
        $total = (int) $total;
        $percent = $total > 0 ? $number/$total*100 : 0;
        if (!is_null(self::getNumberOfDecimals())) {
            $decimals = self::getNumberOfDecimals();
        }

        return number_format($percent, (int)$decimals, '.', ',') . '%';
    }

    /**
     * Get a formatted number.
     *
     * @static
     * @param int $number
     * @param int $decimals
     * @return string
     */
    public static function toNumber($number, $decimals = 0)
    {
        $number = (int) $number;
        $decimalPoint = '.';
        $decimalSeparator = ',';

        if (!is_null(self::getNumberOfDecimals())) {
            $decimals = self::getNumberOfDecimals();
        }

        return number_format($number, (int)$decimals, $decimalPoint, $decimalSeparator);
    }

    // getters & setters

    public static function setNumberOfDecimals($numberOfDecimals)
    {
        self::$numberOfDecimals = (int)$numberOfDecimals;
    }

    public static function getNumberOfDecimals()
    {
        return self::$numberOfDecimals;
    }

    public static function setNumberOfDecimalsPercent($numberOfDecimalsPercent)
    {
        self::$numberOfDecimalsPercent = (int)$numberOfDecimalsPercent;
    }

    public static function getNumberOfDecimalsPercent()
    {
        return self::$numberOfDecimalsPercent;
    }
}
