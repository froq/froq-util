<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\{Util, Arrays, Objects, Numbers, Strings};
use froq\util\misc\System;

// Load consts & classes.
require 'sugars-const.php';
require 'sugars-class.php';

/**
 * Sugar loader.
 */
function sugar(string|array $name): void {
    Util::loadSugar($name);
}

/**
 * Yes man..
 */
function equal($a, $b, ...$c): bool {
    return ($a == $b) || ($c && in_array($a, [$b, ...$c]));
}
function equals($a, $b, ...$c): bool {
    return ($a === $b) || ($c && in_array($a, [$b, ...$c], true));
}

/**
 * Pick/pluck.
 * Loving https://docs.zephir-lang.com/0.12/en/operators#fetch
 */
function pick(array &$array, int|string $key, &$value = null, bool $drop = false): bool {
    return ($value = array_pick($array, $key, drop: $drop)) !== null;
}
function pluck(array &$array, int|string $key, &$value = null): bool {
    return ($value = array_pluck($array, $key)) !== null;
}

/**
 * Fetch/select.
 * @alias of array_fetch(),array_select()
 */
function fetch(array &$array, ...$args) { return array_fetch($array, ...$args); }
function select(array &$array, ...$args) { return array_select($array, ...$args); }

/**
 * The ever most wanted functions (finally come with 8.0, but without case option).
 * @alias of str_has(),str_has_prefix(),str_has_suffix()
 * @since 4.0
 */
function strsrc(...$args) { return str_has(...$args);         } // Search.
function strpfx(...$args) { return str_has_prefix(...$args);  } // Search prefix.
function strsfx(...$args) { return str_has_suffix(...$args);  } // Search suffix.

/**
 * Loving shorter stuffs?
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function upper(string $in): string { return mb_strtoupper($in); }
function lower(string $in): string { return mb_strtolower($in); }

/**
 * Quick array & object (with "x:1, y:2" support).
 */
function qa(...$args): array
{
    // When arguments are named.
    if (!is_list($args)) {
        return $args;
    }

    $ret = [];
    if ($argc = count($args)) {
        for ($i = 1; $i < $argc + 1; $i += 2) {
            $ret[$args[$i - 1]] = $args[$i];
        }
    }
    return $ret;
}
function qo(...$args): object
{
    return (object) qa(...$args);
}

/**
 * Filter an array with value/key notation.
 *
 * @param  array         $array
 * @param  callable|null $func
 * @param  bool          $keep_keys
 * @return array
 * @since  3.0, 5.0 Moved from common.inits.
 */
function filter(array $array, callable $func = null, bool $keep_keys = true): array
{
    return Arrays::filter($array, $func, $keep_keys);
}

/**
 * Map an array with value/key notation.
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $keep_keys
 * @return array
 * @since  3.0, 5.0 Moved from common.inits.
 */
function map(array $array, callable $func, bool $recursive = false, bool $keep_keys = true): array
{
    return Arrays::map($array, $func, $recursive, $keep_keys);
}

/**
 * Reduce an array with value/key notation.
 *
 * @param  array    $array
 * @param  any      $carry
 * @param  callable $func
 * @return any
 * @since  4.0, 5.0 Moved from common.inits.
 */
function reduce(array $array, $carry, callable $func)
{
    return !is_array($carry) ? Arrays::reduce($array, $carry, $func)
                             : Arrays::aggregate($array, $func, $carry);
}

/**
 * Each wrapper for scoped function calls on given array or just for syntactic sugar.
 *
 * @param  array    $array
 * @param  callable $func
 * @return void
 * @since  5.0
 */
function each(array $array, callable $func): void
{
    Arrays::each($array, $func);
}

/**
 * Get size (count/length) of given input.
 *
 * @param  string|array|object $in
 * @return int
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function size(string|array|object $in): int
{
    return match (true) {
        is_string($in)    => mb_strlen($in),
        is_countable($in) => count($in),
        is_object($in)    => count(get_object_vars($in))
    };
}

/**
 * Pad an array or string.
 *
 * @param  array|string $in
 * @param  int          $length
 * @param  mixed|null   $pad
 * @return array|string
 * @since  5.0
 */
function pad(array|string $in, int $length, mixed $pad = null): array|string
{
    return is_array($in) ? array_pad($in, $length, $pad)
         : str_pad($in, $length, pad_string: strval($pad ?? ' '));
}

/**
 * Chunk an array or string.
 *
 * @param  array|string $in
 * @param  int          $length
 * @param  bool         $keep_keys
 * @return array
 * @since  5.0
 */
function chunk(array|string $in, int $length, bool $keep_keys = false): array
{
    return is_array($in) ? array_chunk($in, $length, $keep_keys)
         : str_chunk($in, $length, join: false);
}

/**
 * Concat an array or string.
 *
 * @param  array|string    $in
 * @param  mixed        ...$ins
 * @return array|string
 * @since  4.0, 5.0 Moved from froq/fun.
 */
function concat(array|string $in, mixed ...$ins): array|string
{
    return is_array($in) ? array_concat($in, ...$ins)
         : str_concat($in, ...$ins);
}

/**
 * Slice an array or string.
 *
 * @param  array|string $in
 * @param  int          $start
 * @param  int|null     $end
 * @param  bool         $keep_keys
 * @return array|string
 * @since  3.0, 4.0 Added back, 5.0 Moved from froq/fun.
 */
function slice(array|string $in, int $start, int $end = null, bool $keep_keys = false): array|string
{
    return is_array($in) ? array_slice($in, $start, $end, $keep_keys)
         : mb_substr($in, $start, $end);
}

/**
 * Strip a string, with RegExp (~) option.
 *
 * @param  string      $in
 * @param  string|null $chars
 * @return string
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function strip(string $in, string $chars = null): string
{
    if ($chars === null) {
        return trim($in);
    } else {
        // RegExp: only ~..~ patterns accepted.
        if ($chars[0] == '~' && strlen($chars) >= 3) {
            $ruls = substr($chars, 1, ($pos = strrpos($chars, '~')) - 1);
            $mods = substr($chars, $pos + 1);
            return preg_replace(sprintf('~^%s|%s$~%s', $ruls, $ruls, $mods), '', $in);
        }
        return trim($in, $chars);
    }
}

/**
 * Split a string, with Unicode style.
 *
 * @param  string   $sep
 * @param  string   $in
 * @param  int|null $limit
 * @param  bool     $flags
 * @param  bool     $pad
 * @return array
 * @since  5.0 Moved from froq/fun.
 */
function split(string $sep, string $in, int $limit = null, int $flags = null, bool $pad = true): array
{
    if ($sep === '') {
        $ret = (array) preg_split('~~u', $in, -1, 1);
        if (!$pad && $limit) { // Like str_split(), but with Unicode.
            return array_map(fn($r) => join($r), array_chunk($ret, $limit));
        }
    } else {
        // Note: "[]" is special & used for charsets as separator (pattern), "." etc must be quoted also.
        // $ret = (array) ($limit ? mb_split($sep, $in, $limit) : mb_split($sep, $in));
        // if (!$blanks) { // When no blank fields wanted.
        //     $ret = array_filter($ret, 'strlen');
        // }

        // Escape null bytes (in case).
        $sep = str_replace("\0", "\\0", $sep);

        // Note: "~" must be escaped, no control here.
        $ret = preg_split('~'. $sep .'~u', $in, ($limit ?? -1), ($flags ?? PREG_SPLIT_NO_EMPTY));
    }

    // Plus: prevent 'undefined index ..' error.
    if ($pad && $limit && $limit != count($ret)) {
        $ret = array_pad($ret, $limit, null);
    }

    return $ret;
}

/**
 * Unsplit, a fun function.
 *
 * @param  string $sep
 * @param  array  $in
 * @return string
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function unsplit(string $sep, array $in): string
{
    return join($sep, $in);
}

/**
 * Grep, actually grabs something from given input.
 *
 * @param  string $in
 * @param  string $pattern
 * @param  bool   $named
 * @return string|array|null
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function grep(string $in, string $pattern, bool $named = false): string|array|null
{
    preg_match($pattern, $in, $match, PREG_UNMATCHED_AS_NULL);

    // For named capturing groups.
    if ($named && $match) {
        return array_filter($match, fn($k) => is_string($k), 2);
    }

    return $match[1] ?? null;
}

/**
 * Grep all, actually grabs somethings from given input.
 *
 * @param  string $in
 * @param  string $pattern
 * @param  bool   $named
 * @param  bool   $uniform
 * @return array<string|null>|null
 * @since  3.15, 5.0 Moved from froq/fun.
 */
function grep_all(string $in, string $pattern, bool $named = false, bool $uniform = false): array|null
{
    preg_match_all($pattern, $in, $matches, PREG_UNMATCHED_AS_NULL);

    if (isset($matches[1])) {
        unset($matches[0]); // Drop input.

        $ret = [];

        if (count($matches) == 1) {
            $ret = $matches[1];
        } else {
            $ret = array_map(fn($m) => count($m) == 1 ? $m[0] : $m, $matches);

            // Useful for in case '~href="(.+?)"|">(.+?)</~' etc.
            if ($uniform) {
                foreach ($ret as $i => &$re) {
                    if (is_array($re)) {
                        $re = array_filter($re, 'strlen');
                        if (count($re) == 1) {
                            $re = current($re);
                        }
                    }
                } unset($re);
            }

            // Maintain keys (so reset to 0-N).
            $ret = array_slice($ret, 0);
        }

        // For named capturing groups.
        if ($named && $ret) {
            return array_filter($ret, fn($k) => is_string($k), 2);
        }

        return $ret;
    }

    return null;
}

/**
 * Sub a string with given start/length in unicode manner.
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
 * Cut a string with given length in unicode manner.
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
        $cut = mb_substr($str, 0, $pos); // Before (b).
        return !$length ? $cut : strcut($cut, $length);
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
        $cut = mb_substr($str, $pos + mb_strlen($src)); // After (a).
        return !$length ? $cut : strcut($cut, $length);
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
 * Compare two string input by given or current locale.
 *
 * @param  string      $str1
 * @param  string      $str2
 * @param  string|null $locale
 * @return int
 * @since  5.26
 */
