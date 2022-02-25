<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Strings;

/**
 * The ever most wanted functions (finally come with 8.0, but without case option).
 * @alias str_has(),str_has_prefix(),str_has_suffix()
 * @since 4.0
 */
function strsrc(...$args) { return str_has(...$args);         } // Search.
function strpfx(...$args) { return str_has_prefix(...$args);  } // Search prefix.
function strsfx(...$args) { return str_has_suffix(...$args);  } // Search suffix.

/**
 * Shorter stuff in multi-byte style.
 * @since 3.0, 5.0
 */
function upper(string $in): string { return mb_strtoupper($in); }
function lower(string $in): string { return mb_strtolower($in); }

/**
 * Sub a string with given start/length in multi-byte style.
 *
 * @param  string   $str
 * @param  int      $start
 * @param  int|null $length
 * @return string
 * @since  4.0
 */
function strsub(string $str, int $start, int $length = null): string
{
    return mb_substr($str, $start, $length);
}

/**
 * Cut a string with given length in multi-byte style.
 *
 * @param  string $str
 * @param  int    $length
 * @return string
 * @since  4.0
 */
function strcut(string $str, int $length): string
{
    return ($length >= 0) ? mb_substr($str, 0, $length) : mb_substr($str, $length);
}

/**
 * Cut a string before given search position with/without given length, or return '' if no search found.
 *
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  int|null $offset
 * @param  bool     $icase
 * @return string
 * @since  4.0
 */
function strbcut(string $str, string $src, int $length = null, int $offset = null, bool $icase = false): string
{
    $pos = !$icase ? mb_strpos($str, $src, $offset ?? 0) : mb_stripos($str, $src, $offset ?? 0);

    if ($pos !== false) {
        $ret = mb_substr($str, 0, $pos); // Before (b).
        return $length ? strcut($ret, $length) : $ret;
    }

    return ''; // Not found.
}

/**
 * Cut a string after given search position with/without given length, or return '' if no search found.
 *
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  int|null $offset
 * @param  bool     $icase
 * @return string
 * @since  4.0
 */
function stracut(string $str, string $src, int $length = null, int $offset = null, bool $icase = false): string
{
    $pos = !$icase ? mb_strpos($str, $src, $offset ?? 0) : mb_stripos($str, $src, $offset ?? 0);

    if ($pos !== false) {
        $ret = mb_substr($str, $pos + mb_strlen($src)); // After (a).
        return $length ? strcut($ret, $length) : $ret;
    }

    return ''; // Not found.
}

/**
 * Check whether a string has given search part or not with case-intensive option.
 *
 * @param  string $str
 * @param  string $src
 * @param  bool   $icase
 * @return bool
 * @since  4.0
 */
function str_has(string $str, string $src, bool $icase = false): bool
{
    return !$icase ? str_contains($str, $src) : mb_stripos($str, $src) !== false;
}

/**
 * Check whether a string has given prefix or not with case-intensive option.
 *
 * @param  string $str
 * @param  string $src
 * @param  bool   $icase
 * @return bool
 * @since  4.0
 */
function str_has_prefix(string $str, string $src, bool $icase = false): bool
{
    return !$icase ? str_starts_with($str, $src) : mb_stripos($str, $src) === 0;
}

/**
 * Check whether a string has given suffix or not with case-intensive option.
 *
 * @param  string $str
 * @param  string $src
 * @param  bool   $icase
 * @return bool
 * @since  4.0
 */
function str_has_suffix(string $str, string $src, bool $icase = false): bool
{
    return !$icase ? str_ends_with($str, $src) : mb_strripos($str, $src) === mb_strlen($str) - mb_strlen($src);
}

/**
 * Randomize given string, return a subpart when length given.
 *
 * @param  string   $str
 * @param  int|null $length
 * @return string
 * @since  4.9
 */
function str_rand(string $str, int $length = null): string
{
    $tmp = array_shuffle(mb_str_split($str), false);

    if ($length) {
        $tmp = array_slice($tmp, 0, abs($length));
    }

    return join($tmp);
}

