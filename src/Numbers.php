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

namespace Froq\Util;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Numbers
 * @author     Kerem Güneş <k-gun@mail.com>
 * @since      1.0
 */
final /* static */ class Numbers
{
    /**
     * Compare.
     * @param  number   $a
     * @param  number   $b
     * @param  int|null $precision
     * @return ?int
     */
    public static function compare($a, $b, int $precision = null): ?int
    {
        if (self::isNumber($a) && self::isNumber($b)) {
            $precision = $precision ?? 14; // @default=14

            if (function_exists('bccomp')) {
                return bccomp((string) $a, (string) $b, $precision);
            }

            return round((float) $a, $precision) <=> round((float) $b, $precision);
        }

        return null; // error, not number(s)
    }

    /**
     * Equals.
     * @param  number   $a
     * @param  number   $b
     * @param  int|null $precision
     * @return ?bool
     * @since  3.0
     */
    public static function equals($a, $b, int $precision = null): ?bool
    {
        return ($ret = self::compare($a, $b, $precision)) !== null ? !$ret
            : null; // error, not number(s)
    }

    /**
     * Is digit.
     * @param  any $input
     * @return bool
     * @since  3.0
     */
    public final function isNumber($input): bool
    {
         return is_int($input) || is_float($input);
    }

    /**
     * Is digit.
     * @param  any $input
     * @param  bool $complex
     * @return bool
     * @since  3.0
     */
    public final function isDigit($input, bool $complex = true): bool
    {
        if (is_int($input)) {
            return true;
        } elseif (is_string($input) && ctype_digit($input)) {
            return true;
        } elseif ($complex && is_numeric($input)) {
            $input = (string) $input;
            if (strpos($input, '.') === false && ($input < 0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is id (useful for any (db) object id checking).
     * @param  any $input
     * @return bool
     */
    public static function isId($input): bool
    {
        return self::isNumber($input) && ($input > 0);
    }

    /**
     * Is uint.
     * @param  any $input
     * @return bool
     */
    public static function isUInt($input): bool
    {
        return !is_float($input) && is_int($input) && ($input >= 0);
    }

    /**
     * Is ufloat.
     * @param  any $input
     * @return bool
     */
    public static function isUFloat($input): bool
    {
        return !is_int($input) && is_float($input) && ($input >= 0);
    }

    /**
     * Is signed.
     * @param  any $input
     * @return bool
     */
    public static function isSigned($input): bool
    {
        return self::isNumber($input) && ($input <= 0);
    }

    /**
     * Is unsigned.
     * @param  any $input
     * @return bool
     */
    public static function isUnsigned($input): bool
    {
        return self::isNumber($input) && ($input >= 0);
    }
}
