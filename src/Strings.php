<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

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
     * @param  bool   $icase
     * @param  int    $length
     * @param  string $encoding
     * @return int
     */
    public static function compare(string $string1, string $string2, bool $icase = false, int $length = null,
        string $encoding = null): int
    {
        // Old stuff, same with "<=>" operator.
        // return ($string1 > $string2) - ($string1 < $string2);
        // return $string1 <=> $string2;

        if ($icase) {
            $ret = ($length !== null)
                 ? strncasecmp(mb_strtolower($string1, $encoding), mb_strtolower($string2, $encoding), $length)
                 : strcasecmp(mb_strtolower($string1, $encoding), mb_strtolower($string2, $encoding));
        } else {
            $ret = ($length !== null) ? strncmp($string1, $string2, $length) : strcmp($string1, $string2);
        }

        // Uniform result as 0, 1 or -1.
        return ($ret == 0) ? 0 : ($ret >= 1 ? 1 : -1);
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

        $ret = strcoll($string1, $string2);

        // Restore (if needed).
        if ($locale !== $currentLocale && $currentLocale !== null) {
            setlocale(LC_COLLATE, $currentLocale);
        }

        // Uniform result as 0, 1 or -1.
        return ($ret == 0) ? 0 : ($ret >= 1 ? 1 : -1);
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
            if (self::startsWith($string, $search, $icase, $mbyte)) {
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
            if (self::endsWith($string, $search, $icase, $mbyte)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether given input encoding is UTF.
     *
     * @param  string $string
     * @param  int    $bits
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
        return ($string && !strcmp($string, (string) base64_encode((string) base64_decode($string, true))));
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
            throw new UtilException(
                'Invalid length value `%s`, length must be minimun 1',
                $length
            );
        }

        // As default.
        $chars = BASE62_ALPHABET;

        // Add punctuation chars.
        if ($puncted) {
            $chars .= '!^+%&/\(){}[]<>=*?-_|$#.:,;';
        }

        $max = strlen($chars) - 1;
        $ret = '';

        while (strlen($ret) < $length) {
            $ret .= $chars[random_int(0, $max)];
        }

        return $ret;
    }

    /**
     * Unicode pad (@source https://www.php.net/manual/en/function.str-pad.php#111147).
     *
     * @param  string      $string
     * @param  int         $padLength
     * @param  string      $padString
     * @param  int         $padType
     * @param  string|null $encoding
     * @return string
     * @throws froq\util\UtilException
     */
    public static function pad(string $string, int $padLength, string $padString = ' ', int $padType = STR_PAD_RIGHT,
        string $encoding = null): string
    {
        $stringLength    = mb_strlen($string, $encoding);
        $padStringLength = mb_strlen($padString, $encoding);

        if (!$stringLength && ($padType == STR_PAD_RIGHT || $padType == STR_PAD_LEFT)) {
            $stringLength = 1; // @debug
        }
        if (!$padLength || !$padStringLength || $padLength <= $stringLength) {
            return $string;
        }

        $return = '';
        $repeat = ~~ceil($stringLength - $padStringLength + $padLength);
        if ($padType == STR_PAD_RIGHT) {
            $return = $string . str_repeat($padString, $repeat);
            $return = mb_substr($return, 0, $padLength, $encoding);
        } elseif ($padType == STR_PAD_LEFT) {
            $return = str_repeat($padString, $repeat) . $string;
            $return = mb_substr($return, -$padLength, null, $encoding);
        } elseif ($padType == STR_PAD_BOTH) {
            $length = ($padLength - $stringLength) / 2;
            $repeat = ~~ceil($length / $padStringLength);
            $return = mb_substr(str_repeat($padString, $repeat), 0, ~~floor($length), $encoding)
                    . $string
                    . mb_substr(str_repeat($padString, $repeat), 0, ~~ceil($length), $encoding);
        } else {
            throw new UtilException('Invalid pad type ' . $padType);
        }

        return $return;
    }

    /**
     * Unicode ord (@source https://stackoverflow.com/a/7153133/362780).
     *
     * @param  string $chr
     * @return int|null
     * @since  6.0
     */
    public static function ord(string $chr): int|null
    {
        $ord0 = ord($chr[0]);
        if ($ord0 >= 0 && $ord0 <= 127) {
            return $ord0;
        }

        $ord1 = ord($chr[1]);
        if ($ord0 >= 192 && $ord0 <= 223)  {
            return ($ord0 - 192) * 64 + ($ord1 - 128);
        }

        $ord2 = ord($chr[2]);
        if ($ord0 >= 224 && $ord0 <= 239) {
            return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        }

        $ord3 = ord($chr[3]);
        if ($ord0 >= 240 && $ord0 <= 247) {
            return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        }

        return null;
    }

    /**
     * Unicode chr (@source https://stackoverflow.com/a/7153133/362780).
     *
     * @param  int $ord
     * @return string|null
     * @since  6.0
     */
    public static function chr(int $ord): string|null
    {
        if ($ord <= 127) {
            return chr($ord);
        }
        if ($ord <= 2047) {
            return chr(($ord >> 6) + 192) . chr(($ord & 63) + 128);
        }
        if ($ord <= 65535) {
            return chr(($ord >> 12) + 224) . chr((($ord >> 6) & 63) + 128) . chr(($ord & 63) + 128);
        }
        if ($ord <= 2097151) {
            return chr(($ord >> 18) + 240) . chr((($ord >> 12) & 63) + 128) . chr((($ord >> 6) & 63) + 128) . chr(($ord & 63) + 128);
        }

        return null;
    }
}