function str_compare(string $str1, string $str2, string $locale = null): int
{
    return Strings::compareLocale($str1, $str2, ($locale ?? getlocale(LC_COLLATE)));
}

/**
 * Randomize given string, return sub-part of when length given.
 *
 * @param  string   $str
 * @param  int|null $length
 * @return string|null
 * @since  4.9
 */
function str_rand(string $str, int $length = null): string|null
{
    if ($str == '') {
        trigger_error(sprintf('%s(): Empty string given', __function__));
        return null;
    }

    $str_length = mb_strlen($str);
    if ($length && ($length < 1 || $length > $str_length)) {
        trigger_error(sprintf('%s(): Length must be between 1-%s or null', __function__, $str_length));
        return null;
    }

    $tmp = preg_split('~~u', $str, -1, 1);

    srand(); // Ensure a new seed (@see https://wiki.php.net/rfc/object_scope_prng).
    shuffle($tmp);

    return !$length ? join($tmp) : join(array_slice($tmp, 0, $length));
}

/**
 * Chunk given string properly in unicode style.
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
    $ret = array_chunk(preg_split('~~u', $str, -1, 1), $length);

    return $join ? array_reduce($ret, fn($ret, $part) => $ret .= join($part) . $separator) : $ret;
}

/**
 * Concat given string with others.
 *
 * @param  string                     $str
 * @param  string|int|float|bool|null $items
 * @return string
 * @since  5.31
 */
function str_concat(string $str, string|int|float|bool|null ...$items): string
{
    return $str . join($items);
}

/**
 * Convert base (original source: http://stackoverflow.com/a/4668620/362780).
 *
 * @param  int|string $in    Digits to convert.
 * @param  int|string $from  From chars or base.
 * @param  int|string $to    To chars or base.
 * @return string|null
 * @since  4.0, 4.25 Derived from str_base_convert().
 */
function convert_base(int|string $in, int|string $from, int|string $to): string|null
{
    // Try to use speed/power of GMP.
    if (extension_loaded('gmp') && is_int($from) && is_int($to)) {
        return gmp_strval(gmp_init($in, $from), $to);
    }

    // Using base62 chars.
    $chars = BASE62_ALPHABET;

    if (is_int($from)) {
        if ($from < 2 || $from > 62) {
            trigger_error(sprintf('%s(): Invalid base for from chars, min=2 & max=62', __function__));
            return null;
        }
        $from = strcut($chars, $from);
    }
    if (is_int($to)) {
        if ($to < 2 || $to > 62) {
            trigger_error(sprintf('%s(): Invalid base for to chars, min=2 & max=62', __function__));
            return null;
        }
        $to = strcut($chars, $to);
    }

    $in = strval($in);
    if (!$in || $from == $to) {
        return $in;
    }

    [$in_length, $from_base_length, $to_base_length]
        = [strlen($in), strlen($from), strlen($to)];

    $numbers = [];
    for ($i = 0; $i < $in_length; $i++) {
        $numbers[$i] = strpos($from, $in[$i]);
    }

    $ret = '';
    $old_length = $in_length;

    do {
        $new_length = $div = 0;

        for ($i = 0; $i < $old_length; $i++) {
            $div = ($div * $from_base_length) + $numbers[$i];
            if ($div >= $to_base_length) {
                $numbers[$new_length++] = ($div / $to_base_length) | 0;
                $div = $div % $to_base_length;
            } elseif ($new_length > 0) {
                $numbers[$new_length++] = 0;
            }
        }

        $old_length = $new_length;

        $ret = $to[$div] . $ret;
    } while ($new_length != 0);

    return $ret;
}

/**
 * Convert case.
 *
 * @param  string      $in
 * @param  int         $case
 * @param  string|null $exploder
 * @param  string|null $imploder
 * @return string|null
 * @since  4.26
 */
function convert_case(string $in, int $case, string $exploder = null, string $imploder = null): string|null
{
    // Check valid cases.
    if (!in_array($case, [CASE_LOWER, CASE_UPPER, CASE_TITLE, CASE_DASH, CASE_SNAKE, CASE_CAMEL])) {
        trigger_error(sprintf('%s(): Invalid case %s, use a case from 0..5 range', __function__, $case));
        return null;
    }

    if ($case == CASE_LOWER) {
        return mb_strtolower($in);
    } elseif ($case == CASE_UPPER) {
        return mb_strtoupper($in);
    }

    // Set default split char.
    $exploder = ($exploder !== null && $exploder !== '') ? $exploder : ' ';

    return match ($case) {
        CASE_DASH  => implode('-', explode($exploder, mb_strtolower($in))),
        CASE_SNAKE => implode('_', explode($exploder, mb_strtolower($in))),
        CASE_TITLE => implode($imploder ?? $exploder, array_map(
            fn($s) => mb_ucfirst(trim($s)),
            explode($exploder, mb_strtolower($in))
        )),
        CASE_CAMEL => mb_lcfirst(implode('', array_map(
            fn($s) => mb_ucfirst(trim($s)),
            explode($exploder, mb_strtolower($in))
        ))),
    };
}

/**
 * Check whether class 1 extends class 2.
 *
 * @param  string $class1
 * @param  string $class2
 * @param  bool   $parent_only
 * @return bool
 * @since  4.21
 */
function class_extends(string $class1, string $class2, bool $parent_only = false): bool
{
    return !$parent_only ? is_subclass_of($class1, $class2)
         : is_subclass_of($class1, $class2) && current(class_parents($class1)) === $class2;
}

/**
 * Check whether interface 1 extends interface 2.
 *
 * @param  string $interface1
 * @param  string $interface2
 * @param  bool   $parent_only
 * @return bool
 * @since  5.31
 */
function interface_extends(string $interface1, string $interface2, bool $parent_only = false): bool
{
    return !$parent_only ? is_subclass_of($interface1, $interface2)
         : is_subclass_of($interface1, $interface2) && current(class_implements($interface1)) === $interface2;
}

/**
 * Get class name or short name.
 *
 * @param  string|object $class
 * @param  bool          $short
 * @param  bool          $clean
 * @return string
 * @since  5.0
 */
function get_class_name(string|object $class, bool $short = false, bool $clean = false): string
{
    return !$short ? Objects::getName($class, $clean) : Objects::getShortName($class, $clean);
}

/**
 * Get constants of given class/object, or return null if no such class.
 *
 * @param  string|object $class
 * @param  bool          $with_names
 * @param  bool          $scope_check
 * @return array|null
 * @since  4.0
 */
function get_class_constants(string|object $class, bool $with_names = true, bool $scope_check = true): array|null
{
    if ($scope_check) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            $all = ($caller_class === Objects::getName($class));
        }
    }

    return Objects::getConstantValues($class, ($all ?? !$scope_check), $with_names);
}

/**
 * Get properties of given class/object, or return null if no such class.
 *
 * @param  string|object $class
 * @param  bool          $with_names
 * @param  bool          $scope_check
 * @return array|null
 * @since  4.0
 */
function get_class_properties(string|object $class, bool $with_names = true, bool $scope_check = true): array|null
{
    if ($scope_check) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            $all = ($caller_class === Objects::getName($class));
        }
    }

    return Objects::getPropertyValues($class, ($all ?? !$scope_check), $with_names);
}

/**
 * Get a constant name.
 *
 * @param  mixed  $value
 * @param  string $name_prefix
 * @return string|null
 * @since  5.26
 */
function get_constant_name(mixed $value = null, string $name_prefix): string|null
{
    if ($name_prefix == '') {
        trigger_error(sprintf('%s(): Empty name prefix given', __function__));
        return null;
    }

    return array_first(array_filter(array_keys(get_defined_constants(), $value, true),
        fn($name) => str_starts_with($name, $name_prefix)));
}

/**
 * Get a constant value.
 *
 * @param  string     $name
 * @param  mixed|null $default
 * @return mixed|null
 * @since  5.26
 */
function get_constant_value(string $name, mixed $default = null): mixed
{
    return defined($name) ? constant($name) : $default;
}

/**
 * Check a constant exists, or return null if no such class.
 *
 * @param  string|object $class
 * @param  string        $name
 * @param  bool          $scope_check
 * @return bool
 * @since  4.0
 */
function constant_exists(string|object $class, string $name, bool $scope_check = true): bool
{
    if ($scope_check) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            return ($caller_class === Objects::getName($class)) && Objects::hasConstant($class, $name);
        }
        return defined(Objects::getName($class) .'::'. $name);
    }

    return (bool) Objects::hasConstant($class, $name);
}

/**
 * Get type with/without scalars option.
 *
 * @param  mixed $var
 * @param  bool  $scalars
 * @return string
 * @since  4.0
 */
function get_type(mixed $var, bool $scalars = false): string
{
    if ($scalars && is_scalar($var)) {
        return 'scalar';
    }
    return get_debug_type($var);
}

/**
 * Get last error if exists, by field when given.
 *
 * @param  string|null $field
 * @return any
 * @since  4.17
 */
function get_error(string $field = null)
{
    return $field ? error_get_last()[$field] ?? null
                  : error_get_last();
}

/**
 * Get a uniq-id with/without length & base options.
 *
 * @param  int  $length
 * @param  int  $base
 * @param  bool $hrtime
 * @param  bool $upper
 * @return string|null
 * @since  4.0
 */
function get_uniqid(int $length = 14, int $base = 16, bool $hrtime = false, bool $upper = false): string|null
{
    if ($length < 14) {
        trigger_error(sprintf('%s(): Invalid length, min=14', __function__));
        return null;
    }
    if ($base < 10 || $base > 62) {
        trigger_error(sprintf('%s(): Invalid base, min=10, max=62', __function__));
        return null;
    }

    // Grab 14-length hex from uniqid() or map to hex hrtime() stuff.
    if (!$hrtime) {
        $id = explode('.', uniqid('', true))[0];
    } else {
        $id = implode('', array_map('dechex', hrtime()));
    }

    $ret = $id;

    // Convert non-hex ids.
    if ($base != 16) {
        $ret = '';
        foreach (str_split($id, 8) as $i) {
            $ret .= convert_base($i, 16, $base);
        }
    }

    // Pad if needed.
    $ret_length = strlen($ret);
    if ($ret_length < $length) {
        $ret .= suid($length - $ret_length, $base);
    }

    $upper && $ret = strtoupper($ret);

    return strcut($ret, $length);
}

