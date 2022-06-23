<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\{Util, Arrays, Objects, Numbers, Strings};
use froq\util\misc\System;

// Load base stuff.
require 'sugars-const.php';
require 'sugars-class.php';
require 'sugars-function.php';

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
 * Quick array/object (with "x:1, y:2" notation).
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
 * Quick array/object export (with "x:$x, y:$y" notation).
 */
function qx(array|object $iter, &...$vars): int
{
    return array_export((array) $iter, ...$vars);
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
 * Filter, with some options.
 *
 * @param  array         $array
 * @param  callable|null $func
 * @param  bool          $recursive
 * @param  bool          $use_keys
 * @param  bool          $keep_keys
 * @return array
 * @since  3.0, 5.0
 */
function filter(array $array, callable $func = null, bool $recursive = false, bool $use_keys = false, bool $keep_keys = true): array
{
    return Arrays::filter($array, $func, $recursive, $use_keys, $keep_keys);
}

/**
 * Map, with some options.
 *
 * @param  array                 $array
 * @param  callable|string|array $func
 * @param  bool                  $use_keys
 * @param  bool                  $keep_keys
 * @return array
 * @since  3.0, 5.0
 */
function map(array $array, callable|string|array $func, bool $recursive = false, bool $use_keys = false, bool $keep_keys = true): array
{
    return Arrays::map($array, $func, $recursive, $use_keys, $keep_keys);
}

/**
 * Reduce, with right option.
 *
 * @param  array         $array
 * @param  mixed         $carry
 * @param  callable|null $func
 * @param  bool          $right
 * @return mixed
 * @since  4.0, 5.0
 */
function reduce(array $array, mixed $carry, callable $func = null, bool $right = false): mixed
{
    return Arrays::reduce($array, $carry, $func, $right);
}

/**
 * Get size (length/count).
 *
 * @param  mixed<string|countable|object|null> $var
 * @return int
 * @since  3.0, 5.0
 */
function size(mixed $var): int
{
    // Speed up, a bit..
    if ($var === null || $var === '' || $var === []) {
        return 0;
    }

    return match (true) {
        is_string($var)    => mb_strlen($var),
        is_countable($var) => count($var),
        is_object($var)    => count(get_object_vars($var)),
        default            => 0
    };
}

/**
 * Pad an array or string.
 *
 * @param  array|string $input
 * @param  int          $length
 * @param  mixed|null   $pad
 * @return array|string
 * @since  5.0
 */
function pad(array|string $input, int $length, mixed $pad = null): array|string
{
    return is_array($input) ? array_pad($input, $length, $pad)
         : str_pad($input, $length, strval($pad ?? ' '));
}

/**
 * Chunk an array or string.
 *
 * @param  array|string $input
 * @param  int          $length
 * @param  bool         $keep_keys
 * @return array
 * @since  5.0
 */
function chunk(array|string $input, int $length, bool $keep_keys = false): array
{
    return is_array($input) ? array_chunk($input, $length, $keep_keys)
         : str_chunk($input, $length, join: false);
}

/**
 * Concat an array or string.
 *
 * @param  array|string    $input
 * @param  mixed        ...$inputs
 * @return array|string
 * @since  4.0, 5.0
 */
function concat(array|string $input, mixed ...$inputs): array|string
{
    return is_array($input) ? array_concat($input, ...$inputs)
         : str_concat($input, ...$inputs);
}

/**
 * Slice an array or string.
 *
 * @param  array|string $input
 * @param  int          $start
 * @param  int|null     $end
 * @param  bool         $keep_keys
 * @return array|string
 * @since  3.0, 4.0, 5.0
 */
function slice(array|string $input, int $start, int $end = null, bool $keep_keys = false): array|string
{
    return is_array($input) ? array_slice($input, $start, $end, $keep_keys)
         : mb_substr($input, $start, $end);
}

/**
 * Splice an array or string.
 *
 * @param  array|string       $input
 * @param  int                $start
 * @param  int|null           $end
 * @param  array|string|null  $replace
 * @param  array|string|null &$replaced
 * @return array|string
 * @since  6.0
 */
function splice(array|string $input, int $start, int $end = null, array|string $replace = null, array|string &$replaced = null): array|string
{
    $split  = is_array($input) ? $input : mb_str_split($input);
    $splice = array_splice($split, $start, $end, (array) $replace);

    $replaced = null;
    if ($splice) {
        $replaced = is_array($input) ? $splice : join($splice);
    }

    return is_array($input) ? $split : join($split);
}

/**
 * Split a string, with unicode style.
 *
 * @param  string            $separator
 * @param  string            $input
 * @param  int|null          $limit
 * @param  int|null          $flags
 * @param  RegExpError|null &$error
 * @return array
 * @since  5.0
 */
function split(string $separator, string $input, int $limit = null, int $flags = null, RegExpError &$error = null): array
{
    if ($separator == '') {
        // Safe for binary strings.
        $ret = strlen($input) == mb_strlen($input)
             ? str_split($input) : mb_str_split($input);

        // Mind limit option.
        if ($limit && $limit > 0) {
            $res = array_slice($ret, $limit - 1); // Rest.
            $ret = array_slice($ret, 0, $limit - 1);
            $res && $ret[] = join($res);
        }
    } else {
        // Escape null bytes, delimiter & special char typos.
        $separator = strlen($separator) == 1 ? preg_quote($separator, '~')
             : str_replace(["\0", '~'], ['\0', '\~'], $separator);

        $ret = preg_split(
            '~'. $separator .'~u', $input,
            limit: ($limit ?? -1),
            flags: ($flags |= PREG_SPLIT_NO_EMPTY)
        ) ?: [];
    }

    // Prevent 'undefined index ..' error.
    if ($limit && $limit > count($ret)) {
        $ret = array_pad($ret, $limit, null);
    }

    // Fill error message if requested.
    if (func_num_args() == 5) {
        $message = preg_error_message($code, 'preg_split');
        $message && $error = new RegExpError($message, $code);
    }

    return $ret;
}

/**
 * Unsplit, a fun function.
 *
 * @param  string $separator
 * @param  array  $input
 * @return string
 * @since  3.0, 5.0
 */
function unsplit(string $separator, array $input): string
{
    return join($separator, $input);
}

/**
 * Strip a string, with RegExp (~) option.
 *
 * @param  string $input
 * @param  string $characters
 * @return string
 * @since  3.0, 5.0
 */
function strip(string $input, string $characters = ''): string
{
    if ($characters == '') {
        return trim($input);
    }

    // RegExp: only ~..~ patterns accepted.
    if (strlen($characters) >= 3 && $characters[0] == '~') {
        $ruls = substr($characters, 1, ($pos = strrpos($characters, '~')) - 1);
        $mods = substr($characters, $pos + 1);
        return preg_replace(sprintf('~^%s|%s$~%s', $ruls, $ruls, $mods), '', $input);
    }

    return trim($input, $characters);
}

/**
 * Replace something(s) on an array or string.
 *
 * @param  string|array          $input
 * @param  string|array          $search
 * @param  string|array|callable $replace
 * @param  bool                  $icase
 * @param  int                   $limit
 * @return string|array
 * @since  3.0, 6.0
 */
function replace(string|array $input, string|array $search, string|array|callable $replace, bool $icase = false, int $limit = -1): string|array
{
    if (is_string($input) && is_string($search)) {
        // RegExp: only ~..~ patterns accepted.
        if (strlen($search) >= 3 && $search[0] == '~') {
            return is_callable($replace)
                 ? preg_replace_callback($search, $replace, $input, $limit)
                 : preg_replace($search, $replace, $input, $limit);
        }
    }

    return $icase ? str_ireplace($search, $replace, $input)
                  : str_replace($search, $replace, $input);
}

/**
 * Grep, actually grabs something from given input.
 *
 * @param  string $pattern
 * @param  string $input
 * @param  bool   $named
 * @return string|array|null
 * @since  3.0, 5.0
 */
function grep(string $pattern, string $input, bool $named = false): string|array|null
{
    preg_match($pattern, $input, $match, PREG_UNMATCHED_AS_NULL);

    if (isset($match[1])) {
        $ret = $match[1];

        // For named capturing groups.
        if ($named && $ret) {
            $ret = array_filter($ret, 'is_string', 2);
        }

        return $ret;
    }

    return null;
}

/**
 * Grep all, actually grabs somethings from given input.
 *
 * @param  string $pattern
 * @param  string $input
 * @param  bool   $named
 * @param  bool   $uniform
 * @return array<string|null>|null
 * @since  3.15, 5.0
 */
function grep_all(string $pattern, string $input, bool $named = false, bool $uniform = false): array|null
{
    preg_match_all($pattern, $input, $match, PREG_UNMATCHED_AS_NULL);

    if (isset($match[1])) {
        unset($match[0]); // Drop input.

        $ret = [];

        if (count($match) == 1) {
            $ret = $match[1];
        } else {
            // Reduce empty matches.
            $ret = array_apply($match, fn($m) => (
                is_array($m) && count($m) == 1 ? $m[0] : $m
            ));

            // Useful for '~href="(.+?)"|">(.+?)</~' etc.
            if ($uniform) {
                foreach ($ret as &$re) {
                    if (is_array($re)) {
                        $re = array_filter($re, 'size');
                        if (count($re) == 1) {
                            $re = current($re);
                        }
                    }
                } unset($re);
            }

            // Reset keys (to 0-N).
            $ret = array_slice($ret, 0);
        }

        // Drop empty stuff.
        $ret = array_filter($ret, 'size');

        // For named capturing groups.
        if ($named && $ret) {
            $ret = array_filter($ret, 'is_string', 2);
        }

        return $ret;
    }

    return null;
}

/**
 * Convert base (original source: http://stackoverflow.com/a/4668620/362780).
 *
 * @param  int|string $input
 * @param  int|string $from  From base or characters.
 * @param  int|string $to    To base or characters.
 * @return string|null
 * @since  4.0, 4.25
 */
function convert_base(int|string $input, int|string $from, int|string $to): string|null
{
    // Try to use speed/power of GMP.
    if (is_int($from) && is_int($to) && extension_loaded('gmp')) {
        return gmp_strval(gmp_init($input, $from), $to);
    }

    // Using base62 characters.
    $characters = BASE62_ALPHABET;

    if (is_int($from)) {
        if ($from < 2 || $from > 62) {
            trigger_error(sprintf('%s(): Invalid base for from characters, min=2 & max=62', __function__));
            return null;
        }
        $from = strcut($characters, $from);
    }
    if (is_int($to)) {
        if ($to < 2 || $to > 62) {
            trigger_error(sprintf('%s(): Invalid base for to characters, min=2 & max=62', __function__));
            return null;
        }
        $to = strcut($characters, $to);
    }

    $input = strval($input);
    if (!$input || $from == $to) {
        return $input;
    }

    [$input_length, $from_base_length, $to_base_length]
        = [strlen($input), strlen($from), strlen($to)];

    $numbers = [];
    for ($i = 0; $i < $input_length; $i++) {
        $numbers[$i] = strpos($from, $input[$i]);
    }

    $ret = '';
    $old_length = $input_length;

    do {
        $new_length = $div = 0;

        for ($i = 0; $i < $old_length; $i++) {
            $div = ($div * $from_base_length) + $numbers[$i];
            if ($div >= $to_base_length) {
                $numbers[$new_length++] = (int) ($div / $to_base_length);
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
 * @param  string      $input
 * @param  string|int  $case
 * @param  string|null $exploder
 * @param  string|null $imploder
 * @return string|null
 * @since  4.26
 */
function convert_case(string $input, string|int $case, string $exploder = null, string $imploder = null): string|null
{
    if (is_string($case)) {
        $case_value = get_constant_value('CASE_' . strtoupper($case));
        if ($case_value === null) {
            trigger_error(sprintf('%s(): Invalid case %s, use lower,upper,title,dash,snake,camel', __function__, $case));
            return null;
        }

        $case = $case_value;
    }

    // Check valid cases.
    if (!in_array($case, [CASE_LOWER, CASE_UPPER, CASE_TITLE, CASE_DASH, CASE_SNAKE, CASE_CAMEL], true)) {
        trigger_error(sprintf('%s(): Invalid case %s, use a case from 0..5 range', __function__, $case));
        return null;
    }

    if ($case == CASE_LOWER) {
        return mb_strtolower($input);
    } elseif ($case == CASE_UPPER) {
        return mb_strtoupper($input);
    }

    // Set default split char.
    $exploder = ($exploder !== null && $exploder !== '') ? $exploder : ' ';

    return match ($case) {
        CASE_DASH  => implode('-', explode($exploder, mb_strtolower($input))),
        CASE_SNAKE => implode('_', explode($exploder, mb_strtolower($input))),
        CASE_TITLE => implode($imploder ?? $exploder, array_map(
            fn($s) => mb_ucfirst(trim($s)),
            explode($exploder, mb_strtolower($input))
        )),
        CASE_CAMEL => mb_lcfirst(implode('', array_map(
            fn($s) => mb_ucfirst(trim($s)),
            explode($exploder, mb_strtolower($input))
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
 * @param  bool          $scope_check
 * @param  bool          $assoc
 * @return array|null
 * @since  4.0
 */
function get_class_constants(string|object $class, bool $scope_check = true, bool $assoc = true): array|null
{
    if ($scope_check) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            $all = ($caller_class === Objects::getName($class));
        }
    }

    return Objects::getConstantValues($class, ($all ?? !$scope_check), $assoc);
}

/**
 * Get properties of given class/object, or return null if no such class.
 *
 * @param  string|object $class
 * @param  bool          $scope_check
 * @param  bool          $assoc
 * @return array|null
 * @since  4.0
 */
function get_class_properties(string|object $class, bool $scope_check = true, bool $assoc = true): array|null
{
    if ($scope_check) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            $all = ($caller_class === Objects::getName($class));
        }
    }

    return Objects::getPropertyValues($class, ($all ?? !$scope_check), $assoc);
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

    return first(array_filter(array_keys(get_defined_constants(), $value, true),
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
 * @param  bool          $upper
 * @return bool
 * @since  4.0
 */
function constant_exists(string|object $class, string $name, bool $scope_check = false, bool $upper = false): bool
{
    $class = Objects::getName($class);
    $upper && $name = strtoupper($name);

    if ($scope_check) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            return ($caller_class === $class || is_class_of($class, $caller_class))
                && Objects::hasConstant($class, $name);
        }
        return defined($class .'::'. $name);
    }

    return defined($class .'::'. $name) || Objects::hasConstant($class, $name);
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
 * @return mixed|null
 * @since  4.17
 */
function get_error(string $field = null): mixed
{
    $error = error_get_last();
    if (!$error) {
        return null;
    }

    // Put in a new order.
    $error = array_select($error, ['type', 'file', 'line', 'message', 'function'], combine: true);

    // Separate message & function if available.
    if ($pos = strpos($error['message'], '):')) {
        $error['function'] = substr($error['message'], 0, $pos - 1);
        $error['message']  = ucfirst(substr($error['message'], $pos + 3));
    }

    return $field ? $error[$field] ?? null : $error;
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
    if ($length < 14  && $base < 17) {
        trigger_error(sprintf('%s(): Invalid length, min=14', __function__));
        return null;
    }
    if ($base < 10 || $base > 62) {
        trigger_error(sprintf('%s(): Invalid base, min=10 & max=62', __function__));
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
    if ($base < 17 && $length < 14) {
        trigger_error(sprintf('%s(): Invalid length, min=14', __function__));
        return null;
    }
    if ($base < 10 || $base > 62) {
        trigger_error(sprintf('%s(): Invalid base, min=10 & max=62', __function__));
        return null;
    }

    $ret = '';

    while (strlen($ret) < $length) {
        $id = bin2hex(random_bytes(4));

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
 * @param  string|bool|null $check True or "file", "dir".
 * @return string|null
 * @since  4.0
 */
function get_real_path(string $path, string|bool $check = null): string|null
{
    if (trim($path) == '') {
        return null;
    }

    // NULL-bytes issue.
    if (str_contains($path, "\0")) {
        $path = str_replace("\0", "\\0", $path);
    }

    // Validate existence of file/directory or file only.
    static $check_path; $check_path ??= fn($c, $p) => (
        $c === true ? file_exists($p) : ($c === 'file' ? is_file($p) : is_dir($p))
    );

    if ($ret = realpath($path)) {
        if ($check && !$check_path($check, $ret)) {
            return null;
        }
        return $ret;
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

    if ($check && !$check_path($check, $ret)) {
        return null;
    }

    // Normalize.
    if ($ret) {
        // Drop repeating separators.
        $ret = preg_replace(
            '~(['. preg_quote(PATH_SEPARATOR . DIRECTORY_SEPARATOR) .'])\1+~',
            '\1', $ret
        );

        // Drop ending slashes.
        if ($ret != PATH_SEPARATOR && $ret != DIRECTORY_SEPARATOR) {
            $ret = chop($ret, PATH_SEPARATOR . DIRECTORY_SEPARATOR);
        }

        // Fix leading slash for win.
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

    // Drop "" fields, put in order.
    $info = array_filter($info, 'strlen');
    $info = array_select($info, ['dirname', 'basename', 'filename', 'extension'], combine: true);

    // Really really, real path.
    $realpath = realpath($path);

    $ret = ['path' => $path, 'realpath' => $realpath, 'type' => $realpath ? filetype($path) : null] + $info;

    if (is_dir($path)) {
        $ret['filename'] = $ret['extension'] = null;
    } else {
        $ret['filename']  = file_name($path, false);
        $ret['extension'] = file_extension($path, false);
    }

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
 * @param  int         $options
 * @param  int         $limit
 * @param  int|null    $index
 * @param  string|null $field
 * @param  int|null    $slice
 * @return mixed|null
 * @since  4.0
 */
function get_trace(int $options = 0, int $limit = 0, int $index = null, string $field = null, int $slice = null): mixed
{
    $stack = debug_backtrace($options, limit: $limit ? $limit + 1 : 0);

    // Drop self.
    array_shift($stack);

    // When slice wanted (@internal).
    $slice && $stack = array_slice($stack, $slice);

    foreach ($stack as $i => $trace) {
        $trace = [
            // Index.
            '#' => $i,
            // For "[internal function]", "{closure}" stuff.
            'file' => $trace['file'] ?? null,
            'line' => $trace['line'] ?? null,
        ] + $trace + [
            // Additions.
            'callee' => $trace['function'] ?? null,
            'caller' => null, 'callerClass' => null, 'callerMethod' => null,
        ];

        if (isset($trace['file'], $trace['line'])) {
            $trace['callPath'] = $trace['file'] . ':' . $trace['line'];
        } else {
            $trace['callPath'] = '[internal function]:';
        }

        if (isset($trace['class'])) {
            $trace['method']     = $trace['function'];
            $trace['methodType'] = ($trace['type']  == '::') ? 'static' : 'non-static';
        }
        if (isset($stack[$i + 1]['function'])) {
            $trace['caller'] = $stack[$i + 1]['function'];
        }
        if (isset($stack[$i + 1]['class'])) {
            $trace['callerClass']  = $stack[$i + 1]['class'];
            $trace['callerMethod'] = sprintf('%s::%s', $stack[$i + 1]['class'], $stack[$i + 1]['function']);
        }

        $stack[$i] = $trace;
    }

    return is_null($index) ? $stack : ($stack[$index][$field] ?? $stack[$index] ?? null);
}

/**
 * Create a DateTime instance with/without when & where options.
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
 * Get current Unix timestamp with microseconds as float or string.
 *
 * @param  bool $string
 * @return float|string
 * @since  4.0
 */
function utime(bool $string = false): float|string
{
    $time = microtime(true);

    return !$string ? $time : sprintf('%.6F', $time);
}

/**
 * Get current Unix timestamp with milliseconds as int or string.
 *
 * @param  bool $string
 * @return int|string
 * @since  5.0
 */
function ustime(bool $string = false): int|string
{
    $time = microtime(true) * 1000;

    return !$string ? (int) $time : (string) $time;
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
    if (preg_match_all('~([+-]?\d+)([smhDMY])~', $format, $match)) {
        $format_list = null;
        [, $numbers, $formats] = $match;

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
        // Multi, eg: LC_ALL.
        if (str_contains($ret, ';')) {
            foreach (split(';', $ret) as $re) {
                [$name, $value] = split('=', $re, 2);
                $tmp[] = ['name' => $name, 'value' => $value,
                          'category' => get_constant_value($name)];
            }
        } else {
            // Single, eg: LC_TIME.
            $tmp = ['name' => ($name = get_constant_name($category, 'LC_')),
                    'value' => $ret, 'category' => $name ? $category : null];
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
        $replace = '';
    } else {
        $replace = array_fill(0, count($pattern), '');
    }

    return preg_replace($pattern, $replace, $subject, $limit ?? -1, $count);
}

/**
 * Same as preg_match() but for only named capturing groups.
 *
 * @param  string  $pattern
 * @param  string  $subject
 * @param  array  &$match
 * @param  int     $flags
 * @param  int     $offset
 * @return int|false
 */
function preg_match_names(string $pattern, string $subject, array &$match, int $flags = 0, int $offset = 0): int|false
{
    $res = preg_match($pattern, $subject, $match, $flags, $offset);

    // Select string/named keys.
    if ($res !== false) {
        $match = array_filter($match, 'is_string', 2);
    }

    return $res;
}

/**
 * Same as preg_match_all() but for only named capturing groups.
 *
 * @param  string  $pattern
 * @param  string  $subject
 * @param  array  &$match
 * @param  int     $flags
 * @param  int     $offset
 * @return int|false
 */
function preg_match_all_names(string $pattern, string $subject, array &$match, int $flags = 0, int $offset = 0): int|false
{
    $res = preg_match_all($pattern, $subject, $match, $flags, $offset);

    // Select string/named keys.
    if ($res !== false) {
        $match = array_filter($match, 'is_string', 2);
    }

    return $res;
}

/**
 * Get current value of given array (for the sake of current()) or given key's value if exists.
 *
 * @param  array           $array
 * @param  int|string|null $key
 * @param  mixed|null      $default
 * @return mixed
 * @since  5.35
 */
function value(array $array, int|string $key = null, mixed $default = null): mixed
{
    if (func_num_args() > 1) {
        return $array[$key] ?? $default;
    }
    return $array ? current($array) : null; // No falses.
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
 * @param  array             $array
 * @param  callable|int|null $func
 * @param  int               $flags
 * @param  bool|null         $assoc
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
 * @param  int|null &$code
 * @param  bool      $format
 * @param  bool      $extract
 * @param  bool      $clear
 * @return string|null
 * @since  4.17
 */
function error_message(int &$code = null, bool $format = false, bool $extract = false, bool $clear = false): string|null
{
    $error = error_get_last();
    if (!$error) {
        return null;
    }

    $code = $error['type'];
    $clear && error_clear($code);

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
    // Extract message only dropping caused function.
    elseif ($extract && ($pos = strpos($error['message'], '):'))) {
        return ucfirst(substr($error['message'], $pos + 3));
    }

    return $error['message'];
}

/**
 * Get JSON last error message with code if any, instead "No error".
 *
 * @param  int|null &$code
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
 * @param  int|null    &$code
 * @param  string|null  $func
 * @param  bool         $clear
 * @return string|null
 * @since  4.17
 */
function preg_error_message(int &$code = null, string $func = null, bool $clear = false): string|null
{
    if ($func === null) {
        return ($code = preg_last_error()) ? preg_last_error_msg() : null;
    }

    // Somehow code disappears when error_get_last() called.
    $error_code    = preg_last_error();
    $error_message = error_message(clear: $clear);

    if ($error_message && strsrc($error_message, $func ?: 'preg_')) {
        $message = strsub($error_message, strpos($error_message, '):') + 3);
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
        trigger_error(sprintf('%s(): Invalid length, min=1', __function__));
        return null;
    } elseif ($base < 2 || $base > 62) {
        trigger_error(sprintf('%s(): Invalid base, min=2 & max=62', __function__));
        return null;
    }

    $max = $base - 1;
    $ret = '';

    srand();
    while ($length--) {
        $ret .= BASE62_ALPHABET[rand(0, $max)];
    }

    return $ret;
}

/**
 * Generate a random UUID/GUID, optionally with timestamp prefix.
 *
 * @param  bool $dashed
 * @param  bool $timed
 * @param  bool $guid
 * @param  bool $upper
 * @return string
 * @since  5.0
 */
function uuid(bool $dashed = true, bool $timed = false, bool $guid = false, bool $upper = false): string
{
    $bytes = !$timed ? random_bytes(16)               // Full 16-random bytes.
        : hex2bin(dechex(time())) . random_bytes(12); // Time bin prefix & 12-random bytes.

    // Add signs: 4 (version) & 8, 9, A, B, but GUID doesn't use them.
    if (!$guid) {
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
    }

    $ret = uuid_format(bin2hex($bytes));

    $dashed || $ret = str_replace('-', '', $ret);
    $upper  && $ret = strtoupper($ret);

    return $ret;
}

/**
 * Generate a random UUID/GUID hash, optionally with timestamp prefix.
 *
 * @param  int  $length
 * @param  bool $format
 * @param  bool $timed
 * @param  bool $guid
 * @param  bool $upper
 * @return string|null
 * @since  5.0
 */
function uuid_hash(int $length = 32, bool $format = false, bool $timed = false, bool $guid = false, bool $upper = false): string|null
{
    $algo = [32 => 'md5', 40 => 'sha1', 64 => 'sha256', 16 => 'fnv1a64'][$length] ?? null;

    if (!$algo) {
        trigger_error(sprintf('%s(): Invalid length `%s` [valids: 32,40,64,16]', __function__, $length));
        return null;
    }

    $ret = hash($algo, uuid(true, $timed, $guid));

    $format && $ret = uuid_format($ret);
    $upper  && $ret = strtoupper($ret);

    return $ret;
}

/**
 * Format given input as UUID/GUID.
 *
 * @param  string $input
 * @return string|null
 * @since  5.0
 */
function uuid_format(string $input): string|null
{
    if (strlen($input) != 32 || !ctype_xdigit($input)) {
        trigger_error(sprintf('%s(): Format for only 32-length UUIDs/GUIDs', __function__));
        return null;
    }

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($input, 4));
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

    // Convert special formats (quoted strings, int).
    $format = str_replace(['%q', '%Q', '%i'], ["'%s'", '"%s"', '%d'], $format);

    // Convert special formats (type, bool, array, upper, lower, escape).
    if (preg_test('~%[tbaAULS]~', $format)) {
        // Must find all for a proper param index re-set.
        foreach (grep_all('~(%[a-zAULS])~', $format) as $i => $specifier) {
            switch ($specifier) {
                case '%t': // Types.
                    $format = substr_replace($format, '%s', strpos($format, '%t'), 2);
                    if (array_key_exists($i, $params)) {
                        $params[$i] = get_type($params[$i]);
                    }
                    break;
                case '%b': // Bools (as stringified bools, not 0/1).
                    $format = substr_replace($format, '%s', strpos($format, '%b'), 2);
                    if (array_key_exists($i, $params)) {
                        $params[$i] = format_bool($params[$i]);
                    }
                    break;
                case '%a': case '%A': // Arrays (as joinified items).
                    $format = substr_replace($format, '%s', strpos($format, $specifier), 2);
                    if (array_key_exists($i, $params)) {
                        $separator  = ($specifier == '%a') ? ',' : ', ';
                        $params[$i] = join($separator, (array) $params[$i]);
                    }
                    break;
                case '%U': case '%L': // Upper/Lower.
                    $format = substr_replace($format, '%s', strpos($format, $specifier), 2);
                    if (array_key_exists($i, $params)) {
                        $function   = ($specifier == '%U') ? 'upper' : 'lower';
                        $params[$i] = $function((string) $params[$i]);
                    }
                    break;
                case '%S': // Escape (NULL-bytes only).
                    $format = substr_replace($format, '%s', strpos($format, $specifier), 2);
                    if (array_key_exists($i, $params)) {
                        $params[$i] = str_replace("\0", "\\0", (string) $params[$i]);
                    }
                    break;
            }
        }
    }

    return vsprintf($format, $params);
}

/**
 * Format an input as bool (yes).
 *
 * @param  mixed $input
 * @return string
 * @since  5.31
 */
function format_bool(mixed $input): string
{
    return $input ? 'true' : 'false';
}

/**
 * Format an input as number.
 *
 * @param  int|float|string $input
 * @param  int|bool|null    $decimals
 * @param  string|null      $decimal_separator
 * @param  string|null      $thousand_separator
 * @return string|null
 * @since  5.31
 */
function format_number(int|float|string $input, int|bool|null $decimals = 0, string $decimal_separator = null, string $thousand_separator = null): string|null
{
    if (is_string($input)) {
        if (!is_numeric($input)) {
            trigger_error(sprintf('%s(): Invalid non-numeric input', __function__));
            return null;
        }

        $input += 0;
    }

    $export = var_export($input, true);

    // Auto-detect decimals.
    if (is_true($decimals)) {
        $decimals = strlen(stracut($export, '.'));
    }

    // Prevent corruptions.
    if ($decimals > PRECISION) {
        $decimals = PRECISION;
    }

    $ret = number_format($input, (int) $decimals, $decimal_separator, $thousand_separator);

    // Append ".0" for eg: 1.0 & upper NAN/INF.
    if (!$decimals && !is_int($input) && strlen($export) == 1) {
        $ret .= '.0';
    } elseif ($ret == 'inf' || $ret == 'nan') {
        $ret = strtoupper($ret);
    }

    return $ret;
}

/**
 * Translate given input to slugified output.
 *
 * @param  string $input
 * @param  string $preserve
 * @param  string $replace
 * @return string
 * @since  5.0
 */
function slug(string $input, string $preserve = '', string $replace = '-'): string
{
    static $map;
    $map ??= require 'statics/slug-map.php';

    $preserve && $preserve = preg_quote($preserve, '~');
    $replace  || $replace  = '-';

    $out = preg_replace(['~[^\w'. $preserve . $replace .']+~', '~['. $replace .']+~'],
        $replace, strtr($input, $map));

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

    // Unique stack.
    $uni = [];

    while ($length--) {
        $item = Numbers::random($min, $max, $precision);

        // Provide unique-ness.
        while ($unique && in_array($item, $ret, true) && !in_array($item, $uni, true)) {
            $item = $uni[] = Numbers::random($min, $max, $precision);
        }

        $ret[] = $item;
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
 * @param  bool   $with_rehash
 * @param  bool   $serialized
 * @return string
 * @since  5.25
 */
function get_object_hash(object $object, bool $with_name = true, bool $with_rehash = false, bool $serialized = false): string
{
    return !$serialized ? Objects::getHash($object, $with_name, $with_rehash) : Objects::getSerializedHash($object);
}

/**
 * Set object vars.
 *
 * @param  object          $object
 * @param  object|iterable $vars
 * @return object
 * @since  6.0
 */
function set_object_vars(object $object, object|iterable $vars): object
{
    foreach ($vars as $name => $value) {
        $ref = new ReflectionProperty($object, $name);
        $ref->setValue($object, $value);
    }

    return $object;
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
 * Get an ini directive with bool option.
 *
 * @param  string     $name
 * @param  mixed|null $default
 * @param  bool       $bool
 * @return mixed|null
 * @since  4.0, 6.0
 */
function ini(string $name, mixed $default = null, bool $bool = false): mixed
{
    return System::iniGet($name, $default, $bool);
}

/**
 * Get an env/server variable.
 *
 * @param  string     $name
 * @param  mixed|null $default
 * @param  bool       $server
 * @return mixed|null
 * @since  4.0, 6.0
 */
function env(string $name, mixed $default = null, bool $server = true): mixed
{
    return System::envGet($name, $default, $server);
}

/**
 * Check whether given array is a list array.
 *
 * @param  mixed $var
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
    return $var && is_resource($var) && get_resource_type($var) === 'stream';
}

/**
 * Check whether given input is an iterator.
 *
 * @param  mixed $var
 * @return bool
 * @since  6.0
 */
function is_iterator(mixed $var): bool
{
    return $var && ($var instanceof Traversable);
}

/**
 * Check whether given input is an enum.
 *
 * @param  mixed $var
 * @return bool
 * @since  6.0
 */
function is_enum(mixed $var): bool
{
    return is_object($var) && enum_exists($var::class, false);
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
    $var_type = strtolower(get_debug_type($var));

    // Multiple at once.
    if ($types && str_contains($types[0], '|')) {
        $types = explode('|', $types[0]);

        // A little bit faster than foreach().
        if (in_array($var_type, $types, true)) {
            return true;
        }
    }

    foreach ($types as $type) {
        $type = strtolower($type);
        if (match ($type) {
            // Any/mixed.
            'any', 'mixed' => 1,

            // Sugar stuff.
            'list', 'number', 'image', 'stream', 'iterator', 'enum'
                => ('is_' . $type)($var),

            // Primitive & internal stuff.
            'int', 'float', 'string', 'bool', 'array', 'object', 'null',
            'numeric', 'scalar', 'resource', 'iterable', 'callable', 'countable',
                => ('is_' . $type)($var),

            // All others.
            default => ($var_type === $type) || ($var instanceof $type)
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
 * Check empty state(s) of given input(s).
 *
 * @param  mixed    $var
 * @param  mixed ...$vars
 * @return bool
 * @since  4.0, 5.0
 */
function is_empty(mixed $var, mixed ...$vars): bool
{
    foreach ([$var, ...$vars] as $var) {
        if (empty($var)) {
            return true;
        }

        if (is_object($var) && size($var) == 0) {
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
 * @since  3.5, 5.6
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
 * @since  3.5, 5.6
 */
function is_false(mixed $var): bool
{
    return ($var === false);
}
