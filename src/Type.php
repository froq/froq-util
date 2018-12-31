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
 * @object     Froq\Util\Type
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final /* static */ class Type
{
    /**
     * To int.
     * @param  number $a
     * @return ?int
     */
    public static function toInt($a): ?int
    {
        return is_numeric($a) ? intval($a) : null;
    }

    /**
     * To float.
     * @param  number $a
     * @return ?float
     */
    public static function toFloat($a): ?float
    {
        return is_numeric($a) ? floatval($a) : null;
    }

    /**
     * To bool.
     * @param  any $a
     * @return ?bool
     */
    public static function toBool($a): ?bool
    {
        $a = strval($a);
        if ($a === '1' || $a === '0') {
            return boolval($a);
        }
        if ($a === 'true' || $a === 'false') {
            return $a === 'true';
        }
        return null;
    }

    /**
     * Is UInt.
     * @param  any       $in
     * @param  int|null &$out
     * @param  bool      $strict
     * @return bool
     */
    public static function isUint($in, &$out = null, bool $strict = false): bool
    {
        if ($strict && is_float($in)) {
            return false;
        }
        if (!is_numeric($in) || strpbrk(strval($in), '-.') !== false) {
            return false;
        }
        // convert to type
        return ($out = intval($in)) >= 0;
    }

    /**
     * Is UFloat.
     * @param  any         $in
     * @param  float|null &$out
     * @param  bool        $strict
     * @return bool
     */
    public static function isUfloat($in, &$out = null, bool $strict = false): bool
    {
        if ($strict && is_int($in)) {
            return false;
        }
        if (!is_numeric($in) || (strval($in)[0] ?? '') === '-') {
            return false;
        }
        // convert to type
        return ($out = floatval($in)) >= 0.0;
    }

    /**
     * Is Unsigned.
     * @param  any             $in
     * @param  int|float|null &$out
     * @return bool
     */
    public static function isUnsigned($in, &$out = null): bool
    {
        if (!is_numeric($in) || (strval($in)[0] == '-')) {
            return false;
        }
        // convert to type
        return ($out = abs($in)) >= 0;
    }

    // @wait
    // public static function isClass($x): bool {}
    // public static function isAbstractClass($x): bool {}
    // public static function isInterface($x): bool {}
    // public static function isTrait($x): bool {}
    // public static function isException($x, \Throwable $t = null): bool {}

    // @links
    // https://golang.org/src/builtin/builtin.go
    // https://msdn.microsoft.com/en-us/library/exx3b86w.aspx
    // http://dev.mysql.com/doc/refman/5.7/en/integer-types.html
    // http://stackoverflow.com/questions/3724242/what-is-the-difference-between-int-and-uint-long-and-ulong

    // @wait
    // public static function isSigned($in, &$out = null): bool {}
    // public static function isUnsigned($in, &$out = null): bool {}
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

    // @shortcuts
    // public static function isByte($in, &$out = null): bool {} // -128 to 127
    // public static function isUByte($in, &$out = null): bool {} // 0 to 255
    // public static function isShort($in, &$out = null): bool {}
    // public static function isUShort($in, &$out = null): bool {}
    // public static function isLong($in, &$out = null): bool {}
    // public static function isULong($in, &$out = null): bool {}
}