/**
 * Get a random uniq-id with/without length & base options.
 *
 * @param  int  $length
 * @param  int  $base
 * @param  bool $upper
 * @return string|null
 * @since  4.0
 */
function get_random_uniqid(int $length = 14, int $base = 16, bool $upper = false): string|null
{
    if ($length < 14) {
        trigger_error(sprintf('%s(): Invalid length, min=14', __function__));
        return null;
    }
    if ($base < 10 || $base > 62) {
        trigger_error(sprintf('%s(): Invalid base, min=10, max=62', __function__));
        return null;
    }

    $ret = '';

    while (strlen($ret) < $length) {
        $id = bin2hex(random_bytes(3));

        // Convert non-hex ids.
        $ret .= ($base == 16) ? $id : convert_base($id, 16, $base);
    }

    $upper && $ret = strtoupper($ret);

    return strcut($ret, $length);
}

/**
 * Get request id.
 *
 * @return string
 * @since  4.0
 */
function get_request_id(): string
{
    $parts   = explode('.', utime(true) .'.'. ip2long($_SERVER['SERVER_ADDR'] ?? ''));
    $parts[] = $_SERVER['SERVER_PORT'] ?? 0;
    $parts[] = $_SERVER['REMOTE_PORT'] ?? 0;

    return join('-', array_map(fn($p) => dechex((int) $p), $parts));
}

/**
 * Get real path of given path.
 *
 * @param  string           $path
 * @param  string|bool|null $check
 * @return string|null
 * @since  4.0
 */
function get_real_path(string $path, string|bool $check = null): string|null
{
    if (trim($path) == '') {
        return null;
    }
    if ($rpath = realpath($path)) {
        return $rpath;
    }

    $ret = '';
    $sep = DIRECTORY_SEPARATOR;
    $win = DIRECTORY_SEPARATOR == '\\';

    // Make path "foo" => "./foo" so prevent invalid returns.
    if (!str_contains($path, $sep) || ($win && substr($path, 1, 2) != ':\\')) {
        $path = '.' . $sep . $path;
    }

    foreach (explode($sep, $path) as $i => $cur) {
        if ($i == 0) {
            if ($cur == '~') { // Home path (eg: ~/Desktop).
                $ret = getenv('HOME') ?: '';
                continue;
            } elseif ($cur == '.' || $cur == '..') {
                if ($ret == '') {
                    // @cancel
                    // $file = getcwd(); // Fallback.
                    // foreach (debug_backtrace(0) as $trace) {
                    //     // Search until finding the right path argument (sadly seems no way else
                    //     // for that when call stack is chaining from a function to another function).
                    //     if (empty($trace['args'][0]) || $trace['args'][0] != $path) {
                    //         break;
                    //     }
                    //     $file = $trace['file'];
                    // }

                    $tmp = getcwd() . $sep . basename($path);
                    $ret = ($cur == '.') ? dirname($tmp) : dirname(dirname($tmp));
                }
                continue;
            }
        }

        if ($cur == '' || $cur == '.') {
            continue;
        } elseif ($cur == '..') {
            $ret = dirname($ret); // Up.
            continue;
        }

        // Prepend separator current.
        $ret .= $sep . $cur;
    }

    // Validate file/directory or file only existence.
    if ($check) {
        $ok = ($check == 'file') ? is_file($ret) : file_exists($ret);
        $ok || $ret = null;
    }

    // Normalize.
    if ($ret) {
        // Drop repeatings.
        $ret = preg_replace(
            '~(['. preg_quote(PATH_SEPARATOR . DIRECTORY_SEPARATOR) .'])\1+~',
            '\1', $ret
        );

        // Drop ending slashes.
        if ($ret != PATH_SEPARATOR && $ret != DIRECTORY_SEPARATOR) {
            $ret = chop($ret, PATH_SEPARATOR . DIRECTORY_SEPARATOR);
        }

        // Fix leading slash for win
        if ($win && $ret[0] == $sep) {
            $ret = substr($ret, 1);
        }
    }

    return $ret;
}

/**
 * Get path info of given path.
 *
 * @param  string          $path
 * @param  string|int|null $component
 * @return string|array|null
 * @since  5.0
 */
function get_path_info(string $path, string|int $component = null): string|array|null
{
    $path = get_real_path($path);
    if (!$path) {
        return null;
    }
    if (!$info = pathinfo($path)) {
        return null;
    }

    $ret = [
        'path' => $path,
        'type' => realpath($path) ? filetype($path) : null,
        ...array_map(fn($v) => strlen($v) ? $v : null, $info)
    ];

    $ret['filename']  = file_name($path, false);
    $ret['extension'] = file_extension($path, false);

    if ($component !== null) {
        if (is_string($component)) {
            $ret = $ret[$component] ?? null;
        } else {
            $ret = match ($component) {
                PATHINFO_DIRNAME  => $ret['dirname'],  PATHINFO_BASENAME  => $ret['basename'],
                PATHINFO_FILENAME => $ret['filename'], PATHINFO_EXTENSION => $ret['extension'],
                PATHINFO_TYPE     => $ret['type'],     default            => $ret, // All.
            };
        }
    }

    return $ret;
}

/**
 * Get a bit detailed trace with default options, limit, index and field options.
 *
 * @param  int|null    $options
 * @param  int|null    $limit
 * @param  int|null    $index
 * @param  string|null $field
 * @return any|null
 * @since  4.0
 */
function get_trace(int $options = null, int $limit = null, int $index = null, string $field = null)
{
    $trace = debug_backtrace($options ?? 0, $limit ? $limit + 1 : 0);
    array_shift($trace); // Drop self.

    foreach ($trace as $i => &$cur) {
        $cur += [
            'caller' => null,
            'callee' => $cur['function'] ?? null,
        ];

        if (isset($cur['file'], $cur['line'])) {
            $cur['callPath'] = $cur['file'] . ':' . $cur['line'];
        }
        if (isset($cur['class'])) {
            $cur['method']     = $cur['function'];
            $cur['methodType'] = ($cur['type']  == '::') ? 'static' : 'non-static';
        }
        if (isset($trace[$i + 1]['function'])) {
            $cur['caller'] = $trace[$i + 1]['function'];
        }
    }

    return is_null($index) ? $trace : ($trace[$index][$field] ?? $trace[$index] ?? null);
}

/**
 * Get system temporary directory.
 *
 * @return string
 * @since  4.0
 */
function tmp(): string
{
    return sys_get_temp_dir();
}

/**
 * Create a folder in system temporary directory.
 *
 * @param  string|null $prefix
 * @param  int         $mode
 * @return string|null
 * @since  5.0
 */
function tmpdir(string $prefix = null, int $mode = 0755): string|null
{
    // Prefix may becomes subdir here.
    $dir = tmp() . DIRECTORY_SEPARATOR . $prefix . suid();

    return mkdir($dir, $mode, true) ? $dir : null;
}

/**
 * Create a file in system temporary directory.
 *
 * @param  string|null $prefix
 * @param  int         $mode
 * @return string|null
 * @since  5.0
 */
function tmpnam(string $prefix = null, int $mode = 0644): string|null
{
    // Prefix may becomes subdir here.
    $nam = tmp() . DIRECTORY_SEPARATOR . $prefix . suid();

    return mkfile($nam, $mode, true) ? $nam : null;
}

/**
 * Check whether given directory is in temporary directory.
 *
 * @param  string $dir
 * @return bool
 * @since  5.0
 */
function is_tmpdir(string $dir): bool
{
    return is_dir($dir)
        && str_starts_with($dir, tmp() . DIRECTORY_SEPARATOR)
        && realpath($dir) !== tmp();
}

/**
 * Check whether given file is in temporary directory and created by tmpnam().
 *
 * @param  string $nam
 * @return bool
 * @since  5.0
 */
function is_tmpnam(string $nam): bool
{
    return is_file($nam)
        && str_starts_with($nam, tmp() . DIRECTORY_SEPARATOR);
}

/**
 * Create a file with given file path.
 *
 * @param  string $file
 * @param  int    $mode
 * @param  bool   $tmp @internal
 * @return bool
 * @since  4.0
 */
function mkfile(string $file, int $mode = 0644, bool $tmp = false): bool
{
    if (trim($file) == '') {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    $file = get_real_path($file);

    if (is_dir($file)) {
        trigger_error(sprintf('%s(): Cannot make file %s, it\'s a directory', __function__, $file));
        return false;
    } elseif (is_file($file)) {
        trigger_error(sprintf('%s(): Cannot make file %s, it\'s already exist', __function__, $file));
        return false;
    }

    // Ensure directory.
    $dir = dirname($file);
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        trigger_error(sprintf('%s(): Cannot make file directory %s [%s]', __function__, $dir, error_message()));
        return false;
    }

    return touch($file) && chmod($file, $mode);
}

/**
 * Remove a file.
 *
 * @param  string $file
 * @return bool
 * @since  4.0
 */
