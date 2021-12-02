<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use froq\util\UtilException;
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
        if (is_numeric($in)) {
            if ($decimals !== null) {
                $in = number_format((float) $in, $decimals);
            }

            return $in + 0;
        }

        return null; // Error, not a number.
    }

    /**
     * Compare.
     *
     * @param  int|float $a
     * @param  int|float $b
     * @param  int|null  $precision
     * @return int
     */
    public static function compare(int|float $a, int|float $b, int $precision = null): int
    {
        $precision ??= 14;

        return round((float) $a, $precision) <=> round((float) $b, $precision);
    }

    /**
     * Equals.
     * @param  int|float $a
     * @param  int|float $b
     * @param  int|null  $precision
     * @return bool
     */
    public static function equals(int|float $a, int|float $b, int $precision = null): bool
    {
        return self::compare($a, $b, $precision) === 0;
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
     * Random.
     *
     * @param  int|float|null $min
     * @param  int|float|null $max
     * @param  int|null       $precision
     * @return int|float
     * @since  5.14
     * @throws froq\util\UtilException
     */
    public static function random(int|float $min = null, int|float $max = null, int $precision = null): int|float
    {
        $min ??= 0;
        $max ??= PHP_INT_MAX;

        if ($min === $max) {
            return $min;
        } elseif ($min > $max) {
            // Nope, not like rand()..
            // [$min, $max] = [$max, $min];

            throw new UtilException('Min value must be less than max value');
        }

        if (is_int($min) && is_int($max)) {
            // Interestingly ~50% slower (in some/what cases)..
            $ret = random_int($min, $max);

            // Nope..
            // srand();
            // $ret = rand() % ($max - $min) + $min;
        } else {
            $ret = lcg_value() * ($max - $min) + $min;
            if ($precision !== null) {
                $ret = round($ret, $precision);
            }
        }

        return $ret;
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
        return self::random($min, $max ?? PHP_INT_MAX);
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
        return self::random($min, $max ?? $min + 1.0, $precision);
    }
}
