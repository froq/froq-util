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
        if (is_bool($a)) {
            return $a;
        }

        $a = ''. $a; // to string
        return ($a === '1' || $a === '0') ? boolval($a)
            : (($a === 'true' || $a === 'false') ? $a === 'true' : null);
    }

    // @wait
    // public static function isClass($x): bool {}
    // public static function isAbstractClass($x): bool {}
    // public static function isInterface($x): bool {}
    // public static function isTrait($x): bool {}
    // public static function isException($x, \Throwable $t = null): bool {}
}
