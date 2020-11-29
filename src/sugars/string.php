<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\Strings;

/**
 * String contains.
 * @param  string $in
 * @param  string $search
 * @param  bool   $icase
 * @return bool
 * @since  3.0
 */
function string_contains(string $in, string $search, bool $icase = false): bool
{
    return Strings::contains($in, $search, $icase);
}

/**
 * String contains any.
 * @param  string $in
 * @param  array  $search
 * @param  bool   $icase
 * @return bool
 * @since  3.0
 */
function string_contains_any(string $in, array $searches, bool $icase = false): bool
{
    return Strings::containsAny($in, $searches, $icase);
}

/**
 * String contains all.
 * @param  string $in
 * @param  array  $search
 * @param  bool   $icase
 * @return bool
 * @since  3.0
 */
function string_contains_all(string $in, array $searches, bool $icase = false): bool
{
    return Strings::containsAll($in, $searches, $icase);
}

/**
 * String starts with.
 * @param  string               $in
 * @param  string|array<string> $search
 * @return bool
 * @since  3.0
 */
function string_starts_with(string $in, $search, bool $icase = false, bool $mbyte = false): bool
{
    return is_array($search) ? Strings::startsWithAny($in, $search, $icase, $mbyte)
                             : Strings::startsWith($in, $search, $icase, $mbyte);
}

/**
 * String ends with.
 * @param  string               $in
 * @param  string|array<string> $search
 * @return bool
 * @since  3.0
 */
function string_ends_with(string $in, $search, bool $icase = false, bool $mbyte = false): bool
{
    return is_array($search) ? Strings::endsWithAny($in, $search, $icase, $mbyte)
                             : Strings::endsWith($in, $search, $icase, $mbyte);
}

/**
 * Is utf.
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
 * Is ascii.
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_ascii_string(string $in): bool
{
    return Strings::isAscii($in);
}

/**
 * Is binary.
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_binary_string(string $in): bool
{
    return Strings::isBinary($in);
}

/**
 * Is base64.
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_base64_string(string $in): bool
{
    return Strings::isBase64($in);
}
