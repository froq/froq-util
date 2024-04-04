<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\Strings;

/**
 * Most wanted functions (finally come with 8.0, but without case option).
 *
 * @alias str_has(),str_has_prefix(),str_has_suffix()
 * @since 4.0
 */
function strsrc(...$args): bool { return str_has(...$args);         } // Search.
function strpfx(...$args): bool { return str_has_prefix(...$args);  } // Search prefix.
function strsfx(...$args): bool { return str_has_suffix(...$args);  } // Search suffix.

/**
 * Shorter case converters in multi-byte style.
 *
 * @alias mb_strtoupper(),mb_strtolower()
 * @since 3.0, 5.0
 */
function upper(string $string): string { return mb_strtoupper($string); }
function lower(string $string): string { return mb_strtolower($string); }

/**
 * Substring with given start/length in multi-byte style.
 *
 * @param  string   $string
 * @param  int      $start
 * @param  int|null $length
 * @return string
 * @since  4.0
 */
function strsub(string $string, int $start, int $length = null): string
{
    return mb_substr($string, $start, $length);
}

/**
 * Cut a string with given length in multi-byte style.
 *
 * @param  string $string
 * @param  int    $length
 * @return string
 * @since  4.0
 */
function strcut(string $string, int $length): string
{
    return ($length >= 0) ? mb_substr($string, 0, $length) : mb_substr($string, $length);
}

/**
 * Cut a string before given search position with/without given length, or return '' if no search found.
 *
 * @param  string   $string
 * @param  string   $search
 * @param  int|null $length
 * @param  bool     $icase
 * @param  int      $offset
 * @return string
 * @since  4.0
 */
function strbcut(string $string, string $search, int $length = null, bool $icase = false, int $offset = 0): string
{
    $pos = !$icase ? mb_strpos($string, $search, $offset) : mb_stripos($string, $search, $offset);

    if ($pos !== false) {
        $ret = mb_substr($string, 0, $pos);
        return $length ? strcut($ret, $length) : $ret;
    }

    return ''; // Not found.
}

/**
 * Cut a string after given search position with/without given length, or return '' if no search found.
 *
 * @param  string   $string
 * @param  string   $search
 * @param  int|null $length
 * @param  int      $offset
 * @param  bool     $icase
 * @return string
 * @since  4.0
 */
function stracut(string $string, string $search, int $length = null, bool $icase = false, int $offset = 0): string
{
    $pos = !$icase ? mb_strpos($string, $search, $offset) : mb_stripos($string, $search, $offset);

    if ($pos !== false) {
        $ret = mb_substr($string, $pos + mb_strlen($search));
        return $length ? strcut($ret, $length) : $ret;
    }

    return ''; // Not found.
}

/**
 * Check whether a string has given search part or not with case-intensive option.
 *
 * @param  string               $string
 * @param  string|array<string> $search
 * @param  bool                 $icase
 * @return bool
 * @since  4.0
 */
function str_has(string $string, string|array $search, bool $icase = false): bool
{
    // Multi search.
    if (is_array($search)) {
        foreach ($search as $search) {
            if (str_has($string, (string) $search, $icase)) {
                return true;
            }
        }
        return false;
    }

    return !$icase ? str_contains($string, $search) : (
        mb_stripos($string, $search) !== false
    );
}

/**
 * Check whether a string has given prefix or not with case-intensive option.
 *
 * @param  string               $string
 * @param  string|array<string> $search
 * @param  bool                 $icase
 * @return bool
 * @since  4.0
 */
function str_has_prefix(string $string, string|array $search, bool $icase = false): bool
{
    // Multi search.
    if (is_array($search)) {
        foreach ($search as $search) {
            if (str_has_prefix($string, (string) $search, $icase)) {
                return true;
            }
        }
        return false;
    }

    return !$icase ? str_starts_with($string, $search) : (
        mb_stripos($string, $search) === 0
    );
}

/**
 * Check whether a string has given suffix or not with case-intensive option.
 *
 * @param  string               $string
 * @param  string|array<string> $search
 * @param  bool                 $icase
 * @return bool
 * @since  4.0
 */