function rmfile(string $file): bool
{
    if (trim($file) == '') {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    $file = get_real_path($file);

    if (is_dir($file)) {
        trigger_error(sprintf('%s(): Cannot remove %s, it\'s a directory', __function__, $file));
        return false;
    }

    return is_file($file) && unlink($file);
}

/**
 * Create a folder in system temporary directory.
 *
 * @param  string|null $prefix
 * @param  int         $mode
 * @param  bool        $froq
 * @since  4.0
 * @return string|null
 */
function mkdirtemp(string $prefix = null, int $mode = 0755, bool $froq = false): string|null
{
    // Make froq subdir.
    $froq && $prefix = 'froq/' . $prefix;

    return tmpdir($prefix, $mode);
}

/**
 * Remove a folder from system temporary directory.
 *
 * @param  string $dir
 * @return bool
 * @since  4.0
 */
function rmdirtemp(string $dir): bool
{
    if (!is_tmpdir($dir)) {
        trigger_error(sprintf('%s(): Cannot remove a directory which is outside of %s directory',
            __function__, tmp()));
        return false;
    }

    // Clean inside but not recursive.
    if (is_dir($dir)) {
        foreach (glob($dir . '/*') as $file) {
            unlink($file);
        }
    }

    return is_dir($dir) && rmdir($dir);
}

/**
 * Create a file in temporary directory.
 *
 * @param  string|null $prefix
 * @param  int         $mode
 * @param  bool        $froq
 * @return string|null
 * @since  4.0
 */
function mkfiletemp(string $prefix = null, int $mode = 0644, bool $froq = false): string|null
{
    // Make froq subdir.
    $froq && $prefix = 'froq/' . $prefix;

    return tmpnam($prefix, $mode);
}

/**
 * Remove a file from in temporary directory.
 *
 * @param  string $file
 * @return bool
 * @since  4.0
 */
function rmfiletemp(string $file): bool
{
    if (!is_tmpnam($file)) {
        trigger_error(sprintf('%s(): Cannot remove a file that is outside of %s directory or non-existent',
            __function__, tmp()));
        return false;
    }

    return is_file($file) && unlink($file);
}

/**
 * Read all contents a file handle without modifing seek offset.
 *
 * @alias of file_read_stream()
 * @since 5.0
 */
function freadall(&$fp): string|null
{
    return file_read_stream($fp);
}

/**
 * Reset a file handle contents & set seek position to top.
 *
 * @alias of stream_set_contents()
 * @since 4.0
 */
function freset(&$fp, string $contents): int|null
{
    return stream_set_contents($fp, $contents);
}

/**
 * Get a file handle metadata.
 *
 * @param  resource $fp
 * @return array|null
 * @since  4.0
 */
function fmeta($fp): array|null
{
    return stream_get_meta_data($fp) ?: null;
}

/**
 * Get a file handle size.
 *
 * @param  resource $fp
 * @return int|null
 * @since  5.0
 */
function fsize($fp): int|null
{
    return fstat($fp)['size'] ?? null;
}

/**
 * Get a directory size.
 *
 * @param  string $dir
 * @param  bool   $deep
 * @return int|null
 * @since  5.0
 */
function dirsize(string $dir, bool $deep = true): int|null
{
    $dir = realpath($dir);
    if (!$dir) {
        return null;
    }

    $ret = 0;

    foreach (glob(chop($dir, '/') . '/*') as $path) {
        is_file($path) && $ret += filesize($path);
        if ($deep) {
            is_dir($path) && $ret += dirsize($path, $deep);
        }
    }

    return $ret;
}

/**
 * Reset a file/stream handle contents setting seek position to top.
 *
 * @param  resource &$handle
 * @param  string    $contents
 * @return int|null
 * @since  4.0
 */
function stream_set_contents(&$handle, string $contents): int|null
{
    // Since handle stat size also pointer position is not changing even after ftruncate() for
    // files (not "php://temp" etc), we rewind the handle. Without this, stats won't be resetted!
    rewind($handle);

    // Empty, write & rewind.
    ftruncate($handle, 0);
    $ret = fwrite($handle, $contents);
    rewind($handle);

    return ($ret !== false) ? $ret : null;
}

/**
 * Create a file, optionally a temporary file.
 *
 * @param  string $file
 * @param  int    $mode
 * @param  bool   $tmp
 * @return string|null
 * @since  4.0
 */
function file_create(string $file, int $mode = 0644, bool $tmp = false): string|null
{
    // Check tmp directive.
    if ($file == '@tmp') {
        [$file, $tmp] = [null, true];
    }

    return $tmp ? mkfiletemp($file, $mode) : (
        mkfile($file, $mode) ? $file : null
    );
}

/**
 * Create a temporary file.
 *
 * @alias of mkfiletemp()
 * @since 4.0
 */
function file_create_temp(...$args)
{
    return mkfiletemp(...$args);
}

/**
 * Remove a file.
 *
 * @alias of rmfile()
 * @since 4.0
 */
function file_remove(...$args)
{
    return rmfile(...$args);
}

/**
 * Write a file contents.
 *
 * @alias of file_put_contents()
 * @since 4.0
 */
function file_write(...$args)
{
    $ret = file_put_contents(...$args);

    return ($ret !== false) ? $ret : null;
}

/**
 * Read a file contents.
 *
 * @params ... $args Same as file_get_contents().
 * @since  4.0
 */
function file_read(...$args): string|null
{
    $ret = file_get_contents(...$args);

    return ($ret !== false) ? $ret : null;
}

/**
 * Read a file contents as base64-encoded.
 *
 * @params ... $args Same as file_get_contents().
 * @since  5.0
 */
function file_read_base64(...$args): string|null
{
    $ret = file_get_contents(...$args);

    return ($ret !== false) ? base64_encode($ret) : null;
}

/**
 * Read a file output (buffer) contents.
 *
 * @param  string     $file
 * @param  array|null $file_data
 * @return string|null
 * @since  4.0
 */
function file_read_output(string $file, array $file_data = null): string|null
{
    if (!is_file($file)) {
        trigger_error(sprintf('%s(): No file exists such %s', __function__, $file));
        return null;
    } elseif (!str_ends_with($file, '.php')) {
        trigger_error(sprintf('%s(): Cannot include non-PHP file such %s', __function__, $file));
        return null;
    }

    // Data, used in file.
    $file_data && extract($file_data);

    ob_start();
    include $file;
    return ob_get_clean();
}

/**
 * Read a file stream contents without modifing seek position.
 *
 * @param  resource &$handle
 * @return string|null
 * @since  5.0
 */
function file_read_stream(&$handle): string|null
{
    $pos = ftell($handle);
    $ret = stream_get_contents($handle, -1, 0);
    fseek($handle, $pos);

    return ($ret !== false) ? $ret : null;
}

/**
 * Set a file contents, but no append.
 *
 * @param  string $file
 * @param  string $contents
 * @param  int    $flags
 * @return int|null
 * @since  4.0
 */
function file_set_contents(string $file, string $contents, int $flags = 0): int|null
{
    // Because, setting entire file contents.
    if ($flags) $flags &= ~FILE_APPEND;

    $ret = file_put_contents($file, $contents, $flags);

    return ($ret !== false) ? $ret : null;
}

/**
 * Aliases.
 */
function filepath(...$args) { return file_path(...$args); }
function filename(...$args) { return file_name(...$args); }
function filemime(...$args) { return file_mime(...$args); }

/**
 * Get a file path.
 *
 * @alias of get_real_path()
 * @since 4.0
 */
function file_path(...$args)
{
    return get_real_path(...$args);
}

/**
 * Get file name, not base name.
 *
 * @param  string $file
 * @param  bool   $with_ext
 * @return string|null
 * @since  4.0
 */
function file_name(string $file, bool $with_ext = false): string|null
{
    // A directory is not a file.
    if (str_ends_with($file, DIRECTORY_SEPARATOR)) {
        return null;
    }

    // Function basename() wants an explicit suffix to remove it from name,
    // but using just a boolean here is more sexy..
    $ret = basename($file);

    if ($ret == '.' || $ret == '..') {
        return null;
    }

    if ($ret && !$with_ext && ($ext = file_extension($file, true))) {
        $ret = substr($ret, 0, -strlen($ext));
    }

    return $ret ?: null;
}

/**
 * Get file mime.
 *
 * @param  string $file
 * @return string|null
 * @since  4.0
 */
function file_mime(string $file): string|null
{
    $mime = mime_content_type($file) ?: null;

    if (!$mime) {
        // Try with extension.
        $extension = file_extension($file, false);
        if ($extension) {
            static $cache; // For some speed..
            if (empty($cache[$extension])) {
                foreach (require 'statics/mime.php' as $type => $extensions) {
                    if (in_array($extension, $extensions, true)) {
                        return ($cache[$extension] = $type);
                    }
                }
            }
        }
    }

    return $mime;
}

/**
 * Get file extension.
 *
 * @param  string $file
 * @param  bool   $with_dot
 * @param  bool   $lower
 * @return string|null
 * @since  4.0
 */
function file_extension(string $file, bool $with_dot = false, bool $lower = true): string|null
{
    $info = pathinfo($file);

    // Function pathinfo() returns ".foo" for example "/some/path/.foo" and
    // if $with_dot false then this function return ".", no baybe!
    if (empty($info['filename']) || empty($info['extension'])) {
        return null;
    }

    $ret = strrchr($info['basename'], '.');

    if ($ret) {
        $lower && $ret = strtolower($ret);
        if (!$with_dot) {
            $ret = ltrim($ret, '.');
        }
    }

    return $ret ?: null;
}

/**
 * Init a DateTime object with/without given when option & with/without timezone if given or default timezone.
 *
 * @param  int|float|string|null $when
 * @param  string|null           $where
 * @return DateTime
 * @since  4.25
 */
function udate(int|float|string $when = null, string $where = null): DateTime
{
    $when  ??= '';
    $where ??= System::defaultTimezone();

    switch (get_type($when)) {
        case 'int': // Eg: 1603339284
            $date = new DateTime('', new DateTimeZone($where));
            $date->setTimestamp($when);
            break;
        case 'float': // Eg: 1603339284.221243
            $date = DateTime::createFromFormat('U.u', sprintf('%.6F', $when));
            $date->setTimezone(new DateTimeZone($where));
            break;
        case 'string': // Eg: 2012-09-12 23:42:53
            $date = new DateTime($when, new DateTimeZone($where));
            break;
    }

    return $date;
}

/**
 * Get current microtime (float or string).
 *
 * @param  bool $string
 * @return float|string
 * @since  4.0
 */
function utime(bool $string = false): float|string
{
    $time = microtime(true);

    return $string ? sprintf('%.6F', $time): $time;
}

/**
 * Get current microtime (high-resolution).
 *
 * @param  string|null $where
 * @return int
 * @since  5.0
 */
function ustime(string $where = null): int
{
    $date = udate(utime(), $where);

    return (int) $date->format('Uu');
}

/**
 * Get an interval by given format.
 *
 * @param  string          $format
 * @param  string|int|null $time
 * @return int
 * @since  4.0
 */
function strtoitime(string $format, string|int $time = null): int
{
    // Eg: "1 day" or "1D" (instead "60*60*24" or "86400").
    if (preg_match_all('~([+-]?\d+)([smhDMY])~', $format, $matches)) {
        $format_list = null;
        [, $numbers, $formats] = $matches;

        foreach ($formats as $i => $format) {
            $format_list[] = match ($format) {
                's' => $numbers[$i] . ' second',
                'm' => $numbers[$i] . ' minute',
                'h' => $numbers[$i] . ' hour',
                'D' => $numbers[$i] . ' day',
                'M' => $numbers[$i] . ' month',
                'Y' => $numbers[$i] . ' year',
            };
        }

        // Update format.
        $format_list && $format = join(' ', $format_list);
    }

    $time ??= time();
    if (is_string($time)) {
        $time = strtotime($time);
    }

    return strtotime($format, $time) - $time;
}

/**
 * Get current locale info.
 *
 * @param  int               $category
 * @param  string|array|null $default
 * @param  bool              $array
 * @return string|array|null
 * @since  5.26
 */
function getlocale(int $category = LC_ALL, string|array $default = null, bool $array = false): string|array|null
{
    $ret = $tmp = setlocale($category, 0);
    if ($ret === false) {
        $ret = $default;
    }

    if ($tmp !== false && $array) {
        $tmp = [];
        if (str_contains($ret, ';')) {
            foreach (split(';', $ret) as $re) {
                [$name, $value] = split('=', $re, 2);
                $tmp[] = [$name, get_constant_value($name), 'value' => $value];
            }
        } else {
            $tmp = [$name = get_constant_name($category, 'LC_'),
                    $name ? $category : null, 'value' => $ret];
        }
        $ret = $tmp;
    }

    return $ret;
}

/**
 * Perform a regular expression search returning a bool result.
 *
 * @param  string $pattern
 * @param  string $subject
 * @return bool
 * @since  4.0
 */
function preg_test(string $pattern, string $subject): bool
{
    return (bool) preg_match($pattern, $subject);
}

/**
 * Perform a regular expression search & remove.
 *
 * @param  string|array  $pattern
 * @param  string|array  $subject
 * @param  int|null      $limit
 * @param  int|null     &$count
 * @return string|array|null
 * @since  4.0
 */
function preg_remove(string|array $pattern, string|array $subject, int $limit = null, int &$count = null): string|array|null
{
    if (is_string($pattern)) {
        $replacement = '';
    } else {
        $replacement = array_fill(0, count($pattern), '');
    }

    return preg_replace($pattern, $replacement, $subject, $limit ?? -1, $count);
}

/**
 * Clean given array filtering non-empty values.
 *
 * @param  array $array
 * @return array
 * @since  4.0
 */
function array_clean(array $array): array
{
    return array_filter($array, fn($v) => $v !== null && $v !== '' && $v !== []);
}

/**
 * Check whether all given keys are set in given array.
 *
 * @param  array         $array
 * @param  int|string ...$keys
 * @return bool
 * @since  4.0
 */
function array_isset(array $array, int|string ...$keys): bool
{
    foreach ($keys as $key) {
        if (!isset($array[$key])) {
            return false;
        }
    }
    return true;
}

/**
 * Drop all given keys from given array.
 *
 * @param  array         &$array
 * @param  int|string  ...$keys
 * @return array
 * @since  4.0
 */
function array_unset(array &$array, int|string ...$keys): array
{
    foreach ($keys as $key) {
        unset($array[$key]);
    }
    return $array;
}

/**
 * Check whether given value(s) are set in given array.
 *
 * @param  array    $array
 * @param  mixed ...$values
 * @return bool
 * @since  5.0
 */
function array_contains(array $array, mixed ...$values): bool
{
    foreach ($values as $value) {
        if (!in_array($value, $array, true)) {
            return false;
        }
    }
    return $array && $values;
}

/**
 * Check whether given key(s) are set in given array.
 *
 * @param  array         $array
 * @param  int|string ...$keys
 * @return bool
 * @since  5.3
 */
function array_contains_key(array $array, int|string ...$keys): bool
{
    foreach ($keys as $key) {
        if (!array_key_exists($key, $array)) {
            return false;
        }
    }
    return $array && $keys;
}

/**
 * Drop given value(s) from given array if exist.
 *
 * @param  array    &$array
 * @param  mixed ...$values
 * @return array
 * @since  5.0
 */
function array_delete(array &$array, mixed ...$values): array
{
    foreach ($values as $value) {
        $key = array_search($value, $array, true);
        if ($key !== false) {
            unset($array[$key]);
        }
    }
    return $array;
}

/**
 * Drop given key(s) from given array if exist.
 *
 * @param  array      &$array
 * @param  int|string  $keys
 * @return array
 * @since  5.31
 */
function array_delete_key(array &$array, int|string ...$keys): array
{
    foreach ($keys as $key) {
        unset($array[$key]);
    }
    return $array;
}

/**
 * Append given values to an array, returning given array back.
 *
 * @param  array &$array
 * @param  ...    $values
 * @return array
 * @since  4.0
 */
function array_append(array &$array, ...$values): array
{
    array_push($array, ...$values);
    return $array;
}

/**
 * Prepend given values to an array, returning given array back.
 *
 * @param  array &$array
 * @param  ...    $values
 * @return array
 * @since  4.0
 */
function array_prepend(array &$array, ...$values): array
{
    array_unshift($array, ...$values);
    return $array;
}

/**
 * Fun function, for the sake of array_pop().
 *
 * @param  array &$array
 * @return any
 * @since  4.0
 */
function array_top(array &$array)
{
    return array_shift($array);
}

/**
 * Fun function, for the sake of array_unshift().
 *
 * @param  array &$array
 * @param  ...    $values
 * @return int
 * @since  4.0
 */
function array_unpop(array &$array, ...$values): int
{
    return array_push($array, ...$values);
}

/**
 * Apply given function to each element of given array with key/value notation as default.
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $swap_keys
 * @param  bool     $recursive
 * @return array
 * @since  4.0
 */
function array_apply(array $array, callable $func, bool $swap_keys = false, bool $recursive = false): array
{
    foreach ($array as $key => $value) {
        $array[$key] = ($recursive && is_array($value))
            ? array_apply($value, $func, $swap_keys, true)
            : ($swap_keys ? $func($key, $value)   // With key/value notation.
                          : $func($value, $key)); // With value/key notation.
    }

    return $array;
}

/**
 * Recursive version of array_apply().
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $swap_keys
 * @return array
 * @since  5.37
 */
function array_apply_recursive(array $array, callable $func, bool $swap_keys = false): array
{
    return array_apply($array, $func, $swap_keys, true);
}

/**
 * Recursive version of array_filter().
 *
 * @param  array         $array
 * @param  callable|null $func
 * @param  int           $mode
 * @return array
 * @since  5.40
 */
function array_filter_recursive(array $array, callable $func = null, int $mode = 0): array
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = array_filter_recursive($value, $func, $mode);
        }
    }

    return array_filter($array, $func, $mode);
}

