<?php
/**
 * Copyright (c) 2015 Kerem Güneş
 *
 * MIT License <https://opensource.org/licenses/mit>
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
    public static function compare($a, $b, int $precision = 2): ?int
    {
        if (is_numeric($a) && is_numeric($b)) {
            if (function_exists('bccomp')) {
                return bccomp(strval($a), strval($b), $precision);
            }

            $a = round($a, $precision);
            $b = round($b, $precision);

            return ($a === $b) ? 0 : ($a > $b) ? 1 : -1;
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
    public static function isEqual($a, $b, int $precision = 2): bool
    {
        return (0 === self::compare($a, $b, $precision));
    }
}
