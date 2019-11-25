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

use froq\StaticClass;

/**
 * Strings.
 * @package froq\util
 * @object  froq\util\Strings
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 * @static
 */
final class Strings extends StaticClass
{
    /**
     * Compare.
     * @param  string $input1
     * @param  string $input2
     * @return int
     */
    public static function compare(string $input1, string $input2): int
    {
       return ($input1 > $input2) - ($input1 < $input2);
    }

    /**
     * Compare locale.
     * @param  string $locale
     * @param  string $input1
     * @param  string $input2
     * @return int
     */
    public static function compareLocale(string $locale, string $input1, string $input2): int
    {
        $localeDefault = setlocale(LC_COLLATE, 0);

        setlocale(LC_COLLATE, $locale);
        $result = strcoll($input1, $input2);
        setlocale(LC_COLLATE, $localeDefault); // reset locale

        return $result;
    }

    /**
     * Contains.
     * @param  string $input
     * @param  string $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static function contains(string $input, string $search, bool $caseSensitive = true): bool
    {
        return (false !== ($caseSensitive ? strpos($input, $search) : stripos($input, $search)));
    }

    /**
     * Contains any.
     * @param  string $input
     * @param  array  $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static function containsAny(string $input, array $searches,
        bool $caseSensitive = true): bool
    {
        foreach ($searches as $search) {
            if (self::contains($input, $search, $caseSensitive)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Contains all.
     * @param  string $input
     * @param  array  $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static function containsAll(string $input, array $searches,
        bool $caseSensitive = true): bool
    {
        foreach ($searches as $search) {
            if (!self::contains($input, $search, $caseSensitive)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Starts with.
     * @param  string $input
     * @param  string $search
     * @return bool
     */
    public static function startsWith(string $input, string $search): bool
    {
        return ($search === substr($input, 0, strlen($search)));
    }

    /**
     * Ends with.
     * @param  string $input
     * @param  string $search
     * @return bool
     */
    public static function endsWith(string $input, string $search): bool
    {
        return ($search === substr($input, -strlen($search)));
    }

    /**
     * Trim search.
     * @param  string $input
     * @param  string $search
     * @param  bool   $caseSensitive
     * @param  int    $side
     * @return string
     */
    public static function trimSearch(string $input, string $search, bool $caseSensitive = true,
        int $side = 0): string
    {
        $search = preg_quote($search);
        $pattern = sprintf('~%s~%s', $side == 0 ? "^{$search}|{$search}$" : (
            $side == 1 ? "^{$search}" : "{$search}$"), $caseSensitive ? '' : 'i');

        return (string) preg_replace($pattern, '', $input);
    }

    /**
     * Is utf.
     * @param  ?string $input
     * @param  int     $bits
     * @return bool
     */
    public static function isUtf(?string $input, int $bits = 8): bool
    {
        // 0x00 - 0x10FFFF @link https://en.wikipedia.org/wiki/Code_point
        return !!($input && mb_check_encoding($input, 'UTF-'. $bits));
    }

    /**
     * Is ascii.
     * @param  ?string $input
     * @return bool
     */
    public static function isAscii(?string $input): bool
    {
        // 0x00 - 0x7F (or extended 0xFF) @link https://en.wikipedia.org/wiki/Code_point
        return !!($input && mb_check_encoding($input, 'ASCII'));
    }

    /**
     * Is binary.
     * @param  ?string $input
     * @return bool
     */
    public static function isBinary(?string $input): bool
    {
        return !!($input && !ctype_print($input));
    }

    /**
     * Is base64.
     * @param  ?string $input
     * @return bool
     */
    public static function isBase64(?string $input): bool
    {
        return !!($input && base64_encode(''. base64_decode($input, true)) == $input);
    }
}
