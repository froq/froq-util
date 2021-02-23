<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\{Arrays, Objects, Numbers, Strings};

// Load constants.
defined('nil') || require 'sugars-constant.php';

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
    return ($value = array_pick($array, $key, null, $drop)) !== null;
}
function pluck(array &$array, int|string $key, &$value = null): bool {
    return ($value = array_pluck($array, $key, null)) !== null;
}

/**
 * Format for sprintf()/vsprintf().
 */
function format(string $in, $arg, ...$args): string {
    return is_array($arg) ? vsprintf($in, $arg) : sprintf($in, $arg, ...$args);
}

/**
 * The ever most wanted functions (finally come with 8.0, but without case option).
 * @alias of str_has(),str_has_prefix(),str_has_suffix()
 * @since 4.0, 5.0 Dropped ...$args calls due to speed issues.
 */
function strsrc(string $str, string $src, bool $icase = false): bool { // Search.
    return str_has($str, $src, $icase);
}
function strpfx(string $str, string $src, bool $icase = false): bool { // Search prefix.
    return str_has_prefix($str, $src, $icase);
}
function strsfx(string $str, string $src, bool $icase = false): bool { // Search suffix.
    return str_has_suffix($str, $src, $icase);
}

/**
 * Loving shorter stuffs?
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function upper(string $in): string { return mb_strtoupper($in); }
function lower(string $in): string { return mb_strtolower($in); }

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
    return (func_num_args() == 1) ? Arrays::filter($array)
                                  : Arrays::filter($array, $func, $keep_keys);
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
function map(array $array, callable $func, bool $keep_keys = true): array
{
    return Arrays::map($array, $func, $keep_keys);
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
 * Get size/count/length of given input.
 *
 * @param  any $in
 * @return int|null
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function size($in): int|null
{
    return match (true) {
        is_string($in)    => mb_strlen($in),
        is_countable($in) => count($in),
        is_object($in)    => count(get_object_vars($in)),
        default           => null // No valid input.
    };
}

/**
 * Concat an array or string.
 *
 * @param  array|string    $in
 * @param  array|string ...$ins
 * @return array|string
 * @since  4.0, 5.0 Moved from froq/fun.
 */
function concat(array|string $in, ...$ins): array|string
{
    return match (true) {
        is_array($in)  => array_append($in, ...$ins),
        is_string($in) => $in . join('', $ins)
    };
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
    return match (true) {
        is_array($in)  => array_chunk($in, $length, $keep_keys),
        is_string($in) => str_split($in, $length)
    };
}

/**
 * Slice an array or string.
 *
 * @param  array|string $in
 * @param  int          $start
 * @param  int|null     $end
 * @return array|string
 * @since  3.0, 4.0 Added back, 5.0 Moved from froq/fun.
 */
