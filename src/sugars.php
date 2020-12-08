<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\{Arrays, Objects};

// Load constants.
defined('nil') || require 'sugars-constant.php';

/**
 * Yes man..
 */
function equal($a, $b, ...$c): bool {
    return ($a == $b) || ($c && in_array($a, [$b, ...$c])); }
function equals($a, $b, ...$c): bool {
    return ($a === $b) || ($c && in_array($a, [$b, ...$c], true)); }

/**
 * Pick/pluck..
 * Loving https://docs.zephir-lang.com/0.12/en/operators#fetch
 */
function pick(array &$array, int|string $key, &$value = null, bool $drop = false): bool {
    return ($value = array_pick($array, $key, null, $drop)) !== null; }
function pluck(array &$array, int|string $key, &$value = null): bool {
    return ($value = array_pluck($array, $key, null)) !== null; }

/**
 * The ever most wanted functions (finally come with 8.0, but without case option).
 * @alias of str_has(),str_has_prefix(),str_has_suffix()
 * @since 4.0
 */
function strsrc(...$args) { return str_has(...$args); }        // Search.
function strpfx(...$args) { return str_has_prefix(...$args); } // Search prefix.
function strsfx(...$args) { return str_has_suffix(...$args); } // Search suffix.

/**
 * Filter an array with value/key notation.
 *
 * @param  array           $array
 * @param  callable        $func
 * @param  bool            $keep_keys
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
 * @param  array           $array
 * @param  callable        $func
 * @param  bool            $keep_keys
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
 * @param  array         $array
 * @param  any           $carry
 * @param  callable|null $func
 * @return any
 * @since  4.0, 5.0 Moved from common.inits.
 */
function reduce(array $array, $carry = null, callable $func = null)
{
    return !is_array($carry) ? Arrays::reduce($array, $func, $carry)
                             : Arrays::aggregate($array, $func, $carry);
}

/**
 * Get size/count/length of given input.
 *
 * @param  any  $in
 * @param  bool $mb
 * @return int|null
 * @since  3.0, 5.0 Moved from froq/fun.
 */
function size($in, bool $mb = false): int|null
{
    return match (true) {
        is_string($in)            => !$mb ? strlen($in) : mb_strlen($in),
        is_countable($in)         => count($in),
        ($in instanceof stdClass) => count((array) $in),
        default                   => null // No valid input.
    };
}

/**
 * Concat an array or string.
 *
 * @param  array|string    $in
 * @param  array|string ...$ins
 * @return array|string|null
 * @since  4.0, 5.0 Moved from froq/fun.
 */
function concat(array|string $in, ...$ins): array|string|null
{
    return match (true) {
        is_array($in)  => array_append($in, ...$ins),
        is_string($in) => $in . join('', $ins)
    };
}

/**
 * Slice an array or string.
 *
 * @param  array|string $in
 * @param  int          $start
 * @param  int|null     $end
 * @return array|string|null
 * @since  3.0, 4.0 Added back, 5.0 Moved from froq/fun.
 */
function slice(array|string $in, int $start, int $end = null): array|string|null
{
    return match (true) {
        is_array($in)  => array_slice($in, $start, $end),
        is_string($in) => mb_substr($in, $start, $end)
    };
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
    return ($length > 0) ? substr($str, 0, $length) : substr($str, $length);
}

/**
 * Cut a string before given search position with/without given length, or return null if no search found.
 *
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  bool     $icase
 * @return string|null
 * @since  4.0
 */
function strbcut(string $str, string $src, int $length = null, bool $icase = false): string|null
{
    $pos = !$icase ? strpos($str, $src) : stripos($str, $src);

    if ($pos !== false) {
        $cut = substr($str, 0, $pos); // Before (b).
        return !$length ? $cut : strcut($cut, $length);
    }

    return null; // Not found.
}

/**
 * Cut a string after given search position with/without given length, or return null if no search found.
 *
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  bool     $icase
 * @return string|null
 * @since  4.0
 */
