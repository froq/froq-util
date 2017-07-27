<?php
/**
 * Copyright (c) 2016 Kerem Güneş
 *    <k-gun@mail.com>
 *
 * GNU General Public License v3.0
 *    <http://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
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
        if (!is_numeric($in) || !!strpbrk(strval($in), '-.')) {
            return false;
        }

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
        if (!is_numeric($in) || (strval($in)[0] == '-')) {
            return false;
        }

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

        return ($out = abs($in)) >= 0;
    }

    // @wait
    // public static function isClass($input): bool {}
    // public static function isAbstractClass($input): bool {}
    // public static function isTrait($input): bool {}
    // public static function isInterface($input): bool {}

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