function slice(array|string $in, int $start, int $end = null): array|string
{
    return match (true) {
        is_array($in)  => array_slice($in, $start, $end),
        is_string($in) => mb_substr($in, $start, $end)
    };
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
 * Split a string, with RegExp (~) option.
 *
 * @param  string   $sep
 * @param  string   $in
 * @param  int|null $limit
 * @param  int|null $flags
 * @return array
 * @since  5.0 Moved from froq/fun.
 */
function split(string $sep, string $in, int $limit = null, int $flags = null): array
{
    if ($sep === '') {
        $ret = (array) preg_split('~~u', $in, -1, 1);
        if ($limit) { // As like as str_split(), but with Unicode.
            return array_map(fn($r) => join('', $r), array_chunk($ret, $limit));
        }
    } else {
        $flags ??= 1; // No empty: null or 1.

        // RegExp: only "~..~" patterns accepted.
        if ($sep[0] == '~' && strlen($sep) >= 3) {
            $ret = (array) preg_split($sep, $in, ($limit ?? -1), $flags);
        } else {
            $ret = (array) explode($sep, $in, ($limit ?? PHP_INT_MAX));
            $flags && $ret = array_filter($ret, 'strlen');
        }
    }

    // Plus: prevent 'undefined index ..' error.
    if ($limit && $limit != count($ret)) {
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
 * @return string|null
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function grep(string $in, string $pattern): string|null
{
    preg_match($pattern, $in, $match, PREG_UNMATCHED_AS_NULL);

    return $match[1] ?? null;
}

/**
 * Grep all, actually grabs somethings from given input.
 *
 * @param  string $in
 * @param  string $pattern
 * @param  bool   $uniform
 * @return array<string|null>|null
 * @since  3.15, 5.0 Moved from froq/fun.
 */
function grep_all(string $in, string $pattern, bool $uniform = false): array|null
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

        return $ret;
    }

    return null;
}

/**
 * Cut a string with given length.
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
 * Cut a string before given search position with/without given length, or return null if no search found.
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
 * Cut a string after given search position with/without given length, or return null if no search found.
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
    return !$icase ? str_ends_with($str, $src) : mb_strripos($str, $src) === (mb_strlen($str) - mb_strlen($src));
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

    srand(); // Ensure a new seed (@see https://wiki.php.net/rfc/object_scope_prng).

    return !$length ? str_shuffle($str) : mb_substr(str_shuffle($str), 0, $length);
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
    static $chars = ALPHABET;

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
 * @param  string|null $spliter
 * @param  string|null $joiner
 * @return string|null
 * @since  4.26
 */
function convert_case(string $in, int $case, string $spliter = null, string $joiner = null): string|null
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
    $spliter = ($spliter !== null && $spliter !== '') ? $spliter : ' ';

    return match ($case) {
        CASE_DASH  => implode('-', explode($spliter, mb_strtolower($in))),
        CASE_SNAKE => implode('_', explode($spliter, mb_strtolower($in))),
        CASE_TITLE => implode($joiner ?? $spliter, array_map(
            fn($s) => mb_ucfirst(trim($s)),
            explode($spliter, mb_strtolower($in))
        )),
        CASE_CAMEL => lcfirst(
            implode($joiner ?? '', array_map(
                fn($s) => mb_ucfirst(trim($s)),
                explode($spliter, mb_strtolower($in))
            ))
        ),
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
         : ($parents = class_parents($class1)) && (current($parents) === $class2);
}

/**
 * Get class name or short name.
 *
 * @param  string|object $class
 * @param  bool          $short
 * @return string
 * @since  5.0
 */
function get_class_name(string|object $class, bool $short = false): string
{
    return !$short ? Objects::getName($class) : Objects::getShortName($class);
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
 * Check a constant exists, or return null if no such class.
 *
 * @param  string|object $class
 * @param  string        $name
 * @param  bool          $scope_check
 * @return bool|null
 * @since  4.0
 */
function constant_exists(string|object $class, string $name, bool $scope_check = true): bool|null
{
    if ($scope_check) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            return ($caller_class === Objects::getName($class))
                && Objects::hasConstant($class, $name);
        }
        return defined(Objects::getName($class) .'::'. $name);
    }

    return Objects::hasConstant($class, $name);
}

/**
 * Get type with/without scalars option.
 *
 * @param  any  $var
 * @param  bool $scalars
 * @return string
 * @since  4.0
 */
function get_type($var, bool $scalars = false): string
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
 * @return string|null
 * @since  4.0
 */
function get_uniqid(int $length = 14, int $base = 16, bool $hrtime = false): string|null
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

    return strcut($ret, $length);
}

/**
 * Get a random uniq-id with/without length & base options.
 *
 * @param  int  $length
 * @param  int  $base
 * @return string|null
 * @since  4.0
 */
function get_random_uniqid(int $length = 14, int $base = 16): string|null
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

    return join('-', map($parts, fn($p) => dechex((int) $p)));
}

/**
 * Get real path of given path.
 *
 * @param  string $path
 * @param  bool   $check
 * @param  bool   $check_file
 * @return string|null
 * @since  4.0
 */
