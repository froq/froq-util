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
     * @param  string   $source
     * @param  string   $search
     * @param  int|null offset
     * @param  bool     $caseSensitive
     * @return bool
     */
    public static function contains(string $source, string $search, int $offset = null,
        bool $caseSensitive = true): bool
    {
        $offset = $offset ?? 0;

        // fix: 'offset not contained' error
        if ($offset && abs($offset) > strlen($source)) {
            return false;
        }

        return ($caseSensitive ? strpos($source, $search, $offset)
            : stripos($source, $search, $offset)) !== false;
    }

    /**
     * Contains any.
     * @param  string       $source
     * @param  string|array $search
     * @param  int|null     $offset
     * @param  bool         $caseSensitive
     * @return bool
     * @since  3.0
     */
    public static function containsAny(string $source, $searches, int $offset = null,
        bool $caseSensitive = true): bool
    {
        if (!is_array($searches)) {
            $searches = preg_split('~~u', $searches, -1, PREG_SPLIT_NO_EMPTY);
        }

        foreach ($searches as $search) {
            if (self::contains($source, $search, $offset, $caseSensitive)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Contains all.
     * @param  string       $source
     * @param  string|array $search
     * @param  int|null     $offset
     * @param  bool         $caseSensitive
     * @return bool
     * @since  3.0
     */
    public static function containsAll(string $source, $searches, int $offset = null,
        bool $caseSensitive = true): bool
    {
        if (!is_array($searches)) {
            $searches = preg_split('~~u', $searches, -1, PREG_SPLIT_NO_EMPTY);
        }

        foreach ($searches as $search) {
            if (!self::contains($source, $search, $offset, $caseSensitive)) {
                return false;
            }
        }
        return true;
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
