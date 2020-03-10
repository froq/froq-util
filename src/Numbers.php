<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace froq\util;

use froq\common\objects\StaticClass;

/**
 * Numbers.
 * @package froq\util
 * @object  froq\util\Numbers
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 * @static
 */
final class Numbers extends StaticClass
{
    /**
     * Convert.
     * @param  numeric  $in
     * @param  int|null $decimals
     * @return int|float|null
     * @since  4.0
     */
    public static function convert($in, int $decimals = null)
    {
        if (is_numeric($in)) {
            if (is_int($in) || is_float($in)) {
                return $in;
            }

            if ($decimals !== null) {
                $in = number_format((float) $in, $decimals);
            }

            return is_string($in) && strpos($in, '.') > -1
                ? (float) $in : (int) $in;
        }

        return null; // Error, not a number.
    }

    /**
     * Compare.
     * @param  numeric $a
     * @param  numeric $b
     * @param  int     $precision
     * @return ?int
     */
    public static function compare($a, $b, int $precision = 14): ?int
    {
        if (is_numeric($a) && is_numeric($b)) {
            return round($a, $precision) <=> round($b, $precision);
        }
        return null; // Error, non-number(s).
    }

    /**
     * Equals.
     * @param  numeric $a
     * @param  numeric $b
     * @param  int     $precision
     * @return ?bool
     */
    public static function equals($a, $b, int $precision = 14): ?bool
    {
        return ($ret = self::compare($a, $b, $precision)) === null ? null // Error, non-number(s).
             : ($ret === 0);
    }

    /**
     * Is digit.
     * @param  any $in
     * @return bool
     */
    public static function isNumber($in): bool
    {
        return is_int($in) || is_float($in);
    }

    /**
     * Is digit.
     * @param  any $in
     * @return bool
     */
    public static function isDigit($in): bool
    {
        return is_numeric($in) && (is_int($in) || ctype_digit($in)) && ($in >= 0);
    }

    /**
     * Is id (useful for any (db) incremental id check).
     * @param  any $in
     * @return bool
     */
    public static function isId($in): bool
    {
        return is_numeric($in) && (is_int($in) || ctype_digit($in)) && ($in >= 1);
    }

    /**
     * Is uint.
     * @param  any $in
     * @return bool
     */
    public static function isUInt($in): bool
    {
        return is_int($in) && ($in >= 0);
    }

    /**
     * Is ufloat.
     * @param  any $in
     * @return bool
     */
    public static function isUFloat($in): bool
    {
        return is_float($in) && ($in >= 0);
    }

    /**
     * Is signed.
     * @param  any $in
     * @return bool
     */
    public static function isSigned($in): bool
    {
        return (is_int($in) || is_float($in)) && ($in <= 0);
    }

    /**
     * Is unsigned.
     * @param  any $in
     * @return bool
     */
    public static function isUnsigned($in): bool
    {
        return (is_int($in) || is_float($in)) && ($in >= 0);
    }

    /**
     * Random int.
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
     * @param  float|null $min
     * @param  float|null $max
     * @return float
     * @since  4.0
     */
    public static function randomFloat(float $min = null, float $max = null): float
    {
        $min = $min ?? 0;
        $max = $max ?? 1 + $min;

        return lcg_value() * ($max - $min) + $min;
    }

}