function get_real_path(string $path, bool $check = false, bool $check_file = false): string|null
{
    $path = trim($path);
    if (!$path) {
        return null;
    }
    if ($rpath = realpath($path)) {
        return $rpath;
    }

    // Make path "foo" => "./foo" so prevent invalid returns.
    if (!strsrc($path, __dirsep)) {
        $path = '.' . __dirsep . $path;
    }

    $ret = '';
    $exp = explode(__dirsep, $path);

    foreach ($exp as $i => $cur) {
        $cur = trim($cur);
        if ($i == 0) {
            if ($cur == '~') { // Home path (eg: ~/Desktop).
                $ret = getenv('HOME') ?: '';
                continue;
            } elseif ($cur == '.' || $cur == '..') {
                if (!$ret) {
                    $file = getcwd(); // Fallback.

                    foreach (debug_backtrace(0) as $trace) {
                        // Search until finding the right path argument (sadly seems no way else
                        // for that when call stack is chaining from a function to another function).
                        if (empty($trace['args'][0]) || $trace['args'][0] != $path) {
                            break;
                        }

                        $file = $trace['file'];
                    }

                    $ret  = ($cur == '.') ? dirname($file) : dirname(dirname($file));
                } // Else pass.
                continue;
            }
        }

        if ($cur == '.' || !$cur) {
            continue;
        } elseif ($cur == '..') {
            $ret = dirname($ret); // Jump upper.
            continue;
        }

        $ret .= __dirsep . $cur; // Append current.
    }

    // Validate file/directory or file only existence.
    if ($check) {
        $ok = $check_file ? is_file($path) : file_exists($ret);
        $ok || $ret = null;
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

    $ret = ['path' => $path] + map($info, fn($v) => strlen($v) ? $v : null);

    $ret['filename']  = file_name($path, false);
    $ret['extension'] = file_extension($path, false);

    if ($component) {
        if (is_string($component)) {
            $ret = $ret[$component] ?? null;
        } else {
            $ret = match ($component) {
                PATHINFO_DIRNAME  => $ret['dirname'],  PATHINFO_BASENAME  => $ret['basename'],
                PATHINFO_FILENAME => $ret['filename'], PATHINFO_EXTENSION => $ret['extension'],
                default           => null,
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
            $cur['methodType'] = ($cur['type']  == '::') ? 'static' : 'this';
        }

        if (isset($trace[$i + 1]['function'])) {
            $cur['caller'] = $trace[$i + 1]['function'];
        }
    }

    return is_null($index) ? $trace : $trace[$index][$field] ?? $trace[$index] ?? null;
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
    $dir = tmp() . __dirsep . ($prefix ?? 'froq-') . suid();

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
    $nam = tmp() . __dirsep . ($prefix ?? 'froq-') . suid();

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
    return is_dir($dir) && strpfx($dir, tmp());
}

/**
 * Check whether given file is in temporary directory.
 *
 * @param  string $nam
 * @return bool
 * @since  5.0
 */
function is_tmpnam(string $nam): bool
{
    return is_file($nam) && strpfx($nam, tmp());
}

/**
 * Create a file with given file path.
 *
 * @param  string  $file
 * @param  int     $mode
 * @param  bool    $tmp @internal
 * @return bool|null
 * @since  4.0
 */
function mkfile(string $file, int $mode = 0644, bool $tmp = false): bool|null
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return null;
    }

    // Some speed for internal tmp calls.
    if (!$tmp) {
        $file = get_real_path($file);

        if (is_dir($file)) {
            trigger_error(sprintf('%s(): Cannot create %s, it is a directory', __function__, $file));
            return null;
        } elseif (is_file($file)) {
            trigger_error(sprintf('%s(): Cannot create %s, it is already exist', __function__, $file));
            return null;
        }
    }

    // Ensure directory.
    is_dir(dirname($file)) || mkdir(dirname($file), 0755, true);

    return touch($file) && chmod($file, $mode);
}

/**
 * Remove a file.
 *
 * @param  string $file
 * @return bool|null
 * @since  4.0
 */