function stracut(string $str, string $src, int $length = null, bool $icase = false): string|null
{
    $pos = !$icase ? strpos($str, $src) : stripos($str, $src);

    if ($pos !== false) {
        $cut = substr($str, $pos + strlen($src)); // After (a).
        return !$length ? $cut : strcut($cut, $length);
    }

    return null; // Not found.
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
    return (!$icase ? strpos($str, $src) : stripos($str, $src)) !== false;
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
    return substr_compare($str, $src, 0, strlen($src), $icase) === 0;
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
    return substr_compare($str, $src, -strlen($src), null, $icase) === 0;
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

    $str_length = strlen($str);
    if ($length && ($length < 1 || $length > $str_length)) {
        trigger_error(sprintf('%s(): Length must be between 1-%s or null', __function__, $str_length));
        return null;
    }

    return !$length ? str_shuffle($str) : substr(str_shuffle($str), 0, $length);
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
    // Using base62 chars.
    static $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

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

    [$in_length, $from_base, $to_base]
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
            $div = ($div * $from_base) + $numbers[$i];
            if ($div >= $to_base) {
                $numbers[$new_length++] = ($div / $to_base) | 0;
                $div = $div % $to_base;
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
        return strtolower($in);
    } elseif ($case == CASE_UPPER) {
        return strtoupper($in);
    }

    // Set default split char.
    $spliter = ($spliter !== null || $spliter !== '') ? $spliter : ' ';

    return match ($case) {
        CASE_DASH  => implode('-', explode($spliter, strtolower($in))),
        CASE_SNAKE => implode('_', explode($spliter, strtolower($in))),
        CASE_CAMEL => lcfirst(
            implode($joiner ?? '', array_map(
                fn($s) => ucfirst(trim($s)), explode($spliter, strtolower($in))
            ))
        ),
        CASE_TITLE => implode($joiner ?? $spliter, array_map(
            fn($s) => ucfirst(trim($s)), explode($spliter, strtolower($in))
        )),
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
function get_class_name(string|object $class, bool $short = true): string
{
    return $short ? Objects::getShortName($class) : Objects::getName($class);
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

    return Objects::getConstantValues($class, $all ?? false, $with_names);
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

    return Objects::getPropertyValues($class, $all ?? false, $with_names);
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
 * @param  bool $use_hrtime
 * @return string|null
 * @since  4.0
 */
function get_uniqid(int $length = 14, int $base = 16, bool $use_hrtime = false): string|null
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
    if (!$use_hrtime) {
        $id = explode('.', uniqid('', true))[0];
    } else {
        $id = join('', map(hrtime(), 'dechex'));
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
    while (strlen($ret) < $length) {
        $ret .= get_random_uniqid(3, $base, false);
    }

    return strcut($ret, $length);
}

/**
 * Get a random uniq-id with/without length & base options.
 *
 * @param  int  $length
 * @param  int  $base
 * @param  bool $length_check @internal
 * @return string|null
 * @since  4.0
 */
function get_random_uniqid(int $length = 14, int $base = 16, bool $length_check = true): string|null
{
    if ($length < 14 && $length_check) {
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
    $parts = explode('.', uniqid('', true));

    // Add/use an ephemeral number if no port (~$ cat /proc/sys/net/ipv4/ip_local_port_range).
    $parts[2] = ($_SERVER['REMOTE_PORT'] ?? rand(32768, 60999));

    return vsprintf('%014s-%07x-%04x', $parts);
}

/**
 * Get Froq's temporary directory.
 *
 * @param  string|null $subdir
 * @return string
 * @since  4.0
 */
function get_temp_directory(string $subdir = null): string
{
    $dir = sys_get_temp_dir() . __dirsep . 'froq-temp' . (
        $subdir ? __dirsep . trim($subdir, __dirsep) : ''
    );

    is_dir($dir) || mkdir($dir, 0755, true);

    return __dirsep . trim($dir, __dirsep);
}

/**
 * Get Froq's cache directory.
 *
 * @param  string|null $subdir
 * @return string
 * @since  4.0
 */
function get_cache_directory(string $subdir = null): string
{
    $dir = sys_get_temp_dir() . __dirsep . 'froq-cache'
         . ($subdir ? __dirsep . trim($subdir, __dirsep) : '');

    is_dir($dir) || mkdir($dir, 0755, true);

    return __dirsep . trim($dir, __dirsep);
}

/**
 * Get real user.
 *
 * @return string|null
 * @since  4.0
 */
function get_real_user(): string|null
{
    try {
        return posix_getpwuid(posix_geteuid())['name'] ?? null;
    } catch (Error $e) {
        return getenv('USER') ?: getenv('USERNAME') ?: null;
    }
}

/**
 * Get real path of given path.
 *
 * @param  string $path
 * @param  bool   $strict
 * @return string|null
 * @since  4.0
 */
function get_real_path(string $path, bool $strict = false): string|null
{
    $path = trim($path);
    if (!$path) {
        return null;
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

    if ($strict && !file_exists($ret)) {
        $ret = null;
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
    $path = trim($path);
    if (!$path) {
        return null;
    }

    if (!$info = pathinfo($path)) {
        return null;
    }

    $ret = ['path' => $path] + map($info, fn($v) => strlen($v) ? $v : null);
    $ret['filename'] = file_name($path, false);
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
 * Get a bit detailed trace with default options, limit & index options.
 *
 * @param  int|null $options
 * @param  int|null $limit
 * @param  int|null $index
 * @return array|null
 * @since  4.0
 */
function get_trace(int $options = null, int $limit = null, int $index = null): array|null
{
    $trace = debug_backtrace($options ?? 0, $limit ? $limit + 1 : 0);
    array_shift($trace); // Drop self.

    foreach ($trace as $i => &$cur) {
        $cur += [
            'caller'   => null,
            'callee'   => $cur['function'] ?? null,
            'callPath' => $cur['file'] . ':' . $cur['line'],
        ];

        if (isset($cur['class'])) {
            $cur['method']     = $cur['function'];
            $cur['methodType'] = ($cur['type']  == '::') ? 'static' : 'this';
        }

        if (isset($trace[$i + 1]['function'])) {
            $cur['caller'] = $trace[$i + 1]['function'];
        }
    }

    return is_null($index) ? $trace : $trace[$index] ?? null;
}

/**
 * Get system temporary directory.
 *
 * @return string
 * @since  4.0
 */
function tmp(): string
{
    return dirname(get_temp_directory());
}

/**
 * Create a folder in system temporary directory.
 *
 * @param  string|null $prefix
 * @param  int         $mode
 * @return string
 * @since  4.0
 */
function tmpdir(string $prefix = null, int $mode = 0755): string
{
    $dir = ( // Eg: "/tmp/froq-5f858f253527c91a4006".
        tmp() . __dirsep . ($prefix ?? 'froq-') . get_uniqid(20)
    );

    is_dir($dir) || mkdir($dir, $mode);

    return $dir;
}

/**
 * Create a file with given file path.
 *
 * @param  string  $file
 * @param  int     $mode
 * @return bool|null
 * @since  4.0
 */
function mkfile(string $file, int $mode = 0644): bool|null
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return null;
    }

    $file = get_real_path($file);

    if (is_dir($file)) {
        trigger_error(sprintf('%s(): Cannot create %s, it is a directory', __function__, $file));
        return null;
    } elseif (is_file($file)) {
        trigger_error(sprintf('%s(): Cannot create %s, it is already exists', __function__, $file));
        return null;
    }

    $ok = is_dir(dirname($file)) || mkdir(dirname($file), 0755, true);

    return $mode ? ($ok && touch($file) && chmod($file, $mode))
                 : ($ok && touch($file));
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
 * @alias of tmpdir().
 * @since 4.0
 */
function mkdirtemp(...$args)
{
    return tmpdir(...$args);
}

/**
 * Remove a folder from system temporary directory.
 *
 * @param  string $dir
 * @return bool|null
 * @since 4.0
 */
function rmdirtemp(string $dir): bool|null
{
    if (dirname($dir) != tmp()) {
        trigger_error(sprintf('%s(): Cannot remove a directory which is outside of %s directory',
            __function__, tmp()));
        return null;
    }

    // Clean inside but not recursive.
    foreach (glob($dir . '/*') as $file) {
        unlink($file);
    }

    return is_dir($dir) && rmdir($dir);
}

/**
 * Create a file in temporary directory.
 *
 * @param  string|null $extension
 * @param  int         $mode
 * @param  bool        $froq_temp
 * @return string|null
 * @since  4.0
 */
function mkfiletemp(string $extension = null, int $mode = 0644, bool $froq_temp = true): string|null
{
    $file = ( // Eg: "/tmp/froq-temp/5f858f253527c91a4006".
        ($froq_temp ? get_temp_directory() : dirname(get_temp_directory()))
        . __dirsep . get_uniqid(20)
        . ($extension ? '.' . trim($extension, '.') : '')
    );

    return mkfile($file, $mode) ? $file : null;
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
    if (!strpfx($file, tmp())) {
        trigger_error(sprintf('%s(): Cannot remove a file which is outside of %s directory',
            __function__, tmp()));
        return null;
    }

    return is_file($file) && unlink($file);
}

/**
 * Open a temporary file.
 *
 * @param  string|null $mode
 * @param  int|null    $memo
 * @return resource|null
 * @since  4.0
 */
function fopentemp(string $mode = null, int $memo = null)
{
    if ($mode || $memo) {
        $mode ??= 'w+b'; // Set as default.
        $ret = $memo ? fopen('php://temp/maxmemory:'. $memo, $mode)
            : fopen('php://temp', $mode);
    } else {
        $ret = tmpfile();
    }

    return $ret ?: null;
}

/**
 * Read all contents a file handle.
 *
 * @alias of file_read_stream()
 * @since 5.0
 */
function freadall($fp, int $from = 0): string|null
{
    return file_read_stream($fp, $from);
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
 * Rewind a file handle.
 *
 * @alias of rewind()
 * @since 4.0
 */
function frewind(&$fp): bool
{
    return rewind($fp);
}

/**
 * Reset a file handle contents & seek position.
 *
 * @param  resource &$fp
 * @param  string    $contents
 * @return bool
 * @since  4.0
 */
function freset(&$fp, string $contents): bool
{
    rewind($fp); // Without this, stats won't be resetted.

    return ftruncate($fp, 0) && fwrite($fp, $contents) && !fseek($fp, 0);
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
 * Get a file handle stats & metadata.
 *
 * @param  resource $fp
 * @return array|null
 * @since  4.0
 */
function finfo($fp): array|null
{
    [$stat, $meta] = [fstat($fp), fmeta($fp)];

    return ($stat && $meta) ? $stat + ['meta' => $meta] : null;
}

/**
 * Set a handle contents & seek position.
 *
 * @param  resource &$handle
 * @param  string    $contents
 * @return bool
 * @since  4.0
 */
function stream_set_contents(&$handle, string $contents): bool
{
    // Since handle stat size also pointer position is not changing even after ftruncate() for
    // files (not "php://temp" etc), we rewind the handle.
    rewind($handle);

    // Empty, write & rewind.
    return ftruncate($handle, 0) && fwrite($handle, $contents) && !fseek($handle, 0);
}

/**
 * Init a DateTime object without/without given when option & with/without timezone if given
 * or default timezone.
 *
 * @param  int|float|string $when
 * @param  string|null      $where
 * @return DateTime
 * @since  4.25
 */
function udate(int|float|string $when = null, string $where = null): DateTime
{
    $when ??= '';
    $where ??= date_default_timezone_get();

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
 * Get an interval by given format.
 *
 * @param  string   $format
 * @param  int|null $time
 * @return int
 * @since  4.0
 */
function strtoitime(string $format, int $time = null): int
{
    // Eg: "1 day" or "1D" (instead "60*60*24" or "86400").
    if (preg_match_all('~([+-]?\d+)([smhDMY])~', $format, $matches)) {
        $format_list = null;

        [, $numbers, $formats] = $matches;
        foreach ($formats as $i => $format) {
            switch ($format) {
                case 's': $format_list[] = $numbers[$i] . ' second'; break;
                case 'm': $format_list[] = $numbers[$i] . ' minute'; break;
                case 'h': $format_list[] = $numbers[$i] . ' hour';   break;
                case 'D': $format_list[] = $numbers[$i] . ' day';    break;
                case 'M': $format_list[] = $numbers[$i] . ' month';  break;
                case 'Y': $format_list[] = $numbers[$i] . ' year';   break;
            }
        }

        // Update format.
        $format_list && (
            $format = join(' ', $format_list)
        );
    }

    $time = $time ?? time();

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
function preg_remove(string|array $pattern, string|array $subject, int $limit = null,
    int &$count = null): string|array|null
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
 * Apply given function to each element of given array.
 *
 * @param  array    $array
 * @param  callable $func
 * @return array
 * @since  4.0
 */
function array_apply(array $array, callable $func): array
{
    $ret = []; $i = 0;

    foreach ($array as $key => $value) {
        // Because array_map() tricky with array_keys() only for value => key notation, and also
        // just warns about argument count (e.g: if $func is strval()) and foolishly making all
        // values NULL; simply use this way here with try/catch, catching ArgumentCountError only.
        try {
            $ret[$key] = $func($value, $key, $i++, $array);
        } catch (ArgumentCountError) {
            $ret[$key] = $func($value);
        }
    }

    return $ret;
}

/**
 * Test all given keys are set in an array.
 *
 * @param  array $array
 * @param  array $keys
 * @return bool
 * @since  4.0
 */
function array_isset(array $array, array $keys): bool
{
    foreach ($keys as $key) {
        if (!isset($array[$key])) {
            return false;
        }
    }
    return true;
}

/**
 * Drop all given keys from an array.
 *
 * @param  array &$array
 * @param  array  $keys
 * @return array
 * @since  4.0
 */
function array_unset(array &$array, array $keys): array
{
    foreach ($keys as $key) {
        unset($array[$key]);
    }
    return $array;
}

/**
 * Append given values to an array, returning array back.
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
 * Prepend given values to an array, returning array back.
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
    return array_replace(array_fill_keys($keys, $value), $array);
}

/**
 * Convert key cases mapping by given separator.
 *
 * @param  array       $array
 * @param  int         $case
 * @param  string|null $sep
 * @return array|null
 * @since  4.19
 */
function array_convert_keys(array $array, int $case, string $spliter = null, string $joiner = null): array|null
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
        $ret[$key] = $value;
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
 * Select item(s) from an array by given key(s).
 *
 * @param  int|string|array<int|string> $array
 * @param  array                        $key
 * @param  any|null                     $default
 * @return any
 * @since  5.0
 */
function array_select(array $array, int|string|array $key, $default = null)
{
    // A little bit faster comparing to array_pick().
    foreach ((array) $key as $key) {
        $ret[] = $array[$key] ?? $default;
    }
    return $ret;
}

/**
 * Put given items into an array.
 *
 * @param  array  &$array
 * @param  array   $items
 * @return array
 * @since  4.9, 4.18 Actual version.
 */
function array_put(array &$array, array $items): array
{
    return Arrays::setAll($array, $items);
}

/**
 * Pick an item form an array by given key/path.
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
 * Pluck an item form an array by given key/path.
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $default
 * @return any|null
 * @since  4.9, 4.13 Actual version.
 */
function array_pluck(array &$array, $key, $default = null)
{
    // Just got sick of "if isset array[..]: value=array[..], unset(array[..])" stuffs.
    return is_array($key) ? Arrays::pullAll($array, $key, $default)
                          : Arrays::pull($array, $key, $default);
}

/**
 * Get a/a few random item(s) form an array with/without given limit.
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
 * Create a file with given path.
 *
 * @param  string $file
 * @param  int    $mode
 * @return string|null
 * @since  4.0
 */
function file_create(string $file, int $mode = 0644): string|null
{
    return mkfile($file, $mode) ? $file : null;
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
 * @alias of file_get_contents()
 * @since 4.0
 */
function file_read(...$args)
{
    $ret = file_get_contents(...$args);

    return ($ret !== false) ? $ret : null;
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
 * Read a file stream contents entirely without modifing seek position.
 *
 * @param  resource $handle
 * @param  int      $from
 * @return string|null
 * @since  5.0
 */
function file_read_stream($handle, int $from = 0): string|null
{
    $ret = stream_get_contents($handle, -1, $from);

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
 * Get a file path
 *
 * @alias of get_real_path()
 * @since 4.0
 */
function file_path(...$args)
{
    return get_real_path(...$args);
}

/**
 * Get a file name.
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
 * Get a file extension.
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
 * Get a file (mime) type.
 *
 * @param  string $file
 * @return string|null
 * @since  4.0
 */
function file_type(string $file): string|null
{
    $ret = null;

    if (is_file($file)) try {
        $ret = mime_content_type($file);
        if ($ret === false) try {
            $exec = exec('file -i ' . escapeshellarg($file));
            if ($exec && preg_match('~: *([^/ ]+/[^; ]+)~', $exec, $match)) {
                $ret = $match[1];
                if ($ret == 'inode/directory') {
                    $ret = 'directory';
                }
            }
        } catch (Error) {}
    } catch (Error) {}

    // Try with extension.
    if (!$ret) {
        $extension = file_extension($file, false);
        if ($extension) {
            static $cache; // For some speed..
            if (empty($cache[$extension = strtolower($extension)])) {
                foreach (include 'statics/mime.php' as $type => $extensions) {
                    if (in_array($extension, $extensions, true)) {
                        $cache[$extension] = $ret = $type;
                        break;
                    }
                }
            }
        }
    }

    return $ret ?: null;
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
    if ($code && $code !== (error_get_last()['type'] ?? '')) {
        return;
    }

    error_clear_last();
}

/**
 * Get last error message with code.
 *
 * @param  int|null $code
 * @return string|null
 * @since  4.17
 */
function error_message(int &$code = null): string|null
{
    $error = error_get_last();

    return ($code = $error['type'] ?? '') ? $error['message'] : null;
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
 * Generate a random UUID.
 *
 * @param  bool $dashed
 * @return string
 * @since  5.0
 */
function uuid(bool $dashed = true): string
{
    $id = random_bytes(16);

    // Add signs: 4 (version) & 8, 9, A, B.
    $id[6] = chr(ord($id[6]) & 0x0f | 0x40);
    $id[8] = chr(ord($id[8]) & 0x3f | 0x80);

    $ret = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($id), 4));

    $dashed || $ret = str_replace('-', '', $ret);

    return $ret;
}

/**
 * Generate a random UUID hash.
 *
 * @param  int  $length
 * @param  bool $format
 * @return string|null
 * @since  5.0
 */
function uuid_hash(int $length = 32, bool $format = false): string|null
{
    $ret = match ($length) {
        32 => hash('md5', uuid()), 40 => hash('sha1', uuid()),
        64 => hash('sha256', uuid()), 16 => hash('fnv1a64', uuid()),
        default => null
    };

    if (!$ret) {
        trigger_error(sprintf('%s(): Invalid length, valids are: 32,40,64,16', __function__));
        return null;
    }

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
function mb_lcfirst(string $in, string $encoding = null, bool $tr = true): string
{
    $first = mb_substr($in, 0, 1, $encoding);
    if ($tr && $first === 'I') {
        $first = 'ı';
    }

    return mb_strtolower($first, $encoding) . mb_substr($in, 1, null, $encoding);
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
        'number' => is_number($in),
        'image'  => is_image($in),
        'stream' => is_stream($in),
        default  => strtolower($type) == strtolower(get_type($in))
    };
}
