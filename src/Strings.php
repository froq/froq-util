<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use froq\util\UtilException;

/**
 * Strings.
 *
 * @package froq\util
 * @object  froq\util\Strings
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final class Strings extends \StaticClass
{
    /**
     * Compare two strings.
     *
     * @param  string $string1
     * @param  string $string2
     * @return int
     */
    public static function compare(string $string1, string $string2): int
    {
        // Old stuff, same with "<=>" operator.
        // return ($string1 > $string2) - ($string1 < $string2);

        return ($string1 <=> $string2);
    }

    /**
     * Compare two strings by a locale.
     *
     * @param  string $string1
     * @param  string $string2
     * @param  string $locale
     * @return int
     */
    public static function compareLocale(string $string1, string $string2, string $locale): int
    {
        static $currentLocale;
        $currentLocale ??= getlocale(LC_COLLATE);

        // Should change?
        if ($locale !== $currentLocale) {
            setlocale(LC_COLLATE, $locale);
        }

        $result = strcoll($string1, $string2);

        // Restore (if needed).
        if ($locale !== $currentLocale && $currentLocale !== null) {
            setlocale(LC_COLLATE, $currentLocale);
        }

        return $result;
    }

    /**
     * Check whether given input contains given search.
     *
     * @param  string $string
     * @param  string $search
     * @param  bool   $icase
     * @return bool
     */
    public static function contains(string $string, string $search, bool $icase = false): bool
    {
        return (!$icase ? mb_strpos($string, $search) : mb_stripos($string, $search)) !== false;
    }

    /**
     * Check whether given input contains any of given searches.
     *
     * @param  string        $string
     * @param  array<string> $searches
     * @param  bool          $icase
     * @return bool
     */
    public static function containsAny(string $string, array $searches, bool $icase = false): bool
    {
        foreach ($searches as $search) {
            if (self::contains($string, $search, $icase)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether given input contains all given search.
     *
     * @param  string        $string
     * @param  array<string> $searches
     * @param  bool          $icase
     * @return bool
     */
    public static function containsAll(string $string, array $searches, bool $icase = false): bool
    {
        foreach ($searches as $search) {
            if (!self::contains($string, $search, $icase)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether given input starts with given search.
     *
     * @param  string $string
     * @param  string $search
     * @param  bool   $icase
     * @param  bool   $mbyte
     * @return bool
     */
    public static function startsWith(string $string, string $search, bool $icase = false, bool $mbyte = false): bool
    {
        if ($string !== '') {
            if ($icase && $mbyte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $string = mb_convert_case(mb_convert_case($string, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $search = mb_convert_case(mb_convert_case($search, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }

            return substr_compare($string, $search, 0, strlen($search), $icase) === 0;
        }

        return false;
    }

    /**
     * Check whether given input starts with any of given searches.
     *
     * @param  string        $string
     * @param  array<string> $searches
     * @param  bool          $icase
     * @param  bool          $mbyte
     * @return bool
     * @since  4.0
     */
    public static function startsWithAny(string $string, array $searches, bool $icase = false, bool $mbyte = false): bool
    {
        foreach ($searches as $search) {
            if (self::startsWith($string, (string) $search, $icase, $mbyte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether given input ends with given search.
     *
     * @param  string $string
     * @param  string $search
     * @param  bool   $icase
     * @param  bool   $mbyte
     * @return bool
     */
    public static function endsWith(string $string, string $search, bool $icase = false, bool $mbyte = false): bool
    {
        if ($string !== '') {
            if ($icase && $mbyte) {
                // Double, cos for eg: Turkish characters issues (ı => I, İ => i).
                $string = mb_convert_case(mb_convert_case($string, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
                $search = mb_convert_case(mb_convert_case($search, MB_CASE_UPPER_SIMPLE), MB_CASE_LOWER_SIMPLE);
            }

            return substr_compare($string, $search, -strlen($search), null, $icase) === 0;
        }

        return false;
    }

    /**
     * Check whether given input ends with any of given searches.
     *
     * @param  string        $string
     * @param  array<string> $searches
     * @param  bool          $icase
     * @param  bool          $mbyte
     * @return bool
     * @since  4.0
     */
    public static function endsWithAny(string $string, array $searches, bool $icase = false, bool $mbyte = false): bool
    {
        foreach ($searches as $search) {
            if (self::endsWith($string, (string) $search, $icase, $mbyte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether given input encoding is UTF.
     *
     * @param  string $string
     * @param  int     $bits
     * @return bool
     * @since  4.0
     */
    public static function isUtf(string $string, int $bits = 8): bool
    {
        // 0x00 - 0x10FFFF @link https://en.wikipedia.org/wiki/Code_point
        return ($string && mb_check_encoding($string, 'UTF-'. $bits));
    }

    /**
     * Check whether given input encoding is ASCII.
     *
     * @param  string $string
     * @return bool
     * @since  4.0
     */
    public static function isAscii(string $string): bool
    {
        // 0x00 - 0x7F (or extended 0xFF) @link https://en.wikipedia.org/wiki/Code_point
        return ($string && mb_check_encoding($string, 'ASCII'));
    }

    /**
     * Check whether given input contains binary.
     *
     * @param  string $string
     * @return bool
     * @since  4.0
     */
    public static function isBinary(string $string): bool
    {
        return ($string && ($string = str_replace(["\t", "\n", "\r"], '', $string)) && !ctype_print($string));
    }

    /**
     * Check whether given input is base64-ed.
     *
     * @param  string $string
     * @return bool
     * @since  4.0
     */
    public static function isBase64(string $string): bool
    {
        return ($string && !strcmp($string, ''. base64_encode(''. base64_decode($string, true))));
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