/**
 * Recursive version of array_map().
 *
 * @param  callable $func
 * @param  array    $array
 * @return array
 * @since  5.1
 */
function array_map_recursive(callable $func, array $array): array
{
    foreach ($array as $key => $value) {
        $array[$key] = is_array($value) ? array_map_recursive($func, $value)
            : $func($value);
    }

    return $array;
}

/**
 * Map an array keys (@see https://wiki.php.net/rfc/array_change_keys).
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $recursive
 * @return array
 * @since  5.0
 */
function array_map_keys(array $array, callable $func, bool $recursive = false): array
{
    $ret = [];
    foreach ($array as $key => $value) {
        $key = $func($key);
        $ret[$key] = ($recursive && is_array($value)) ? array_map_keys($value, $func, true)
            : $value;
    }

    return $ret;
}

/**
 * Ensure an array keys.
 *
 * @param  array    $array
 * @param  array    $keys
 * @param  any|null $value
 * @param  bool     $isset
 * @return array
 * @since  4.0
 */
function array_pad_keys(array $array, array $keys, $value = null, bool $isset = false): array
{
    foreach ($keys as $key) {
        $ok = $isset ? isset($array[$key]) : array_key_exists($key, $array);
        $ok || $array[$key] = $value;
    }

    return $array;
}

/**
 * Convert key cases to lower.
 *
 * @param  array  $array
 * @param  bool   $recursive
 * @return array
 * @since  6.0
 */
function array_lower_keys(array $array, bool $recursive = false): array
{
    return array_map_keys($array, fn($key) => mb_strtolower((string) $key), $recursive);
}

/**
 * Convert key cases to upper.
 *
 * @param  array  $array
 * @param  bool   $recursive
 * @return array
 * @since  6.0
 */
function array_upper_keys(array $array, bool $recursive = false): array
{
    return array_map_keys($array, fn($key) => mb_strtoupper((string) $key), $recursive);
}

/**
 * Convert key cases to given case.
 *
 * @param  array       $array
 * @param  int         $case
 * @param  string|null $exploder
 * @param  string|null $imploder
 * @param  bool        $recursive
 * @return array
 * @since  4.19
 */
function array_convert_keys(array $array, int $case, string $exploder = null, string $imploder = null, bool $recursive = false): array
{
    return array_map_keys($array, fn($key) => convert_case((string) $key, $case, $exploder, $imploder), $recursive);
}

/**
 * Get key of given value or return null if not found, with/without last (key) option.
 *
 * @param  array array
 * @param  any   $value
 * @param  bool  $strict
 * @param  bool  $last
 * @return int|string|null
 * @since  5.0
 */
function array_search_key(array $array, $value, bool $strict = true, bool $last = false): int|string|null
{
    $key = !$last ? array_search($value, $array, $strict)
                  : array_search($value, array_reverse($array, true), $strict);

    return ($key !== false) ? $key : null;
}

/**
 * Search given values with/without strict comparison, returning found their keys.
 *
 * @param  array  $array
 * @param  array  $values
 * @param  bool   $strict
 * @param  bool   $reverse
 * @return array<int|string|null>
 * @since  4.0, 5.25 moved from sugars/array.
 */