function str_has_suffix(string $string, string|array $search, bool $icase = false): bool
{
    // Multi search.
    if (is_array($search)) {
        foreach ($search as $search) {
            if (str_has_suffix($string, (string) $search, $icase)) {
                return true;
            }
        }
        return false;
    }

    return !$icase ? str_ends_with($string, $search) : (
        mb_strripos($string, $search) === mb_strlen($string) - mb_strlen($search)
    );
}

/**
 * Ensure string starts with given prefix, prepend if not.
 *
 * @param  string $string
 * @param  string $prefix
 * @return string
 * @since  7.11
 */
function str_prefix(string $string, string $prefix): string
{
    return str_starts_with($string, $prefix) ? $string : $prefix . $string;
}

/**
 * Ensure string ends with given suffix, append if not.
 *
 * @param  string $string
 * @param  string $suffix
 * @return string
 * @since  7.11
 */
function str_suffix(string $string, string $suffix): string
{
    return str_ends_with($string, $suffix) ? $string : $string . $suffix;
}

/**
 * Test given string with given pattern.
 *
 * @param  string      $string
 * @param  string      $pattern
 * @param  string|null $modifiers
 * @return bool
 */
function str_test(string $string, string $pattern, string $modifiers = null): bool
{
    return ($string === '' || $pattern === '') ? false
         : preg_test(sprintf('~%s~%s', addcslashes($pattern, '~'), $modifiers), $string);
}

/**
 * Randomize given string, return a subpart when length given.
 *
 * @param  string   $string
 * @param  int|null $length
 * @return string
 * @since  4.9
 */
function str_rand(string $string, int $length = null): string
{
    return mb_substr(mb_str_shuffle($string), 0, $length);
}

/**
 * Chunk given string properly in multi-byte style.
 *
 * @param  string       $string
 * @param  int          $length
 * @param  string|false $separator
 * @param  bool         $join
 * @param  bool         $chop
 * @return string|array
 * @since  5.31
 * @throws ArgumentError When length is less than 1.
 */
function str_chunk(string $string, int $length = 76, string|false $separator = "\r\n", bool $join = true,
    bool $chop = false): string|array
{
    if ($length < 1) {
        throw new ArgumentError('Invalid length %s [min=1]', $length);
    }

    $ret = array_chunk(mb_str_split($string), $length);

    if ($join) {
        $ret = array_map('join', $ret);

        // Join chunks too.
        if ($separator !== false) {
            $ret = join($separator, $ret);
            $chop || ($ret .= $separator);
        }
    }

    return $ret;
}

/**
 * Concat given string with others.
 *
 * @param  string               $string
 * @param  string|Stringable ...$strings
 * @return string
 * @since  5.31
 */
function str_concat(string $string, string|Stringable ...$strings): string
{
    return $string . join($strings);
}

/**
 * Reverse given string, with multi-byte option.
 *
 * @param  string           $string
 * @param  string|bool|null $encoding True or encoding for multi-byte.
 * @return string
 * @since  7.12
 */
function str_reverse(string $string, string|bool $encoding = null): string
{
    return (func_num_args() === 1 || $encoding === false) // No multi-byte directive.
         ? strrev($string) : mb_strrev($string, $encoding !== true ? $encoding : null);
}

/**
 * Slice a string with multiple functionalities, before/after or simply do substring work, like `strstr()`
 * but dropping search character from return.
 *
 * Examples:
 * ```
 * str_slice('Lorem', [0, 2])              => 'Lo'
 * str_slice('Lorem', range: [0, 2])       => 'Lo'
 * str_slice('Lorem', 'o', true)           => 'L'
 * str_slice('Lorem', 'o', false)          => 'rem'
 * str_slice('Lorem', 'o', true, [1, -1])  => 'ore'
 * str_slice('Lorem', 'o', false, [1, -1]) => 'ore'
 * ```
 *
 * @param  string                $string
 * @param  string|array|null     $search_or_range Search string or start-end range.
 * @param  string|bool|null      $before_or_after Works if $search_or_range given (before|true, after|false, default=after).
 * @param  int|array<int>|null   $range           Start-end range or just start offset.
 * @param  bool                  $icase
 * @param  int                   $offset
 * @return string
 */
