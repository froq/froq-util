<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use froq\util\UtilException;

/**
 * Numbers.
 *
 * @package froq\util
 * @object  froq\util\Numbers
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final class Numbers extends \StaticClass
{
    /** Constants. */
    public const PRECISION  = PRECISION,
                 EPSILON    = PHP_FLOAT_EPSILON,
                 MAX_INT    = PHP_INT_MAX,
                 MAX_FLOAT  = PHP_FLOAT_MAX;

    /**
     * Convert.
     *
     * @param  int|float|string $input
     * @param  int|null         $decimals
     * @return int|float
     * @since  4.0
     */
    public static function convert(int|float|string $input, int $decimals = null): int|float
    {
        $input =@ format_number($input, $decimals);

        return ($input !== null) ? ($input + 0) : NAN; // Invalid, not a number.
    }

    /**
     * Compare.
     *
     * @param  int|float $number1
     * @param  int|float $number2
     * @param  int|null  $precision
     * @return int
     */
    public static function compare(int|float $number1, int|float $number2, int $precision = null): int
    {
        $precision ??= self::PRECISION;

        return round((float) $number1, $precision) <=> round((float) $number2, $precision);
    }

    /**
     * Equals.
     * @param  int|float $number1
     * @param  int|float $number2
     * @param  int|null  $precision
     * @return bool
     */
    public static function equals(int|float $number1, int|float $number2, int $precision = null): bool
    {
        return self::compare($number1, $number2, $precision) === 0;
    }

    /**
     * Check whether given input is number.
     *
     * @param  any $input
     * @return bool
     */
    public static function isNumber($input): bool
    {
        return is_number($input);
    }

    /**
     * Check whether given input is digit.
     *
     * @param  any $input
     * @return bool
     */
    public static function isDigit($input): bool
    {
        return is_numeric($input) && ($input >= 0)
            && (is_int($input) || ctype_digit((string) $input));
    }

    /**
     * Check whether given input is an ID (useful for any (db) incremental id check).
     *
     * @param  any $input
     * @return bool
     */
    public static function isId($input): bool
    {
        return is_numeric($input) && ($input >= 1)
            && (is_int($input) || ctype_digit((string) $input));
    }

    /**
     * Check whether given input is uint.
     *
     * @param  any $input
     * @return bool
     */
    public static function isUInt($input): bool
    {
        return is_int($input) && ($input >= 0);
    }

    /**
     * Check whether given input is ufloat.
     *
     * @param  any $input
     * @return bool
     */
    public static function isUFloat($input): bool
    {
        return is_float($input) && ($input >= 0);
    }

    /**
     * Check whether given input is signed.
     *
     * @param  any $input
     * @return bool
     */
    public static function isSigned($input): bool
    {
        return is_number($input) && ($input < 0);
    }

    /**
     * Check whether given input is unsigned.
     * @param  any $input
     * @return bool
     */
    public static function isUnsigned($input): bool
    {
        return is_number($input) && ($input >= 0);
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
        // For floats.
        $maxOrig = $max;

        $min ??= 0;
        $max ??= self::MAX_INT;

        if ($min === $max) {
            return $min;
        } elseif ($min > $max) {
            // Nope, not like rand()..
            // [$min, $max] = [$max, $min];

            throw new UtilException('Min value must be less than max value');
        }

        if (is_int($min) && is_int($max)) {
            // Interestingly ~50% slower (in some/what cases)..
            // $ret = random_int($min, $max);

            srand();
            $ret = ($min == 0 && $max == 1)
                 ? rand(0, 1)                     // Just in case.
                 : rand() % ($max - $min) + $min; // Prevent big numbers.
        } else {
            $max = $maxOrig ?? ($min + 1.0);
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
        return self::random($min, $max ?? self::MAX_INT);
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
        return self::random($min, $max ?? ($min + 1.0), $precision);
    }
}