/**
 * Chunk given string properly in multi-byte style.
 *
 * @param  string $str
 * @param  int    $length
 * @param  string $separator
 * @param  bool   $join
 * @return string|array
 * @since  5.31
 */
function str_chunk(string $str, int $length = 76, string $separator = "\r\n", bool $join = true): string|array
{
    $ret = array_chunk(mb_str_split($str), abs($length));

    if ($join) {
        $ret = array_reduce($ret, fn($s, $ss) => $s .= join($ss) . $separator);
    }

    return $ret;
}

/**
 * Concat given string with others.
 *
 * @param  string    $str
 * @param  mixed  ...$strs
 * @return string
 * @since  5.31
 */
function str_concat(string $str, mixed ...$strs): string
{
    if (!$strs) {
        return $str;
    }

    return $str . join($strs);
}

/**
 * Compare two string inputs.
 *
 * @param  string      $str1
 * @param  string      $str2
 * @param  bool        $icase
 * @param  int|null    $length
 * @param  string|null $locale
 * @param  string|null $encoding
 * @return int
 * @since  5.26
 */
function str_compare(string $str1, string $str2, bool $icase = false, int $length = null, string $locale = null, string $encoding = null): int
{
    return ($locale !== null)
         ? Strings::compareLocale($str1, $str2, $locale)
         : Strings::compare($str1, $str2, $icase, $length, $encoding);
}

/**
 * Convert a multi-byte string's first character to upper-case.
 *
 * @param  string      $str
 * @param  bool        $tr
 * @param  string|null $encoding
 * @return string
 * @since  5.0
 */
function mb_ucfirst(string $str, bool $tr = false, string $encoding = null): string
{
    if ($str == '') {
        return $str;
    }

    $first = mb_substr($str, 0, 1, $encoding);
    if ($tr && $first == 'i') {
        $first = 'İ';
    }

    return mb_strtoupper($first, $encoding) . mb_substr($str, 1, null, $encoding);
}

/**
 * Convert a multi-byte string's first character to lower-case.
 *
 * @param  string      $str
 * @param  bool        $tr
 * @param  string|null $encoding
 * @return string
 * @since  5.0
 */
function mb_lcfirst(string $str, bool $tr = false, string $encoding = null): string
{
    if ($str == '') {
        return $str;
    }

    $first = mb_substr($str, 0, 1, $encoding);
    if ($tr && $first == 'I') {
        $first = 'ı';
    }

    return mb_strtolower($first, $encoding) . mb_substr($str, 1, null, $encoding);
}

/**
 * Reverse a multi-byte string.
 *
 * @param  string      $str
 * @param  string|null $encoding
 * @return string
 * @since  6.0
 */
function mb_strrev(string $str, string $encoding = null): string
{
    if ($str == '') {
        return $str;
    }

    return join(array_reverse(mb_str_split($str, 1, $encoding)));
}

/**
 * Get a character by given index in multi-byte style.
 *
 * @param  string      $str
 * @param  int         $index
 * @param  string|null $encoding
 * @return string|null
 * @since  5.17
 */
function char_at(string $str, int $index, string $encoding = null): string|null
{
    if ($index < 0 && -$index > mb_strlen($str, $encoding)) {
        return null;
    }

    $char = mb_substr($str, $index, 1, $encoding);

    return ($char != '') ? $char : null;
}

/**
 * Get a character code by given index in multi-byte style.
 *
 * @param  string      $str
 * @param  int         $index
 * @param  string|null $encoding
 * @return int|null
 * @since  5.17
 */
function char_code_at(string $str, int $index, string $encoding = null): int|null
{
    if ($index < 0 && -$index > mb_strlen($str, $encoding)) {
        return null;
    }

    $char = mb_substr($str, $index, 1, $encoding);

    return ($char != '') ? mb_ord($char, $encoding) : null;
}