function str_slice(string $string, string|array $search_or_range = null, string|bool $before_or_after = null,
    int|array $range = null, bool $icase = false, int $offset = 0): string
{
    // Override $range when $search_or_range given as array,
    // and set $search_or_range as NULL.
    if ($search_or_range !== null || $range !== null) {
        // Range given, swap & nullify.
        if (is_array($search_or_range)) {
            [$range, $search_or_range] = [$search_or_range, null];
        }

        [$start, $end] = pad((array) $range, 2);

        if ($start !== null) {
            return strsub($string, $start, $end);
        }

        if ($search_or_range !== null) {
            return match ($before_or_after ?? false) {
                true, 'before' => strbcut($string, $search_or_range, null, $icase, $offset),
                false, 'after' => stracut($string, $search_or_range, null, $icase, $offset),
                default        => '' // Invalid directive.
            };
        }
    }

    return $string; // No-op.
}

/**
 * Splice a string, return modified string, and optionally fill `$replaced` argument if `$replace` argument given.
 *
 * Examples:
 * ```
 * str_splice('Lorem', 1, 1)             => 'Lrem'
 * str_splice('Lorem', 1, 1, 'x')        => 'Lxrem'
 * str_splice('Lorem', 1, 1, ['x', 'y']) => 'Lxyrem'
 * ```
 *
 * @param  string             $string
 * @param  int                $start
 * @param  int|null           $end
 * @param  string|array|null  $replace
 * @param  string|array|null &$replaced
 * @return string
 * @since  7.13
 */
function str_splice(string $string, int $start, int $end = null, string|array $replace = null, string|array &$replaced = null): string
{
    $charlist = mb_str_split($string);
    $replaced = array_splice($charlist, $start, $end, (array) $replace);

    return join($charlist);
}

/**
 * Scan given string in a safe way.
 *
 * @param  string    $string
 * @param  string    $format
 * @param  mixed ...&$vars
 * @return int|array
 */
function str_scan(string $string, string $format, mixed &...$vars): int|array
{
    $vars_count = $vars ? count($vars) : -1;
    $spec_count = preg_match_all('~(?<!%)%[a-zA-Z]~', $format);

    // ValueError: Variable is not assigned by any conversion specifiers.
    if ($vars_count > -1 && $vars_count > $spec_count) {
        $vars = array_slice($vars, 0, $spec_count);
    }
    // ValueError: Different numbers of variable names and field specifiers.
    elseif ($vars_count > 0 && $vars_count < $spec_count) {
        $vars = array_pad($vars, $spec_count, null);
    }

    return sscanf($string, $format, ...$vars);
}

/**
 * Apply word-count on given string in multi-byte style.
 *
 * @param  string $string
 * @param  int    $type
 * @return int|array
 */
function str_wordcount(string $string, int $type = 0): int|array
{
    $tmp = preg_split('~[^\p{L}\'\-]+~u', $string, flags: PREG_SPLIT_NO_EMPTY);

    return match ($type) {
        default => $tmp,
        0 => count($tmp),
        // Map of words' count.
        1 => reduce($tmp, function(?array $ret, string $word): ?array {
            $ret[$word] ??= 0;
            $ret[$word] += 1;
            return $ret;
        }) ?: [],
    };
}

/**
 * Apply word-wrap on given string in multi-byte style.
 *
 * @param  string $string
 * @param  int    $width
 * @param  string $break
 * @param  bool   $cut
 * @return string
 * @since  6.0
 */
function str_wordwrap(string $string, int $width = 75, string $break = "\n", bool $cut = false): string
{
    if (!$cut) {
        return wordwrap($string, $width, $break);
    }

    /** @thanks http://php.net/wordwrap#107570 */
    return trim(preg_replace(
        '~(.{1,' . $width . '})(?:\s|$)|(.{' . $width . '})~uS',
        '\1\2' . $break,
        $string
    ), $break);
}