function array_search_keys(array $array, array $values, bool $strict = true, bool $reverse = false): array
{
    $ret = [];

    $keys = array_keys($array);
    foreach ($values as $key => $value) {
        foreach ($keys as $key) {
            if ($strict ? ($array[$key] === $value) : ($array[$key] == $value)) {
                $ret[] = $key;
            }
        }
    }

    $reverse && $ret = array_reverse($ret);

    return $ret;
}

/**
 * Check a value if exists with/without strict comparison, filling found key as ref-arg.
 *
 * @param  any         $value
 * @param  array       $array
 * @param  bool        $strict
 * @param  int|string &$key
 * @return bool
 * @since  4.0
 */
function array_value_exists($value, array $array, bool $strict = true, int|string &$key = null): bool
{
    $key = array_search_key($array, $value, $strict);

    return ($key !== null);
}

/**
 * Fetch item(s) from an array by given path(s) with dot notation.
 *
 * @param  array                &$array
 * @param  string|array<string>  $path
 * @param  any|null              $default
 * @param  bool                  $drop
 * @return any|null
 * @since  5.0
 */
function array_fetch(array &$array, string|array $path, $default = null, bool $drop = false)
{
    if (is_array($path)) {
        $ret = [];
        foreach ($path as $pat) {
            $ret[] = array_fetch($array, (string) $pat, $default, $drop);
        }
        return $ret;
    }

    if (array_key_exists($path, $array)) {
        $ret = $array[$path] ?? $default;
        if ($drop) {
            unset($array[$path]);
        }
        return $ret;
    }

    $keys = explode('.', $path);
    $key  = array_shift($keys);

    if (!$keys) {
        $ret = $array[$key] ?? $default;
        if ($drop) {
            unset($array[$key]);
        }
        return $ret;
    }

    // Dig more..
    if (is_array($array[$key] ?? null)) {
        return array_fetch($array[$key], implode('.', $keys), $default, $drop);
    }

    return $default;
}

/**
 * Select item(s) from an array by given key(s), optionally combining keys/values.
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $default
 * @param  bool                          $drop
 * @param  bool                          $combine
 * @return any|null
 * @since  5.0
 */
function array_select(array &$array, int|string|array $key, $default = null, bool $drop = false, bool $combine = false)
{
    // A little bit faster comparing to array_fetch() & array_pick().
    foreach ((array) $key as $ke) {
        $ret[] = $array[$ke] ?? $default;
        if ($drop) {
            unset($array[$ke]);
        }
    }

    if ($combine) {
        return array_combine((array) $key, $ret);
    }

    return is_array($key) ? ($ret ?? null) : ($ret[0] ?? null);
}

/**
 * Put given key,value item or item (key) with value into an array.
 *
 * @param  array   &$array
 * @param  array    $item
 * @param  any|null $value
 * @return array
 * @since  4.18
 */
function array_put(array &$array, int|string|array $item, $value = null): array
{
    return is_array($item) ? Arrays::setAll($array, $item)
                           : Arrays::set($array, $item, $value);
}

/**
 * Drop an item from an array by given key or dotted path.
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @return any|null
 * @since  5.0
 */
function array_drop(array &$array, int|string|array $key): array
{
    array_pluck($array, $key);
    return $array;
}

/**
 * Pick an item from an array by given key or dotted path.
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $default
 * @param  bool                          $drop
 * @return any|null
 * @since  4.9, 4.13 Added default.
 */
function array_pick(array &$array, int|string|array $key, $default = null, bool $drop = false)
{
    // Just got sick of "value=array[..] ?? .." stuffs.
    return is_array($key) ? Arrays::getAll($array, $key, $default, $drop)
                          : Arrays::get($array, $key, $default, $drop);
}

/**
 * Pluck an item from an array by given key or dotted path.
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $default
 * @return any|null
 * @since  4.13
 */
function array_pluck(array &$array, int|string|array $key, $default = null)
{
    // Just got sick of "if isset array[..]: value=array[..], unset(array[..])" stuffs.
    return is_array($key) ? Arrays::pullAll($array, $key, $default)
                          : Arrays::pull($array, $key, $default);
}

/**
 * Find first item that satisfies given test function.
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $reverse
 * @return mixed|null
 * @since  5.0, 5.31
 */
function array_find(array $array, callable $func, bool $reverse = false): mixed
{
    return Arrays::find($array, $func, $reverse);
}

/**
 * Find all items that satisfy given test function.
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $reverse
 * @param  bool     $use_keys
 * @return array<mixed|null>
 * @since  5.31
 */
function array_find_all(array $array, callable $func, bool $reverse = false, bool $use_keys = false): array
{
    return Arrays::findAll($array, $func, $reverse, $use_keys);
}

/**
 * Find first item key that satisfies given test function.
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $reverse
 * @return int|string|null
 * @since  5.0
 */
function array_find_key(array $array, callable $func, bool $reverse = false): int|string|null
{
    return Arrays::findKey($array, $func, $reverse);
}

/**
 * Find all keys that satisfy given test function.
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $reverse
 * @return array<int|string|null>
 * @since  5.31
 */
function array_find_keys(array $array, callable $func, bool $reverse = false): array
{
    return Arrays::findKeys($array, $func, $reverse);
}

/**
 * Bridge function to Arrays.flat().
 *
 * @param  array $array
 * @param  bool  $use_keys
 * @param  bool  $fix_keys
 * @param  bool  $multi
 * @return array
 * @since  4.0
 */
function array_flat(array $array, bool $use_keys = false, bool $fix_keys = false, bool $multi = true): array
{
    return Arrays::flat($array, $use_keys, $fix_keys, $multi);
}

/**
 * Get a/a few random item(s) from an array with/without given limit.
 *
 * @param  array    &$array
 * @param  int       $limit
 * @param  any|null  $default
 * @param  bool      $drop
 * @return any|null
 * @since  4.12
 */
function array_rand_value(array &$array, int $limit = 1, $default = null, bool $drop = false)
{
    // Just got sick of "value = array[array_rand(array)]" stuffs.
    return Arrays::getRandom($array, $limit, drop: $drop) ?? $default;
}

/**
 * Caculate average an array summing all items.
 *
 * @param  array $array
 * @param  bool  $zeros
 * @return float
 * @since  4.5, 4.20 Derived from array_avg().
 */
function array_average(array $array, bool $zeros = true): float
{
    return Arrays::average($array, $zeros);
}

/**
 * Aggregate an array by given function.
 *
 * @param  array      $array
 * @param  callable   $func
 * @param  array|null $carry
 * @return array
 * @since  4.14, 4.15 Derived from array_agg().
 */
function array_aggregate(array $array, callable $func, array $carry = null): array
{
    return Arrays::aggregate($array, $func, $carry);
}

/**
 * Shuffle an array maintaining keys if assoc true.
 *
 * @param  array $array
 * @param  bool  $assoc
 * @return array
 * @since  4.0, 5.25 Moved from sugar/array.
 */
function array_shuffle(array $array, bool $assoc = false): array
{
    return Arrays::shuffle($array, $assoc);
}

/**
 * Compose an array with given keys/values, unlike errorizing array_combine().
 *
 * @param  array<int|string> $keys
 * @param  array             $values
 * @param  mixed|null        $default
 * @return array
 * @since  4.11, 5.38 Moved from sugars/array.
 */
function array_compose(array $keys, array $values, mixed $default = null): array
{
    return Arrays::compose($keys, $values, $default);
}

/**
 * Merge all given item(s) with given array with strict uniqueness.
 *
 * @param  array    $array
 * @param  mixed    $item
 * @param  mixed ...$items
 * @return array
 * @since  5.30
 */
function array_concat(array $array, mixed $item, mixed ...$items): array
{
    return Arrays::concat($array, $item, ...$items);
}

/**
 * Merge all given array(s) with strict uniqueness.
 *
 * @param  array    $array1
 * @param  array    $array2
 * @param  array ...$arrays
 * @return array
 * @since  5.0
 */
function array_union(array $array1, array $array2, array ...$arrays): array
{
    return Arrays::union($array1, $array2, ...$arrays);
}

/**
 * Deduplicate repeating values with strict comparison as default.
 *
 * @param  array $array
 * @param  bool  $strict
 * @return array
 * @since  5.25
 */
function array_dedupe(array $array, bool $strict = true): array
{
    return Arrays::dedupe($array, $strict);
}

/**
 * Create a groupped result from given array (@see https://wiki.php.net/rfc/array_column_results_grouping).
 *
 * @param  array  $array
 * @param  string $field
 * @return array
 * @since  5.31
 */
function array_group(array $array, int|string $field): array
{
    $ret = [];

    foreach ($array as $row) {
        $row = (array) $row;
        if (array_key_exists($field, $row)) {
            $key = $row[$field];
            $ret[$key][] = $row;
        }
    }

    return $ret;
}

/**
 * Get first value of given array.
 *
 * @param  array $array
 * @return any|null
 * @since  5.0
 */
function array_first(array $array)
{
    return $array[array_key_first($array)] ?? null;
}

/**
 * Get last value of given array.
 *
 * @param  array $array
 * @return any|null
 * @since  5.0
 */
function array_last(array $array)
{
    return $array[array_key_last($array)] ?? null;
}

/**
 * Get given array stuff as entries.
 *
 * @param  array $array
 * @return array
 * @since  5.19
 */
function array_entries(array $array): array
{
    foreach ($array as $key => $value) {
        $ret[] = [$key, $value];
    }

    return $ret ?? [];
}

/**
 * Like array_push() but taking key/value arguments.
 *
 * @param  array      &$array
 * @param  int|string  $key
 * @param  mixed       $value
 * @return array
 * @since  5.22
 */
function array_push_entry(array &$array, int|string $key, mixed $value): array
{
    // Drop old key (in case).
    unset($array[$key]);

    $array[$key] = $value;

    return $array;
}

/**
 * Like array_pop() but returning key/value pairs.
 *
 * @param  array &$array
 * @return array|null
 * @since  5.22
 */
function array_pop_entry(array &$array): array|null
{
    $key = array_key_last($array);

    if ($key !== null) {
        return [$key, array_pop($array)];
    }

    return null;
}

