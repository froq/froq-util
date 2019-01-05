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
 */
final /* static */ class Numbers
{
    /**
     * Compare.
     * @param  number $a
     * @param  number $b
     * @param  int    $precision
     * @return ?int
     */
    public static function compare($a, $b, int $precision = null): ?int
    {
        if (is_numeric($a) && is_numeric($b)) {
            $precision = $precision ?? 14; // @default=14

            if (function_exists('bccomp')) {
                return bccomp((string) $a, (string) $b, $precision);
            }

            return round((float) $a, $precision) <=> round((float) $b, $precision);
        }

        return null;
    }

    /**
     * Is equal.
     * @param  number $a
     * @param  number $b
     * @param  int    $precision
     * @return bool
     */
    public static function isEqual($a, $b, int $precision = null): bool
    {
        return !self::compare($a, $b, $precision);
    }

    /**
     * Is id (useful for object id checking).
     * @param  number $input
     * @return bool
     */
    public static function isId($input): bool
    {
        return intval($input) > 0 && self::isUInt($input);
    }

    /**
     * Is UInt.
     * @param  number $input
     * @return bool
     */
    public static function isUInt($input): bool
    {
        if (!is_numeric($input) || is_float($input)) {
            return false;
        }

        $input = (string) json_encode($input, JSON_PRESERVE_ZERO_FRACTION);
        return strpbrk($input, '-.') === false;
    }

    /**
     * Is UFloat.
     * @param  number $input
     * @return bool
     */
    public static function isUFloat($input): bool
    {
        if (!is_numeric($input) || is_int($input)) {
            return false;
        }

        $input = (string) json_encode($input, JSON_PRESERVE_ZERO_FRACTION);
        return $input[0] !== '-' && strpos($input, '.') !== false;
    }

    /**
     * Is Signed.
     * @param  number $input
     * @return bool
     */
    public static function isSigned($input): bool
    {
        return is_numeric($input) && floatval($input) <= 0;
    }

    /**
     * Is Unsigned.
     * @param  number $input
     * @return bool
     */
    public static function isUnsigned($input): bool
    {
        return is_numeric($input) && floatval($input) >= 0;
    }

    // @links
    // https://golang.org/src/builtin/builtin.go
    // https://msdn.microsoft.com/en-us/library/exx3b86w.aspx
    // http://dev.mysql.com/doc/refman/5.7/en/integer-types.html
    // http://stackoverflow.com/questions/3724242/what-is-the-difference-between-int-and-uint-long-and-ulong

    // @wait
    // public static function isInt($in, &$out = null): bool {}
    // public static function isUInt($in, &$out = null): bool {}
    // public static function isInt8($in, &$out = null): bool {}
    // public static function isUInt8($in, &$out = null): bool {}
    // public static function isInt64($in, &$out = null): bool {}
    // public static function isUInt64($in, &$out = null): bool {}
    // public static function isFloat($in, &$out = null): bool {}
    // public static function isUFloat($in, &$out = null): bool {}
    // public static function isFloat32($in, &$out = null): bool {}
    // public static function isUFloat32($in, &$out = null): bool {}
    // public static function isFloat64($in, &$out = null): bool {}
    // public static function isUFloat64($in, &$out = null): bool {}

    // @shortcuts (maybe for sql type checks)
    // public static function isByte($in, &$out = null): bool {} // -128 to 127
    // public static function isUByte($in, &$out = null): bool {} // 0 to 255
    // public static function isShort($in, &$out = null): bool {}
    // public static function isUShort($in, &$out = null): bool {}
    // public static function isLong($in, &$out = null): bool {}
    // public static function isULong($in, &$out = null): bool {}
}