/**
 * Compare two string inputs.
 *
 * @param  string      $string1
 * @param  string      $string2
 * @param  bool        $icase
 * @param  int|null    $length
 * @param  string|null $locale
 * @param  string|null $encoding
 * @return int
 * @since  5.26
 */
function str_compare(string $string1, string $string2, bool $icase = false, int $length = null, string $locale = null,
    string $encoding = null): int
{
    return ($locale !== null)
         ? Strings::compareLocale($string1, $string2, $locale)
         : Strings::compare($string1, $string2, $icase, $length, $encoding);
}

/**
 * Remove search part(s) in given string (just to get rid of str_replace(), yes..).
 *
 * @param  string    $string
 * @param  array     $search
 * @param  int|null &$count
 * @return string
 * @since  7.4
 */
function str_remove(string $string, string|array $search, int &$count = null): string
{
    return str_replace($search, '', $string, $count);
}

/**
 * Pop a string if it contains any given separator, like explode() but with multi separator
 * and a fixing limit options.
 *
 * @param  string               $string
 * @param  string|array<string> $separator
 * @param  int|null             $limit
 * @return array
 * @since  7.20
 */
function str_pop(string $string, string|array $separator, int $limit = null): array
{
    $ret = [];

    if ($separator !== '' && $separator !== []) {
        foreach ((array) $separator as $search) {
            if (str_contains($string, (string) $search)) {
                $ret = explode($search, $string, $limit ?? PHP_INT_MAX);
                break;
            }
        }
    }

    if ($string !== '' && !$ret) {
        $ret = [$string];
    }

    // Prevent "undefined index" error.
    if ($limit && $limit > count($ret)) {
        $ret = array_pad($ret, $limit, null);
    }

    return $ret;
}

/**
 * Upperize given string (with multi-byte & Turkish characters support).
 *
 * @param  string           $string
 * @param  bool             $tr
 * @param  string|bool|null $encoding
 * @return string
 * @since  7.4
 */
function str_upper(string $string, bool $tr = false, string|bool $encoding = null): string
{
    if ($string === '') {
        return '';
    }

    // Damageable Turkish characters.
    if ($tr) {
        $string = str_replace(['ı', 'i'], ['I', 'İ'], $string);
        $encoding = ENCODING;
    }

    return $encoding ? mb_strtoupper($string, $encoding !== true ? $encoding : null) : strtoupper($string);
}

/**
 * Lowerize given string (with multi-byte & Turkish characters support).
 *
 * @param  string           $string
 * @param  bool             $tr
 * @param  string|bool|null $encoding
 * @return string
 * @since  7.4
 */
function str_lower(string $string, bool $tr = false, string|bool $encoding = null): string
{
    if ($string === '') {
        return '';
    }

    // Damageable Turkish characters.
    if ($tr) {
        $string = str_replace(['I', 'İ'], ['ı', 'i'], $string);
        $encoding = ENCODING;
    }

    return $encoding ? mb_strtolower($string, $encoding !== true ? $encoding : null) : strtolower($string);
}

/**
 * Titleize given string (with multi-byte & Turkish characters support).
 *
 * @param  string      $string
 * @param  bool        $tr
 * @param  string|null $encoding
 * @return string
 * @since  7.4
 */
function str_title(string $string, bool $tr = false, string $encoding = null): string
{
    if ($string === '') {
        return '';
    }

    if (!$tr) {
        return mb_convert_case($string, MB_CASE_TITLE_SIMPLE, $encoding);
    }

    // Fix damageable Turkish characters first.
    $string = str_replace(['I', 'İ'], ['ı', 'i'], $string);

    // Then lowerize all as expected.
    $string = mb_strtolower($string, ENCODING);

    foreach (explode(' ', $string) as $part) {
        $parts[] = mb_ucfirst($part, true, ENCODING);
    }

    return implode(' ', $parts);
}

/**
 * Check if given string is empty after trimming it (for lazy devs).
 *
 * @param  string $string
 * @return bool
 * @since  7.6
 */
function str_empty(string $string): bool
{
    // Trim " " and "\t" only (no NULL-bytes trimmed).
    return trim($string, "\x20\x09") === '';
}