/**
 * Like array_unshift() but taking key/value arguments.
 *
 * @param  array      &$array
 * @param  int|string  $key
 * @param  mixed       $value
 * @return array
 * @since  5.22
 */
function array_unshift_entry(array &$array, int|string $key, mixed $value): array
{
    $array = [$key => $value] + $array;

    return $array;
}

/**
 * Like array_shift() but returning key/value pairs.
 *
 * @param  array &$array
 * @return array|null
 * @since  5.22
 */
function array_shift_entry(array &$array): array|null
{
    $key = array_key_first($array);

    if ($key !== null) {
        if (is_list($array)) {
            return [$key, array_shift($array)];
        }
        // Keep assoc keys (do not modify).
        return [$key, array_select($array, $key, drop: true)];
    }

    return null;
}

/**
 * Compact given keys with given vars.
 *
 * @param  int|string|array    $keys
 * @param  mixed            ...$vars
 * @return array
 * @since  6.0
 */
function array_compact(int|string|array $keys, mixed ...$vars): array
{
    $ret = [];

    foreach ((array) $keys as $i => $key) {
        $ret[$key] = $vars[$i] ?? null;
    }

    return $ret;
}

/**
 * Extract given keys to given vars with refs.
 *
 * @param  int|string|array     $keys
 * @param  mixed            &...$vars
 * @return int
 * @since  6.0
 */
function array_extract(array $array, int|string|array $keys, mixed &...$vars): int
{
    $ret = 0;

    foreach ((array) $keys as $i => $key) {
        if (isset($array[$key])) {
            $vars[$i] = $array[$key];
            $ret++;
        }
    }

    return $ret;
}

/**
 * Get current value of given array (for the sake of current())
 * or given key's value if exists.
 *
 * @param  array           $array
 * @param  int|string|null $key
 * @param  mixed|null      $default
 * @return mixed
 * @since  5.35
 */
function value(array $array, int|string $key = null, mixed $default = null): mixed
{
    return ($key === null)
         ? (first($array) ?? $default)
         : ($array[$key]  ?? $default);
}

/**
 * Really got sick of "pass by reference" error.
 *
 * @param  array $array
 * @return mixed
 * @since  5.29
 */
function first(array $array): mixed
{
    return $array ? reset($array) : null; // No falses.
}

/**
 * Really got sick of "pass by reference" error.
 *
 * @param  array $array
 * @return mixed
 * @since  5.29
 */
function last(array $array): mixed
{
    return $array ? end($array) : null; // No falses.
}

/**
 * Sort an array without modifying input array.
 *
 * @param  array              $array
 * @param  callable|int|null  $func
 * @param  int                $flags
 * @param  bool|null          $assoc
 * @return array
 * @since  5.41
 */
function sorted(array $array, callable|int $func = null, int $flags = 0, bool $assoc = null): array
{
    return Arrays::sort($array, $func, $flags, $assoc);
}

/**
 * Remove last error message with/without code.
 *
 * @param  int|null $code
 * @return void
 * @since  5.0
 */
function error_clear(int $code = null): void
{
    if ($code && $code !== get_error('type')) {
        return;
    }

    error_clear_last();
}

/**
 * Get last error message with code, optionally formatted.
 *
 * @param  int|null $code
 * @param  bool     $format
 * @param  bool     $extract
 * @return string|null
 * @since  4.17
 */
function error_message(int &$code = null, bool $format = false, bool $extract = false): string|null
{
    $error = error_get_last();
    if (!$error) {
        return null;
    }

    $code = $error['type'];

    // Format with name.
    if ($format) {
        $error['name'] = match ($error['type']) {
            E_NOTICE,     E_USER_NOTICE     => 'NOTICE',
            E_WARNING,    E_USER_WARNING    => 'WARNING',
            E_DEPRECATED, E_USER_DEPRECATED => 'DEPRECATED',
            default                         => 'ERROR'
        };

        return vsprintf('%s(%d): %s at %s:%s', array_select(
            $error, ['name', 'type', 'message', 'file', 'line']
        ));
    }
    // Extract message only.
    elseif ($extract) {
        return substr($error['message'], strpos($error['message'], '):') + 3);
    }

    return $error['message'];
}

/**
 * Get JSON last error message with code if any, instead "No error".
 *
 * @param  int|null $code
 * @return string|null
 * @since  4.17
 */
function json_error_message(int &$code = null): string|null
{
    return ($code = json_last_error()) ? json_last_error_msg() : null;
}

/**
 * Get PECL last error message with code if any, instead "No error".
 *
 * @param  int|null $code
 * @return string|null
 * @since  4.17
 */
function preg_error_message(string $call = null, int &$code = null): string|null
{
    if ($call == null) {
        return ($code = preg_last_error()) ? preg_last_error_msg() : null;
    }

    $error_message = error_message($error_code);
    if ($error_message) {
        $message = grep($error_message, "~{$call}\(\):\s+(.+)~");
        if ($message) {
            $code = $error_code;
            return $message;
        }
    }
    return null;
}

/**
 * Generate an arbitrary unique identifier in given/default base.
 *
 * @param  int $length
 * @param  int $base
 * @return string|null
 * @since  5.0
 */
function suid(int $length = 6, int $base = 62): string|null
{
    if ($length < 1) {
        trigger_error(sprintf('%s(): Invalid length, min=14', __function__));
        return null;
    } elseif ($base < 2 || $base > 62) {
        trigger_error(sprintf('%s(): Invalid base, min=10, max=62', __function__));
        return null;
    }

    $ret = '';
    $max = $base - 1;

    srand();
    while ($length--) {
        $ret .= BASE62_ALPHABET[rand(0, $max)];
    }

    return $ret;
}

/**
 * Generate a random UUID/GUID, optionally with current timestamp.
 *
 * @param  bool $dashed
 * @param  bool $timed
 * @param  bool $guid
 * @return string
 * @since  5.0
 */
function uuid(bool $dashed = true, bool $timed = false, bool $guid = false): string
{
    $bytes = !$timed
        ? random_bytes(16) // Fully 16-random bytes.
        : hex2bin(dechex(time())) . random_bytes(12); // Time bin prefix & 12-random bytes.

    // Add signs: 4 (version) & 8, 9, A, B, but GUID doesn't use them.
    if (!$guid) {
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
    }

    $ret = uuid_format(bin2hex($bytes));

    return $dashed ? $ret : str_replace('-', '', $ret);
}

/**
 * Generate a random UUID/GUID hash, optionally with current timestamp.
 *
 * @param  int  $length
 * @param  bool $dashed
 * @param  bool $timed
 * @param  bool $guid
 * @return string|null
 * @since  5.0
 */
function uuid_hash(int $length = 32, bool $dashed = false, bool $timed = false, bool $guid = false): string|null
{
    $algo =@ [32 => 'md5', 40 => 'sha1', 64 => 'sha256', 16 => 'fnv1a64'][$length];

    if (!$algo) {
        trigger_error(sprintf('%s(): Invalid length, valids are: 32,40,64,16', __function__));
        return null;
    }

    $ret = hash($algo, uuid(false, $timed, $guid));

    return !$dashed ? $ret : uuid_format($ret);
}

/**
 * Format given input as UUID/GUID.
 *
 * @param  string $in
 * @return string|null
 * @since  5.0
 */
function uuid_format(string $in): string|null
{
    if (strlen($in) != 32 || !ctype_xdigit($in)) {
        trigger_error(sprintf('%s(): Format for only 32-length UUIDs/GUIDs', __function__));
        return null;
    }

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($in, 4));
}

/**
 * Format for sprintf().
 *
 * @param  string   $format
 * @param  mixed    $input
 * @param  mixed ...$inputs
 * @return string
 */
function format(string $format, mixed $input, mixed ...$inputs): string
{
    $params = [$input, ...$inputs];

    // Convert special formats (quoted string, int).
    $format = str_replace(['%q', '%i'], ["'%s'", '%d'], $format);

    // Convert bools as proper bools (not 1|0).
    if (str_contains($format, '%b')) {
        foreach (grep_all($format, '~(%[a-z])~') as $i => $op) {
            if ($op == '%b') {
                $format = substr_replace($format, '%s', strpos($format, '%b'), 2);
                $params[$i] = format_bool($params[$i]);
            }
        }
    }

    return vsprintf($format, $params);
}

/**
 * Format an input as bool (yes).
 *
 * @param  bool|int $input
 * @return string
 * @since  5.31
 */
function format_bool(bool|int $input): string
{
    return $input ? 'true' : 'false';
}

/**
 * Format an input as number (properly).
 *
 * @param  int|float|string $input
 * @param  int|null         $decimals
 * @param  string|null      $dsep
 * @param  string|null      $tsep
 * @return string|null
 * @since  5.31
 */
function format_number(int|float|string $input, int|null $decimals = 0, string|null $dsep = '.', string|null $tsep = ','): string|null
{
    if (is_string($input)) {
        if (!is_numeric($input)) {
            trigger_error(sprintf('%s(): Invalid non-numeric input', __function__));
            return null;
        }

        $input = 0 + $input;
    }

    // Prevent number corruption.
    if ($decimals && $decimals > NUMBER_PRECISION) {
        $decimals = NUMBER_PRECISION;
    }

    $ret = number_format($input, (int) $decimals, $dsep, $tsep);

    // Append ".0" for 1.0 & upper NAN/INF.
    if (!$decimals && !is_int($input) && strlen($ret) == 1) {
        $ret .= '.0';
    } elseif ($ret == 'inf' || $ret == 'nan') {
        $ret = strtoupper($ret);
    }

    return $ret;
}

/**
 * Convert a multi-byte string's first character to upper-case.
 *
 * @param  string $in
 * @param  bool   $tr
 * @return string
 * @since  5.0
 */
function mb_ucfirst(string $in, bool $tr = false): string
{
    $first = mb_substr($in, 0, 1);
    if ($tr && $first === 'i') {
        $first = 'İ';
    }

    return mb_strtoupper($first) . mb_substr($in, 1);
}

/**
 * Convert a multi-byte string's first character to lower-case.
 *
 * @param  string $in
 * @param  bool   $tr
 * @return string
 * @since  5.0
 */
