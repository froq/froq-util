<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use froq\util\UtilException;
use froq\common\object\StaticClass;

/**
 * Strings.
 *
 * @package froq\util
 * @object  froq\util\Strings
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final class Strings extends StaticClass
{
    /**
     * Compare two strings.
     *
     * @param  string $in1
     * @param  string $in2
     * @return int
     */
    public static function compare(string $in1, string $in2): int
    {
        // Old stuff, same with "<=>" operator.
        // return ($in1 > $in2) - ($in1 < $in2);

        return $in1 <=> $in2;
    }

    /**
     * Compare two strings by a locale.
     *
     * @param  string $in1
     * @param  string $in2
     * @param  string $locale
     * @return int
     */
    public static function compareLocale(string $in1, string $in2, string $locale): int
    {
        static $currentLocale;
        $currentLocale ??= setlocale(LC_COLLATE, 0);

        // Should change?
        if ($locale !== $currentLocale) {
            setlocale(LC_COLLATE, $locale);
        }

        $result = strcoll($in1, $in2);

        // Restore (if needed).
        if ($locale !== $currentLocale && $currentLocale !== null) {
            setlocale(LC_COLLATE, $currentLocale);
        }

        return $result;
    }

    /**
     * Check whether given input contains given search.
     *
     * @param  string $in
     * @param  string $src
     * @param  bool   $icase
     * @return bool
     */
    public static function contains(string $in, string $src, bool $icase = false): bool
    {
        return (!$icase ? mb_strpos($in, $src) : mb_stripos($in, $src)) !== false;
    }

    /**
     * Check whether given input contains any of given searches.
     *
     * @param  string        $in
     * @param  array<string> $srcs
     * @param  bool          $icase
     * @return bool
     */
    public static function containsAny(string $in, array $srcs, bool $icase = false): bool
    {
        foreach ($srcs as $src) {
            if (self::contains($in, $src, $icase)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether given input contains all given search.
     *
     * @param  string        $in
     * @param  array<string> $srcs
     * @param  bool          $icase
     * @return bool
     */
    public static function containsAll(string $in, array $srcs, bool $icase = false): bool
    {
        foreach ($srcs as $src) {
            if (!self::contains($in, $src, $icase)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether given input starts with given search.
     *
     * @param  string $in
     * @param  string $src
     * @param  bool   $icase
     * @param  bool   $mbyte
     * @return bool
     */
    public static function startsWith(string $in, string $src, bool $icase = false, bool $mbyte = false): bool
    {
        if ($in !== '') {
            if ($icase && $mbyte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $in = mb_convert_case(mb_convert_case($in, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $src = mb_convert_case(mb_convert_case($src, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }

            return substr_compare($in, $src, 0, strlen($src), $icase) === 0;
        }

        return false;
    }

    /**
     * Check whether given input starts with any of given searches.
     *
     * @param  string        $in
     * @param  array<string> $srcs
     * @param  bool          $icase
     * @param  bool          $mbyte
     * @return bool
     * @since  4.0
     */
    public static function startsWithAny(string $in, array $srcs, bool $icase = false, bool $mbyte = false): bool
    {
        foreach ($srcs as $src) {
            if (self::startsWith($in, (string) $src, $icase, $mbyte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether given input ends with given search.
     *
     * @param  string $in
     * @param  string $src
     * @param  bool   $icase
     * @param  bool   $mbyte
     * @return bool
     */
    public static function endsWith(string $in, string $src, bool $icase = false, bool $mbyte = false): bool
    {
        if ($in !== '') {
            if ($icase && $mbyte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $in = mb_convert_case(mb_convert_case($in, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $src = mb_convert_case(mb_convert_case($src, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }

            return substr_compare($in, $src, -strlen($src), null, $icase) === 0;
        }

        return false;
    }

    /**
     * Check whether given input ends with any of given searches.
     *
     * @param  string        $in
     * @param  array<string> $srcs
     * @param  bool          $icase
     * @param  bool          $mbyte
     * @return bool
     * @since  4.0
     */
    public static function endsWithAny(string $in, array $srcs, bool $icase = false, bool $mbyte = false): bool
    {
        foreach ($srcs as $src) {
            if (self::endsWith($in, (string) $src, $icase, $mbyte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether given input encoding is UTF.
     *
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
     * Check whether given input encoding is ASCII.
     *
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
     * Check whether given input contains binary.
     *
     * @param  string $in
     * @return bool
     * @since  4.0
     */
    public static function isBinary(string $in): bool
    {
        return ($in && ($in = str_replace(["\t", "\n", "\r"], '', $in)) && !ctype_print($in));
    }

    /**
     * Check whether given input is base64-ed.
     *
     * @param  string $in
     * @return bool
     * @since  4.0
     */
    public static function isBase64(string $in): bool
    {
        return ($in && !strcmp($in, ''. base64_encode(''. base64_decode($in, true))));
    }

    /**
     * Generate a random output by given length.
     *
     * @param  int  $length
     * @param  bool $puncted
     * @return string
     * @throws froq\util\UtilException
     */
    public static function random(int $length, bool $puncted = false): string
    {
        if ($length < 1) {
            throw new UtilException('Invalid length value `%s`, length must be minimun 1', $length);
        }

        $chars = BASE62_ALPHABET;
        if ($puncted) { // Add punctuation chars.
            $chars .= '!^+%&/\(){}[]<>=*?-_|$#.:,;';
        }
        $charsLength = strlen($chars);

        $ret = '';

        srand();
        while (strlen($ret) < $length) {
            $ret .= $chars[rand(0, $charsLength - 1)];
        }

        return $ret;
    }
}
