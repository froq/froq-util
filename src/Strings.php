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

use froq\common\objects\StaticClass;

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
     * @param  string $in1
     * @param  string $in2
     * @return int
     */
    public static function compare(string $in1, string $in2): int
    {
       return ($in1 > $in2) - ($in1 < $in2);
    }

    /**
     * Compare locale.
     * @param  string $in1
     * @param  string $in2
     * @param  string $locale
     * @return int
     */
    public static function compareLocale(string $in1, string $in2, string $locale): int
    {
        static $localeDefault;
        if ($localeDefault === null) {
            $localeDefault = setlocale(LC_COLLATE, 0);
        }

        setlocale(LC_COLLATE, $locale);
        $result = strcoll($in1, $in2);

        if ($localeDefault !== null) {
            setlocale(LC_COLLATE, $localeDefault); // Restore locale.
        }

        return $result;
    }

    /**
     * Contains.
     * @param  string $in
     * @param  string $search
     * @param  bool   $caseInsensitive
     * @return bool
     */
    public static function contains(string $in, string $search, bool $caseInsensitive = false): bool
    {
        return (!$caseInsensitive ? strpos($in, $search) : stripos($in, $search)) !== false;
    }

    /**
     * Contains any.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $caseInsensitive
     * @return bool
     */
    public static function containsAny(string $in, array $searches, bool $caseInsensitive = false): bool
    {
        foreach ($searches as $search) {
            if (self::contains($in, $search, $caseInsensitive)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Contains all.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $caseInsensitive
     * @return bool
     */
    public static function containsAll(string $in, array $searches, bool $caseInsensitive = false): bool
    {
        foreach ($searches as $search) {
            if (!self::contains($in, $search, $caseInsensitive)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Starts with.
     * @param  string $in
     * @param  string $search
     * @param  bool   $caseInsensitive
     * @return bool
     */
    public static function startsWith(string $in, string $search, bool $caseInsensitive = false,
        bool $multiByte = false): bool
    {
        if ($in !== '') {
            if ($caseInsensitive && $multiByte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $in  = mb_convert_case(mb_convert_case($in, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $search = mb_convert_case(mb_convert_case($search, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }
            return substr_compare($in, $search, 0, strlen($search), $caseInsensitive) === 0;
        }
        return false;
    }

    /**
     * Starts with any.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $caseInsensitive
     * @return bool
     * @since  4.0
     */
    public static function startsWithAny(string $in, array $searches, bool $caseInsensitive = false,
        bool $multiByte = false): bool
    {
        foreach ($searches as $search) {
            if (self::startsWith($in, $search, $caseInsensitive, $multiByte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ends with.
     * @param  string $in
     * @param  string $search
     * @return bool
     */
    public static function endsWith(string $in, string $search, bool $caseInsensitive = false,
        bool $multiByte = false): bool
    {
        if ($in !== '') {
            if ($caseInsensitive && $multiByte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $in  = mb_convert_case(mb_convert_case($in, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $search = mb_convert_case(mb_convert_case($search, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }
            return substr_compare($in, $search, -strlen($search), null, $caseInsensitive) === 0;
        }
        return false;
    }

    /**
     * Ends with any.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $caseInsensitive
     * @return bool
     * @since  4.0
     */
    public static function endsWithAny(string $in, array $searches, bool $caseInsensitive = false,
        bool $multiByte = false): bool
    {
        foreach ($searches as $search) {
            if (self::endsWith($in, $search, $caseInsensitive, $multiByte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is utf.
     * @param  string $in
     * @param  int     $bits
     * @return bool
     * @since  4.0
     */
    public static function isUtf(string $in, int $bits = 8): bool
    {
        // 0x00 - 0x10FFFF @link https://en.wikipedia.org/wiki/Code_point
        return ($in && mb_check_encoding($in, 'UTF-'. $bits));
    }

    /**
     * Is ascii.
     * @param  string $in
     * @return bool
     * @since  4.0
     */
    public static function isAscii(string $in): bool
    {
        // 0x00 - 0x7F (or extended 0xFF) @link https://en.wikipedia.org/wiki/Code_point
        return ($in && mb_check_encoding($in, 'ASCII'));
    }

    /**
     * Is binary.
     * @param  string $in
     * @return bool
     * @since  4.0
     */
    public static function isBinary(string $in): bool
    {
        return ($in && ($in = str_replace(["\t", "\n", "\r"], '', $in)) && !ctype_print($in));
    }

    /**
     * Is base64.
     * @param  string $in
     * @return bool
     * @since  4.0
     */
    public static function isBase64(string $in): bool
    {
        return ($in && !strcmp($in, ''. base64_encode(''. base64_decode($in, true))));
    }
}
