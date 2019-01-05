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
 * @object     Froq\Util\Strings
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final /* static */ class Strings
{
    /**
     * Contains.
     * @param  string $source
     * @param  string $search
     * @param  int    $offset
     * @param  bool   $caseInsensitive
     * @return bool
     */
    public static function contains(string $source, string $search, int $offset = 0,
        bool $caseInsensitive = false): bool
    {
        return false !== (!$caseInsensitive ? strpos($source, $search, $offset)
            : stripos($source, $search, $offset));
    }

    /**
     * Contains any.
     * @param  string $source
     * @param  string $search
     * @param  bool   $caseInsensitive
     * @return bool
     */
    public static function containsAny(string $source, string $searches,
        bool $caseInsensitive = false): bool
    {
        return false !== (!$caseInsensitive ? strpbrk($source, $searches)
            : strpbrk(strtolower($source), strtolower($searches)));
    }

    /**
     * Starts with.
     * @param  string $source
     * @param  string $search
     * @return bool
     */
    public static function startsWith(string $source, string $search): bool
    {
        return ($search === substr($source, 0, strlen($search)));
    }

    /**
     * Ends with.
     * @param  string $source
     * @param  string $search
     * @return bool
     */
    public static function endsWith(string $source, string $search): bool
    {
        return ($search === substr($source, -strlen($search)));
    }
}
