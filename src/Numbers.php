<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use froq\common\object\StaticClass;

/**
 * Numbers.
 *
 * @package froq\util
 * @object  froq\util\Numbers
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final class Numbers extends StaticClass
{
    /**
     * Convert.
     *
     * @param  numeric  $in
     * @param  int|null $decimals
     * @return int|float|null
     * @since  4.0
     */
    public static function convert($in, int $decimals = null): int|float|null
    {
        if (is_number($in)) {
            return $in;
        }

        if (is_numeric($in)) {
            if ($decimals !== null) {
                $in = number_format((float) $in, $decimals);
            }

            return is_string($in) && str_contains($in, '.') ? (float) $in : (int) $in;
        }

        return null; // Error, not a number.
    }

    /**
     * Compare.
     *
     * @param  numeric  $a
     * @param  numeric  $b
     * @param  int|null $precision
     * @return int|null
     */
    public static function compare($a, $b, int $precision = null): int|null
    {
        $precision ??= 14;

        if (is_numeric($a) && is_numeric($b)) {
            return round($a, $precision) <=> round($b, $precision);
        }

        return null; // Error, non-number(s).
    }

    /**
     * Equals.
     * @param  numeric  $a
     * @param  numeric  $b
     * @param  int|null $precision
     * @return bool|null
     */
    public static function equals($a, $b, int $precision = null): bool|null
    {
        $precision ??= 14;

        return ($ret = self::compare($a, $b, $precision)) === null ? null // Error, non-number(s).
             : ($ret === 0);
    }

    /**
     * Check whether given input is number.
     *
     * @param  any $in
     * @return bool
     */
    public static function isNumber($in): bool
    {
        return is_number($in);
    }

    /**
     * Check whether given input is digit.
     *
     * @param  any $in
     * @return bool
     */
    public static function isDigit($in): bool
    {
        return is_numeric($in) && (is_int($in) || ctype_digit((string) $in)) && ($in >= 0);
    }

    /**
     * Check whether given input is an ID (useful for any (db) incremental id check).
     *
     * @param  any $in
     * @return bool
     */
    public static function isId($in): bool
    {
        return is_numeric($in) && (is_int($in) || ctype_digit((string) $in)) && ($in >= 1);
    }

    /**
     * Check whether given input is uint.
     *
     * @param  any $in
     * @return bool
     */
    public static function isUInt($in): bool
    {
        return is_int($in) && ($in >= 0);
    }

    /**
     * Check whether given input is ufloat.
     *
     * @param  any $in
     * @return bool
     */
    public static function isUFloat($in): bool
    {
        return is_float($in) && ($in >= 0);
    }

    /**
     * Check whether given input is signed.
     *
     * @param  any $in
     * @return bool
     */
    public static function isSigned($in): bool
    {
        return is_number($in) && ($in < 0);
    }

    /**
     * Check whether given input is unsigned.
     * @param  any $in
     * @return bool
     */
    public static function isUnsigned($in): bool
    {
        return is_number($in) && ($in >= 0);
    }

    /**
     * Random int.
     *
     * @param  int|null $min
     * @param  int|null $max
     * @return int
     * @since  4.0
     */
    public static function randomInt(int $min = null, int $max = null): int
    {
        $min = $min ?? 0;
        $max = $max ?? PHP_INT_MAX;

        return random_int($min, $max);
    }

    /**
     * Random float.
     *
     * @param  float|null $min
     * @param  float|null $max
     * @param  int|null   $precision
     * @return float
     * @since  4.0
     */
    public static function randomFloat(float $min = null, float $max = null, int $precision = null): float
    {
        $min = $min ?? 0;
        $max = $max ?? 1 + $min;

        $ret = lcg_value() * ($max - $min) + $min;

        if ($precision !== null) {
            $ret = round($ret, $precision);
        }

        return $ret;
    }

}
