<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

use froq\util\Strings;

/**
 * Compare two strings, optionally by a locale.
 *
 * @param  string      $string1
 * @param  string      $string2
 * @param  string|null $locale
 * @param  bool        $icase
 * @return int
 * @since  5.0
 */
function string_compare(string $string1, string $string2, string $locale = null, bool $icase = false): int
{
    return is_null($locale) ? Strings::compare($string1, $string2, $icase)
                            : Strings::compareLocale($string1, $string2, $locale);
}

/**
 * Check whether given string contains given search.
 *
 * @param  string               $string
 * @param  string|array<string> $search
 * @param  bool                 $icase
 * @return bool
 * @since  3.0
 */
function string_contains(string $string, string|array $search, bool $icase = false): bool
{
    return is_string($search) ? Strings::contains($string, $search, $icase)
                              : Strings::containsAny($string, $search, $icase);
}

/**
 * Check whether given string starts with given search/searches.
 *
 * @param  string               $string
 * @param  string|array<string> $src
 * @return bool
 * @since  3.0
 */
function string_starts_with(string $string, string|array $src, bool $icase = false, bool $mbyte = false): bool
{
    return is_string($src) ? Strings::startsWith($string, $src, $icase, $mbyte)
                           : Strings::startsWithAny($string, $src, $icase, $mbyte);
}

/**
 * Check whether given string ends with given search/searches.
 *
 * @param  string               $string
 * @param  string|array<string> $src
 * @return bool
 * @since  3.0
 */
function string_ends_with(string $string, string|array $src, bool $icase = false, bool $mbyte = false): bool
{
    return is_string($src) ? Strings::endsWith($string, $src, $icase, $mbyte)
                           : Strings::endsWithAny($string, $src, $icase, $mbyte);
}

/**
 * Check whether given string encoding is UTF.
 *
 * @param  string $string
 * @param  int    $bits
 * @return bool
 * @since  4.0
 */
function is_utf_string(string $string, int $bits = 8): bool
{
    return Strings::isUtf($string, $bits);
}

/**
 * Check whether given string encoding is ASCII.
 *
 * @param  string $string
 * @return bool
 * @since  4.0
 */
function is_ascii_string(string $string): bool
{
    return Strings::isAscii($string);
}

/**
 * Check whether given string contains binary.
 *
 * @param  string $string
 * @return bool
 * @since  4.0
 */
function is_binary_string(string $string): bool
{
    return Strings::isBinary($string);
}

/**
 * Check whether given string is base64-ed.
 *
 * @param  string $string
 * @return bool
 * @since  4.0
 */
function is_base64_string(string $string): bool
{
    return Strings::isBase64($string);
}
