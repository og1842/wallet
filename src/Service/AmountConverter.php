<?php declare(strict_types=1);

namespace App\Service;

class AmountConverter
{
    /**
     * Convert string to amount to store in DB
     *
     * @param string $amount
     *
     * @return int
     *
     * @example '19.990' -> 1999, '5.1' -> 510, '.1' -> 10
     */
    public static function convertToDbValue(string $amount): int
    {
        $arr = explode('.', $amount);
        $amount = $arr[0];

        if (isset($arr[1])) { // decimal
            $decimal = substr($arr[1], 0, 2);
        } else {
            $decimal = '';
        }

        $decimalLength = strlen($decimal);

        if ($decimalLength < 2) {
            $decimal = str_pad($decimal, 2, '0', STR_PAD_RIGHT); // fill with zeros
        }

        $amount .= $decimal;
        $converted = (int)$amount;

        if ($converted < 0) {
            return 0;
        }

        if ($converted > 9999999900) {
            return 0;
        }

        return $converted;
    }

    /**
     * Convert from DB to show in template
     *
     * @param int $amount
     *
     * @return float
     */
    public static function convertFromDbValue(int $amount): float
    {
        return round($amount / 100, 2);
    }
}