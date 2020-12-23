<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\Strings;

/**
 * Compare two strings, optionally by a locale.
 *
 * @param  string      $in1
 * @param  string      $in2
 * @param  string|null $locale
 * @return int
 * @since  5.0
 */
function string_compare(string $in1, string $in2, string $locale = null): int
{
    return is_null($locale) ? Strings::compare($in1, $in2)
                            : Strings::compareLocale($in1, $in2, $locale);
}

/**
 * Check whether given input contains given search.
 *
 * @param  string $in
 * @param  string $src
 * @param  bool   $icase
 * @return bool
 * @since  3.0
 */
function string_contains(string $in, string $src, bool $icase = false): bool
{
    return Strings::contains($in, $src, $icase);
}

/**
 * Check whether given input contains any of given searches.
 *
 * @param  string $in
 * @param  array  $srcs
 * @param  bool   $icase
 * @return bool
 * @since  3.0
 */
function string_contains_any(string $in, array $srcs, bool $icase = false): bool
{
    return Strings::containsAny($in, $srcs, $icase);
}

/**
 * Check whether given input contains all given search.
 *
 * @param  string $in
 * @param  array  $srcs
 * @param  bool   $icase
 * @return bool
 * @since  3.0
 */
function string_contains_all(string $in, array $srcs, bool $icase = false): bool
{
    return Strings::containsAll($in, $srcs, $icase);
}

/**
 * Check whether given input starts with given search/searches.
 *
 * @param  string               $in
 * @param  string|array<string> $src
 * @return bool
 * @since  3.0
 */
function string_starts_with(string $in, string|array $src, bool $icase = false, bool $mbyte = false): bool
{
    return is_string($src) ? Strings::startsWith($in, $src, $icase, $mbyte)
                           : Strings::startsWithAny($in, $src, $icase, $mbyte);
}

/**
 * Check whether given input ends with given search/searches.
 *
 * @param  string               $in
 * @param  string|array<string> $src
 * @return bool
 * @since  3.0
 */
function string_ends_with(string $in, string|array $src, bool $icase = false, bool $mbyte = false): bool
{
    return is_string($src) ? Strings::endsWith($in, $src, $icase, $mbyte)
                           : Strings::endsWithAny($in, $src, $icase, $mbyte);
}

/**
 * Check whether given input encoding is UTF.
 *
 * @param  string $in
 * @param  int    $bits
 * @return bool
 * @since  4.0
 */
function is_utf_string(string $in, int $bits = 8): bool
{
    return Strings::isUtf($in, $bits);
}

/**
 * Check whether given input encoding is ASCII.
 *
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_ascii_string(string $in): bool
{
    return Strings::isAscii($in);
}

/**
 * Check whether given input contains binary.
 *
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_binary_string(string $in): bool
{
    return Strings::isBinary($in);
}

/**
 * Check whether given input is base64-ed.
 *
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_base64_string(string $in): bool
{
    return Strings::isBase64($in);
}