function rmfile(string $file): bool|null
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return null;
    }

    $file = get_real_path($file);

    if (is_dir($file)) {
        trigger_error(sprintf('%s(): Cannot remove %s, it is a directory', __function__, $file));
        return null;
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
 * @return bool|null
 * @since  4.0
 */
function rmdirtemp(string $dir): bool|null
{
    if (!is_tmpdir($dir)) {
        trigger_error(sprintf('%s(): Cannot remove a directory which is outside of %s directory',
            __function__, tmp()));
        return null;
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
 * @return bool|null
 * @since  4.0
 */
function rmfiletemp(string $file): bool|null
{
    if (!is_tmpnam($file)) {
        trigger_error(sprintf('%s(): Cannot remove a file which is outside of %s directory',
            __function__, tmp()));
        return null;
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
    } elseif (!strsfx($file, '.php')) {
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
    if (substr($file, -1) === __dirsep) {
        return null;
    }

    // Function basename() wants an explicit suffix to remove it from name,
    // but using just a boolean here is more sexy..
    $ret = basename($file);

    if ($ret && !$with_ext && ($ext = file_extension($file, true))) {
        $ret = substr($ret, 0, -strlen($ext));
    }

    return $ret ?: null;
}

/**
 * Get file extension.
 *
 * @param  string $file
 * @param  bool   $with_dot
 * @return string|null
 * @since  4.0
 */
function file_extension(string $file, bool $with_dot = false): string|null
{
    $info = pathinfo($file);

    // Function pathinfo() returns ".foo" for example "/some/path/.foo" and
    // if $with_dot false then this function return ".", no baybe!
    if (empty($info['filename']) || empty($info['extension'])) {
        return null;
    }

    $ret = strrchr($info['basename'], '.');

    if ($ret && !$with_dot) {
        $ret = ltrim($ret, '.');
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
            if (empty($cache[$extension = strtolower($extension)])) {
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
 * Init a DateTime object without/without given when option & with/without timezone if given
 * or default timezone.
 *
 * @param  int|float|string|null $when
 * @param  string|null           $where
 * @return DateTime
 * @since  4.25
 */
function udate(int|float|string $when = null, string $where = null): DateTime
{
    $when ??= ''; $where ??= date_default_timezone_get();

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
 * Get microtime as float or string.
 *
 * @param  bool $string
 * @return float|string
 * @since  4.0
 */
function utime(bool $string = false): float|string
{
    return !$string ? microtime(true) : sprintf('%.6F', microtime(true));
}

/**
 * Get a random float.
 *
 * @param  float|null $min
 * @param  float|null $max
 * @return float
 * @since  5.0
 */
function urand(float $min = null, float $max = null): float
{
    return Numbers::randomFloat($min, $max);
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

    $time ??= (int) date('U');
    if (is_string($time)) {
        $time = strtotime($time);
    }

    return strtotime($format, $time) - $time;
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
 * Apply given function to each element of given array with key/value notation.
 *
 * @param  array    $array
 * @param  callable $func
 * @return array
 * @since  4.0
 */
function array_apply(array $array, callable $func): array
{
    return array_map($func, $array, array_keys($array));
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
 * Check whether all given values are set in given array.
 *
 * @param  array    $array
 * @param  any   ...$values
 * @return bool
 * @since  5.0
 */
function array_contains(array $array, ...$values): bool
{
    foreach ($values as $value) {
        if (!in_array($value, $array, true)) {
            return false;
        }
    }
    return $array && $values;
}

/**
 * Drop given values from given array if exist.
 *
 * @param  array    &$array
 * @param  any   ...$values
 * @return array
 * @since  5.0
 */
function array_delete(array &$array, ...$values): array
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
 * Ensure keys padding given keys on an array with/without given pad value.
 *
 * @param  array    $array
 * @param  array    $keys
 * @param  any|null $value
 * @return array
 * @since  4.0
 */
function array_pad_keys(array $array, array $keys, $value = null): array
{
    foreach ($keys as $key) {
        isset($array[$key]) || $array[$key] = $value;
    }
    return $array;
}

/**
 * Map given array fields by given keys only.
 *
 * @param  array    $array
 * @param  array    $keys
 * @param  callable $func
 * @return array
 * @since  5.0
 */
function array_map_keys(array $array, array $keys, callable $func): array
{
    foreach ($keys as $key) {
        $array[$key] = $func($array[$key] ?? null);
    }
    return $array;
}

/**
 * Convert key cases mapping by given separator.
 *
 * @param  array       $array
 * @param  int         $case
 * @param  string|null $spliter
 * @param  string|null $joiner
 * @param  bool        $recursive
 * @return array|null
 * @since  4.19
 */
function array_convert_keys(array $array, int $case, string $spliter = null, string $joiner = null, bool $recursive = false): array|null
{
    // Check valid cases.
    if (!in_array($case, [CASE_LOWER, CASE_UPPER, CASE_TITLE, CASE_DASH, CASE_SNAKE, CASE_CAMEL])) {
        trigger_error(sprintf('%s(): Invalid case %s, use a case from 0..5 range', __function__, $case));
        return null;
    }

    if ($case == CASE_LOWER || $case == CASE_UPPER) {
        return array_change_key_case($array, $case);
    }

    if (!$spliter) {
        trigger_error(sprintf('%s(): No separator given', __function__, $case));
        return null;
    }

    $ret = [];

    foreach ($array as $key => $value) {
        $key = convert_case($key, $case, $spliter, $joiner);
        if ($recursive && is_array($value)) {
            $value = array_convert_keys($value, $case, $spliter, $joiner, true);
            $ret[$key] = $value;
        } else {
            $ret[$key] = $value;
        }
    }

    return $ret;
}

/**
 * Change keys mapping by given function (@see https://wiki.php.net/rfc/array_change_keys).
 *
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $recursive
 * @return array
 * @since  5.0
 */
function array_change_keys(array $array, callable $func, bool $recursive = false): array
{
    $ret = [];

    foreach ($array as $key => $value) {
        $key = $func($key);
        if ($recursive && is_array($value)) {
            $ret[$key] = array_change_keys($value, $func, true);
        } else {
            $ret[$key] = $value;
        }
    }

    return $ret;
}

/**
 * Check a value if exists with/without strict comparison.
 *
 * @param  any   $value
 * @param  array $array
 * @param  bool  $strict
 * @return bool
 * @since  4.0
 */
function array_value_exists($value, array $array, bool $strict = true): bool
{
    return in_array($value, $array, $strict);
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
    return Arrays::getRandom($array, $limit, false, $drop) ?? $default;
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
 * Perform a mathematical union on given array inputs.
 *
 * @param  array     $array1
 * @param  array     $array2
 * @param  array ... $others
 * @return array
 * @since  5.0
 */
function array_union(array $array1, array $array2, array ...$others): array
{
    return array_values(array_unique(array_merge($array1, $array2, ...$others)));
}

/**
 * Check whether given array is a list array.
 *
 * @param  any  $in
 * @param  bool $allow_empty
 * @return bool
 * @since  5.0
 */
function is_list($in, bool $allow_empty = true): bool
{
    return $allow_empty ? is_array($in) && Arrays::isSet($in, true)
                 : $in && is_array($in) && Arrays::isSet($in, true);
}

/**
 * Check whether given key exists on given array.
 *
 * @param  string $key
 * @param  array  $array
 * @return bool
 * @since  5.0
 */
function is_array_key(int|string $key, array $array): bool
{
    return array_key_exists($key, $array);
}

/**
 * Check whether given value exists on given array in strict comparison.
 *
 * @param  any   $value
 * @param  array $array
 * @return bool
 * @since  5.0
 */
function is_array_value($value, array $array): bool
{
    return array_value_exists($value, $array);
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
 * @return string|null
 * @since  4.17
 */
function error_message(int &$code = null, bool $format = false): string|null
{
    $error = error_get_last();
    if (!$error) {
        return null;
    }

    $code = $error['type'];

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
function preg_error_message(int &$code = null): string|null
{
    return ($code = preg_last_error()) ? preg_last_error_msg() : null;
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
        $ret .= ALPHABET[rand(0, $max)];
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
    $bytes = !$timed ? random_bytes(16)               // Fully 16-random bytes.
        : hex2bin(dechex(time())) . random_bytes(12); // Bin of time prefix & 12-random bytes.

    // Add signs: 4 (version) & 8, 9, A, B, but GUID doesn't use them.
    if (!$guid) {
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
    }

    $ret = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));

    $dashed || $ret = str_replace('-', '', $ret);

    return $ret;
}

/**
 * Generate a random UUID/GUID hash, optionally with current timestamp.
 *
 * @param  int  $length
 * @param  bool $format
 * @param  bool $timed
 * @param  bool $guid
 * @return string|null
 * @since  5.0
 */
function uuid_hash(int $length = 32, bool $format = false, bool $timed = false, bool $guid = false): string|null
{
    static $algos = [32 => 'md5', 40 => 'sha1', 64 => 'sha256', 16 => 'fnv1a64'];

    $algo = $algos[$length] ?? null;

    if (!$algo) {
        trigger_error(sprintf('%s(): Invalid length, valids are: 32,40,64,16', __function__));
        return null;
    }

    $ret = hash($algo, uuid(false, $timed, $guid));

    if ($format) {
        if ($length != 32) {
            trigger_error(sprintf('%s(): Format option for only 32-length hashes', __function__));
            return null;
        }

        $ret = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($ret, 4));
    }

    return $ret;
}

/**
 * Convert a multi-byte string's first character to upper-case.
 *
 * @param  string      $in
 * @param  string|null $encoding
 * @param  bool        $tr
 * @return string
 * @since  5.0
 */
function mb_ucfirst(string $in, string $encoding = null, bool $tr = false): string
{
    $first = mb_substr($in, 0, 1, $encoding);
    if ($tr && $first === 'i') {
        $first = 'İ';
    }

    return mb_strtoupper($first, $encoding) . mb_substr($in, 1, null, $encoding);
}

/**
 * Convert a multi-byte string's first character to lower-case.
 *
 * @param  string      $in
 * @param  string|null $encoding
 * @param  bool        $tr
 * @return string
 * @since  5.0
 */
function mb_lcfirst(string $in, string $encoding = null, bool $tr = false): string
{
    $first = mb_substr($in, 0, 1, $encoding);
    if ($tr && $first === 'I') {
        $first = 'ı';
    }

    return mb_strtolower($first, $encoding) . mb_substr($in, 1, null, $encoding);
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
    static $chars; $chars ??= require 'statics/slug-chars.php';

    $preserve && $preserve = preg_quote($preserve, '~');
    $replace  || $replace  = '-';

    $out = preg_replace(['~[^\w'. $preserve . $replace .']+~', '~['. $replace .']+~'],
        $replace, strtr($in, $chars));

    return strtolower(trim($out, $replace));
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
function random_string(int $length = 10, bool $puncted = false): string
{
    return Strings::random($length, $puncted);
}

/**
 * Check whether given input is a number.
 *
 * @param  any $in
 * @return bool
 * @since  5.0
 */
function is_number($in): bool
{
    return is_int($in) || is_float($in);
}

/**
 * Check whether given input is a GdImage.
 *
 * @param  any $in
 * @return bool
 * @since  5.0
 */
function is_image($in): bool
{
    return $in && ($in instanceof GdImage);
}

/**
 * Check whether given input is a stream.
 *
 * @param  any $in
 * @return bool
 * @since  5.0
 */
function is_stream($in): bool
{
    return $in && is_resource($in) && get_resource_type($in) == 'stream';
}

/**
 * Check whether given input is type of other.
 *
 * @param  any    $in
 * @param  string $type
 * @return bool
 * @since  5.0
 */
function is_type_of($in, string $type): bool
{
    return match ($type) {
        'image'  => is_image($in),  'stream' => is_stream($in),
        'number' => is_number($in), 'scalar' => is_scalar($in),
        'array'  => is_array($in),  'object' => is_object($in),
        default  => strtolower($type) == strtolower(get_type($in)) // All others.
    };
}

/**
 * Check empty state(s) of given input(s).
 *
 * @param  any     $in
 * @param  any ... $ins
 * @return bool
 * @since  4.0 Added back, 5.0 Moved from sugars/is.
 */
function is_empty($in, ...$ins): bool
{
    $ins = [$in, ...$ins];

    foreach ($ins as $in) {
        $size = size($in);
        if ($size !== null && !$size) {
            return true;
        }
        if (empty($in)) {
            return true;
        }
    }

    return false;
}