function mb_lcfirst(string $in, bool $tr = false): string
{
    $first = mb_substr($in, 0, 1);
    if ($tr && $first === 'I') {
        $first = 'ı';
    }

    return mb_strtolower($first) . mb_substr($in, 1);
}

/**
 * Get a character by given index in Unicode style.
 *
 * @param  string $in
 * @param  int    $index
 * @return string|null
 * @since  5.17
 */
function char_at(string $in, int $index): string|null
{
    $char = mb_substr($in, $index, 1);

    return ($char != '') ? $char : null;
}

/**
 * Get a character code by given index in Unicode style.
 *
 * @param  string $in
 * @param  int    $index
 * @return int|null
 * @since  5.17
 */
function char_code_at(string $in, int $index): int|null
{
    $char = mb_substr($in, $index, 1);

    return ($char != '') ? mb_ord($char) : null;
}

/**
 * Translate given input to slugified output.
 *
 * @param  string $in
 * @param  string $preserve
 * @param  string $replace
 * @return string
 * @since  5.0
 */
function slug(string $in, string $preserve = '', string $replace = '-'): string
{
    static $chars;
    $chars ??= require 'statics/slug-chars.php';

    $preserve && $preserve = preg_quote($preserve, '~');
    $replace  || $replace  = '-';

    $out = preg_replace(['~[^\w'. $preserve . $replace .']+~', '~['. $replace .']+~'],
        $replace, strtr($in, $chars));

    return strtolower(trim($out, $replace));
}

/**
 * Generate a random number.
 *
 * @param  int|float|null $min
 * @param  int|float|null $max
 * @param  int|null       $precision
 * @return int|float
 * @since  5.14
 */
function random(int|float $min = null, int|float $max = null, int $precision = null): int|float
{
    return Numbers::random($min, $max, $precision);
}

/**
 * Generate a random float, optionally with precision.
 *
 * @param  float|null $min
 * @param  float|null $max
 * @param  int        $precision
 * @return float
 * @since  5.0
 */
function random_float(float $min = null, float $max = null, int $precision = null): float
{
    return Numbers::randomFloat($min, $max, $precision);
}

/**
 * Generate a random string, optionally puncted.
 *
 * @param  int  $length
 * @param  bool $puncted
 * @return string
 * @since  5.0
 */
function random_string(int $length, bool $puncted = false): string
{
    return Strings::random($length, $puncted);
}

/**
 * Generate a random range by given length.
 *
 * @param  int            $length
 * @param  int|float|null $min
 * @param  int|float|null $max
 * @param  int|null       $precision
 * @param  bool           $unique
 * @return array|null
 * @since  5.41
 */
function random_range(int $length, int|float $min = null, int|float $max = null, int $precision = null, bool $unique = true): array|null
{
    $ret = [];

    if ($length < 0) {
        trigger_error(sprintf('%s(): Negative length given', __function__));
        return null;
    }

    while ($length) {
        $item = Numbers::random($min, $max, $precision);

        // Provide unique-ness.
        while ($unique && in_array($item, $ret, true)) {
            $item = Numbers::random($min, $max, $precision);
        }

        $ret[] = $item;
        $length--;
    }

    return $ret;
}

/**
 * Get an object id.
 *
 * @param  object $object
 * @param  bool   $with_name
 * @return string
 * @since  5.25
 */
function get_object_id(object $object, bool $with_name = true): string
{
    return Objects::getId($object, $with_name);
}

/**
 * Get an object hash.
 *
 * @param  object $object
 * @param  bool   $with_name
 * @param  bool   $rehash
 * @param  bool   $serialize
 * @return string
 * @since  5.25
 */
function get_object_hash(object $object, bool $with_name = true, bool $rehash = false, bool $serialize = false): string
{
    return !$serialize ? Objects::getHash($object, $with_name, $rehash) : Objects::getSerializedHash($object);
}

/**
 * Set a var on an object.
 *
 * @param  object     $object
 * @param  int|string $var
 * @param  mixed      $value
 * @param  bool       $easy
 * @return void
 * @since  5.20
 */
function set_object_var(object $object, int|string $var, mixed $value, bool $easy = true): void
{
    // Yes property_exists(), cus of reflection exception for non-exists props.
    if ($easy || !property_exists($object, $var)) {
        $object->$var = $value;
        return;
    }

    $ref = new ReflectionProperty($object, (string) $var);
    $ref->setValue($object, $value);
}

/**
 * Get a var from an object.
 *
 * @param  object     $object
 * @param  int|string $var
 * @param  mixed|null $default
 * @param  bool       $easy
 * @return mixed
 * @since  5.20
 */
function get_object_var(object $object, int|string $var, mixed $default = null, bool $easy = true): mixed
{
    // No property_exists() cus of scope errors.
    if ($easy) {
        return $object->$var ?? $default;
    }

    $ref = new ReflectionProperty($object, (string) $var);

    @ $value = $ref->getValue($object);

    // Cannot get the (default) value when unset() applied on the property.
    if ($value === null && $ref->hasDefaultValue()) {
        $value = $ref->getDefaultValue();
    }

    return $value ?? $default;
}

/**
 * Check whether an argument was given in call silently (so func_get_arg() causes errors).
 *
 * @param  int|string $arg
 * @return bool
 * @since  5.28
 */
function func_has_arg(int|string $arg): bool
{
    $trace = debug_backtrace(0)[1];

    // Name check.
    if (is_string($arg)) {
        if (!empty($trace['args'])) {
            $ref = !empty($trace['class'])
                ? new ReflectionCallable([$trace['class'], $trace['function']])
                : new ReflectionCallable($trace['function']);

            return array_key_exists($ref->getParameter($arg)?->getPosition(), $trace['args']);
        }

        return false;
    }

    // Count & position check.
    return !empty($trace['args']) && array_key_exists($arg, $trace['args']);
}

/**
 * Check whether any arguments was given in call.
 *
 * @param  int|string ...$args
 * @return bool
 * @since  5.28
 */
function func_has_args(int|string ...$args): bool
{
    if ($args) {
        foreach ($args as $arg) {
            if (!func_has_arg($arg)) {
                return false;
            }
        }
        return true;
    }

    $trace = debug_backtrace(0)[1];

    // Count check.
    return !empty($trace['args']);
}

/**
 * Check whether given array is a list array.
 *
 * @param  mixed $var
 * @param  bool  $strict
 * @return bool
 * @since  5.0
 */
function is_list(mixed $var): bool
{
    return is_array($var) && array_is_list($var);
}

/**
 * Check whether given input is a number.
 *
 * @param  mixed $var
 * @return bool
 * @since  5.0
 */
function is_number(mixed $var): bool
{
    return is_int($var) || is_float($var);
}

/**
 * Check whether given input is a GdImage.
 *
 * @param  mixed $var
 * @return bool
 * @since  5.0
 */
function is_image(mixed $var): bool
{
    return $var && ($var instanceof GdImage);
}

/**
 * Check whether given input is a stream.
 *
 * @param  mixed $var
 * @return bool
 * @since  5.0
 */
function is_stream(mixed $var): bool
{
    return $var && (is_resource($var) && get_resource_type($var) == 'stream');
}

/**
 * Check whether given input is any type of given types.
 *
 * @param  mixed     $var
 * @param  string ...$types
 * @return bool
 * @since  5.0
 */
function is_type_of(mixed $var, string ...$types): bool
{
    foreach ($types as $type) {
        $type = strtolower($type);
        if (match ($type) {
            'list'     => is_list($var),     'number'   => is_number($var),
            'image'    => is_image($var),    'stream'   => is_stream($var),
            'iterable' => is_iterable($var), 'callable' => is_callable($var),
            'scalar'   => is_scalar($var),   'mixed'    => true,
            // @note Required for objects/all below.
            'object'   => is_object($var),
            // All others.
            default    => strtolower(get_type($var)) === $type
        }) {
            return true;
        }
    }
    return false;
}

/**
 * Check whether given search value equals to any of given values with strict comparison.
 *
 * @param  mixed    $value
 * @param  mixed ...$values
 * @return bool
 * @since  5.31
 */
function is_equal_of(mixed $value, mixed ...$values): bool
{
    $search_value = $value;
    foreach ($values as $value) {
        if ($search_value === $value) {
            return true;
        }
    }
    return false;
}

/**
 * Check whether given class is any type of given class(es).
 *
 * @param  object|string    $class
 * @param  object|string ...$classes
 * @return bool
 * @since  5.31
 */
function is_class_of(string|object $class, string|object ...$classes): bool
{
    $class1 = get_class_name($class);
    foreach ($classes as $class2) {
        $class2 = get_class_name($class2);
        if (is_a($class1, $class2, true)) {
            return true;
        }
    }
    return false;
}

/**
 * Check whether given class method is callable (exists & public).
 *
 * @param  string|object   $class
 * @param  string          $method
 * @param  bool            $static
 * @param  Reflector|null &$ref
 * @return bool
 * @since  5.0
 */
function is_callable_method(string|object $class, string $method, bool $static = false, Reflector &$ref = null): bool
{
    if (method_exists($class, $method)) {
        $ref = new ReflectionMethod($class, $method);
        return $static ? $ref->isPublic() && $ref->isStatic() : $ref->isPublic();
    }
    return false;
}

/**
 * Check empty state(s) of given input(s).
 *
 * @param  mixed    $var
 * @param  mixed ...$vars
 * @return bool
 * @since  4.0 Added back, 5.0 Moved from sugars/is.
 */
function is_empty(mixed $var, mixed ...$vars): bool
{
    foreach ([$var, ...$vars] as $var) {
        if (empty($var)) {
            return true;
        }
        if ((is_string($var) || is_array($var) || is_object($var))
            && !size($var)) {
            return true;
        }
    }

    return false;
}

/**
 * Check whether given input is true.
 *
 * @param  mixed $var
 * @return bool
 * @since  3.5, 5.6 Moved from sugars/is.
 */
function is_true(mixed $var): bool
{
    return ($var === true);
}

/**
 * Check whether given input is false.
 *
 * @param  mixed $var
 * @return bool
 * @since  3.5, 5.6 Moved from sugars/is.
 */
function is_false(mixed $var): bool
{
    return ($var === false);
}
