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
     * @param  number   $a
     * @param  number   $b
     * @param  int|null $precision
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

        return null; // error, not number(s)
    }

    /**
     * Equals.
     * @param  number   $a
     * @param  number   $b
     * @param  int|null $precision
     * @return ?bool
     */
    public static function equals($a, $b, int $precision = null): ?bool
    {
        return ($return = self::compare($a, $b, $precision)) === null ? null // error, not number(s)
            : !$return;
    }

    /**
     * Is id (useful for any (db) object id checking).
     * @param  number $input
     * @return bool
     */
    public static function isId($input): bool
    {
        return is_numeric($input) && intval($input) > 0;
    }

    /**
     * Is uint.
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
     * Is ufloat.
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
     * Is signed.
     * @param  number $input
     * @return bool
     */
    public static function isSigned($input): bool
    {
        return is_numeric($input) && floatval($input) <= 0;
    }

    /**
     * Is unsigned.
     * @param  number $input
     * @return bool
     */
    public static function isUnsigned($input): bool
    {
        return is_numeric($input) && floatval($input) >= 0;
    }
}
