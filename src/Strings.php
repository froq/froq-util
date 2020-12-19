<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\util;

use froq\common\object\StaticClass;

/**
 * Strings.
 *
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
        static $default; $default ??= setlocale(LC_COLLATE, 0);

        setlocale(LC_COLLATE, $locale);
        $result = strcoll($in1, $in2);

        if ($default !== null) {
            setlocale(LC_COLLATE, $default); // Restore locale.
        }

        return $result;
    }

    /**
     * Contains.
     * @param  string $in
     * @param  string $search
     * @param  bool   $icase
     * @return bool
     */
    public static function contains(string $in, string $search, bool $icase = false): bool
    {
        return (!$icase ? strpos($in, $search) : stripos($in, $search)) !== false;
    }

    /**
     * Contains any.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $icase
     * @return bool
     */
    public static function containsAny(string $in, array $searches, bool $icase = false): bool
    {
        foreach ($searches as $search) {
            if (self::contains($in, $search, $icase)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Contains all.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $icase
     * @return bool
     */
    public static function containsAll(string $in, array $searches, bool $icase = false): bool
    {
        foreach ($searches as $search) {
            if (!self::contains($in, $search, $icase)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Starts with.
     * @param  string $in
     * @param  string $search
     * @param  bool   $icase
     * @param  bool   $mbyte
     * @return bool
     */
    public static function startsWith(string $in, string $search, bool $icase = false, bool $mbyte = false): bool
    {
        if ($in !== '') {
            if ($icase && $mbyte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $in = mb_convert_case(mb_convert_case($in, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $search = mb_convert_case(mb_convert_case($search, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }

            return substr_compare($in, $search, 0, strlen($search), $icase) === 0;
        }

        return false;
    }

    /**
     * Starts with any.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $icase
     * @param  bool          $mbyte
     * @return bool
     * @since  4.0
     */
    public static function startsWithAny(string $in, array $searches, bool $icase = false, bool $mbyte = false): bool
    {
        foreach ($searches as $search) {
            if (self::startsWith($in, (string) $search, $icase, $mbyte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ends with.
     * @param  string $in
     * @param  string $search
     * @param  bool   $icase
     * @param  bool   $mbyte
     * @return bool
     */
    public static function endsWith(string $in, string $search, bool $icase = false, bool $mbyte = false): bool
    {
        if ($in !== '') {
            if ($icase && $mbyte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $in = mb_convert_case(mb_convert_case($in, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $search = mb_convert_case(mb_convert_case($search, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }

            return substr_compare($in, $search, -strlen($search), null, $icase) === 0;
        }

        return false;
    }

    /**
     * Ends with any.
     * @param  string        $in
     * @param  array<string> $searches
     * @param  bool          $icase
     * @param  bool          $mbyte
     * @return bool
     * @since  4.0
     */
    public static function endsWithAny(string $in, array $searches, bool $icase = false, bool $mbyte = false): bool
    {
        foreach ($searches as $search) {
            if (self::endsWith($in, (string) $search, $icase, $mbyte)) {
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