/**
 * Convert first character of given string to upper-case in multi-byte style.
 *
 * @param  string      $string
 * @param  bool        $tr
 * @param  string|null $encoding
 * @return string
 * @since  5.0
 */
function mb_ucfirst(string $string, bool $tr = false, string $encoding = null): string
{
    if ($string === '') {
        return '';
    }

    $first = mb_substr($string, 0, 1, $encoding);
    if ($tr) {
        $encoding = ENCODING;
        if ($first === 'i') {
            $first = 'İ';
        }
    }

    return mb_strtoupper($first, $encoding) . mb_substr($string, 1, null, $encoding);
}

/**
 * Convert first character of given string to lower-case in multi-byte style.
 *
 * @param  string      $string
 * @param  bool        $tr
 * @param  string|null $encoding
 * @return string
 * @since  5.0
 */
function mb_lcfirst(string $string, bool $tr = false, string $encoding = null): string
{
    if ($string === '') {
        return '';
    }

    $first = mb_substr($string, 0, 1, $encoding);
    if ($tr) {
        $encoding = ENCODING;
        if ($first === 'I') {
            $first = 'ı';
        }
    }

    return mb_strtolower($first, $encoding) . mb_substr($string, 1, null, $encoding);
}

/**
 * Reverse given string in multi-byte style.
 *
 * @param  string      $string
 * @param  string|null $encoding
 * @return string
 * @since  6.0
 */
function mb_strrev(string $string, string $encoding = null): string
{
    if ($string === '') {
        return '';
    }

    return join(array_reverse(mb_str_split($string, 1, $encoding)));
}

/**
 * Pad given string in multi-byte style.
 *
 * @param  string      $string
 * @param  int         $pad_length
 * @param  string      $pad_string
 * @param  int         $pad_type
 * @param  string|null $encoding
 * @return string
 * @since  6.0
 */
function mb_str_pad(string $string, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT,
    string $encoding = null): string
{
    return Strings::pad($string, $pad_length, $pad_string, $pad_type, $encoding);
}

/**
 * Shuffle given string in multi-byte style.
 *
 * @param  string      $string
 * @param  string|null $encoding
 * @return string
 * @since  7.11
 */
function mb_str_shuffle(string $string, string $encoding = null): string
{
    return join(array_shuffle(
        mb_str_split($string, 1, $encoding),
        assoc: false
    ));
}

/**
 * Get a character by given index in multi-byte style.
 *
 * @param  string      $string
 * @param  int         $index
 * @param  string|null $encoding
 * @return string|null
 * @since  5.17
 */
function char_at(string $string, int $index, string $encoding = null): string|null
{
    if ($index < 0 && -$index > mb_strlen($string, $encoding)) {
        return null;
    }

    $char = mb_substr($string, $index, 1, $encoding);

    return ($char !== '') ? $char : null;
}

/**
 * Get a character code by given index in multi-byte style.
 *
 * @param  string      $string
 * @param  int         $index
 * @param  string|null $encoding
 * @return int|null
 * @since  5.17
 */
function char_code_at(string $string, int $index, string $encoding = null): int|null
{
    if ($index < 0 && -$index > mb_strlen($string, $encoding)) {
        return null;
    }

    $char = mb_substr($string, $index, 1, $encoding);

    return ($char !== '') ? mb_ord($char, $encoding) : null;
}

/**
 * As an ally of in_array() function.
 *
 * @param  string|array<string> $search
 * @param  string               $string
 * @param  bool                 $icase
 * @param  string|null          $side One of: start, prefix, left or end, suffix, right.
 * @return bool
 * @since  7.15
 * @throws ArgumentError
 */
function in_string(string|array $search, string $string, bool $icase = false, string $side = null): bool
{
    return match ($side) {
        '', null
            => str_has($string, $search, $icase),
        'start', 'prefix', 'left'
            => str_has_prefix($string, $search, $icase),
        'end', 'suffix', 'right'
            => str_has_suffix($string, $search, $icase),

        default => throw new ArgumentError('Invalid side %q', $side)
    };
}
