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
function pick(array &$array, $key, &$value = null, bool $drop = false): bool {
    return ($value = array_pick($array, $key, null, $drop)) !== null; }
function pluck(array &$array, $key, &$value = null): bool {
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
 * Strcut (cut a string with given length).
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
 * Strbcut (cut a string before given search position with/without given length).
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  bool     $icase
 * @return ?string
 * @since  4.0
 */
function strbcut(string $str, string $src, int $length = null, bool $icase = false): ?string
{
    $pos = !$icase ? strpos($str, $src) : stripos($str, $src);

    if ($pos !== false) {
        $cut = substr($str, 0, $pos); // Before (b).
        return !$length ? $cut : strcut($cut, $length);
    }

    return null; // Not found.
}

/**
 * Stracut (cut a string after given search position with/without given length).
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  bool     $icase
 * @return ?string
 * @since  4.0
 */
function stracut(string $str, string $src, int $length = null, bool $icase = false): ?string
{
    $pos = !$icase ? strpos($str, $src) : stripos($str, $src);

    if ($pos !== false) {
        $cut = substr($str, $pos + strlen($src)); // After (a).
        return !$length ? $cut : strcut($cut, $length);
    }

    return null; // Not found.
}

/**
 * Str has (RFC: http://wiki.php.net/rfc/str_contains).
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
 * Str has prefix (RFC: http://wiki.php.net/rfc/add_str_starts_with_and_ends_with_functions).
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
 * Str has suffix (RFC: http://wiki.php.net/rfc/add_str_starts_with_and_ends_with_functions).
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
 * Str rand.
 * @param  string   $str
 * @param  int|null $length
 * @return ?string
 * @since  4.9
 */
function str_rand(string $str, int $length = null): ?string
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
 * @param  int|string $in    Digits to convert.
 * @param  int|string $from  From chars or base.
 * @param  int|string $to    To chars or base.
 * @return ?string
 * @throws TypeError
 * @since  4.0, 4.25 Derived from str_base_convert().
 * @todo   Use "union" types for arguments.
 */
function convert_base($in, $from, $to): ?string
{
    if (!is_int($in) && !is_string($in)) {
        throw new TypeError(sprintf(
            '%s() expects parameter 1 to be int|string, %s given', __function__, get_type($in)
        ));
    } elseif (!is_int($from) && !is_string($from)) {
        throw new TypeError(sprintf(
            '%s() expects parameter 2 to be int|string, %s given', __function__, get_type($from)
        ));
    } elseif (!is_int($to) && !is_string($to)) {
        throw new TypeError(sprintf(
            '%s() expects parameter 3 to be int|string, %s given', __function__, get_type($to)
        ));
    }

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
 * @param  string      $in
 * @param  int         $case
 * @param  string|null $spliter
 * @param  string|null $joiner
 * @return ?string
 * @since  4.26
 */
function convert_case(string $in, int $case, string $spliter = null, string $joiner = null): ?string
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
    $spliter = ($spliter === null || $spliter === '') ? ' ' : $spliter;

    $ret = strtolower($in);

    switch ($case) {
        case CASE_DASH:
            $ret = implode('-', explode($spliter, $ret));
            break;
        case CASE_SNAKE:
            $ret = implode('_', explode($spliter, $ret));
            break;
        case CASE_CAMEL:
            $ret = implode($joiner ?? '', array_map('ucfirst', explode($spliter, $ret)));
            $ret = lcfirst($ret);
            break;
        case CASE_TITLE:
            $ret = implode($joiner ?? $spliter, array_map('ucfirst', explode($spliter, $ret)));
            break;
    }

    return $ret;
}

/**
 * Class extends.
 * @param  string $class1
 * @param  string $class2
 * @param  bool   $parent_only
 * @return bool
 * @since  4.21
 */
function class_extends(string $class1, string $class2, bool $parent_only = false): bool
{
    if (!$parent_only) {
        return is_subclass_of($class1, $class2);
    }

    return ($parents = class_parents($class1)) && (current($parents) === $class2);
}

/**
 * Get class constants.
 * @param  string|object $class
 * @param  bool          $with_names
 * @param  bool          $check_scope
 * @return ?array
 * @since  4.0
 */
function get_class_constants($class, bool $with_names = true, bool $check_scope = true): ?array
{
    if ($check_scope) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            $all = ($caller_class === Objects::getName($class));
        }
    }

    return Objects::getConstantValues($class, $all ?? false, $with_names);
}

/**
 * Get class properties.
 * @param  string|object $class
 * @param  bool          $with_names
 * @param  bool          $check_scope
 * @return ?array
 * @since  4.0
 */
function get_class_properties($class, bool $with_names = true, bool $check_scope = true): ?array
{
    if ($check_scope) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            $all = ($caller_class === Objects::getName($class));
        }
    }

    return Objects::getPropertyValues($class, $all ?? false, $with_names);
}

/**
 * Constant exists.
 * @param  string|object $class
 * @param  string        $name
 * @param  bool          $check_scope
 * @return ?bool
 * @since  4.0
 */
function constant_exists($class, string $name, bool $check_scope = true): ?bool
{
    if ($check_scope) {
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
 * Get type (RFC: http://wiki.php.net/rfc/get_debug_type).
 * @param  any  $var
 * @param  bool $scalars
 * @return string
 * @since  4.0
 */
function get_type($var, bool $scalars = false): string
{
    if (is_object($var)) {
        $ret = get_class($var);
        // Anonymous class.
        if ($pos = strpos($ret, "\0")) {
            $ret = substr($ret, 0, $pos);
        }
    } else {
        static $scalars_array   = ['int', 'float', 'string', 'bool'];
        static $translate_array = ['integer' => 'int', 'double' => 'float', 'boolean' => 'bool'];

        $ret = strtr(strtolower(gettype($var)), $translate_array);

        if ($scalars && in_array($ret, $scalars_array)) {
            $ret = 'scalar';
        } elseif ($ret == 'resource (closed)') {
            $ret = 'resource-closed';
        } elseif ($ret == 'unknown type') {
            $ret = 'unknown';
        }
    }

    return $ret;
}

/**
 * Get error.
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
 * Get uniqid.
 * @param  int  $length
 * @param  int  $base
 * @param  bool $use_hrtime
 * @return ?string
 * @since  4.0
 */
function get_uniqid(int $length = 14, int $base = 16, bool $use_hrtime = false): ?string
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
        $id = strstr(uniqid('', true), '.', true);
    } else {
        $id = join('', array_map('dechex', hrtime()));
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
        $ret .= get_random_uniqid(1, $base, false);
    }

    return $ret;
}

/**
 * Get random uniqid.
 * @param  int  $length
 * @param  int  $base
 * @param  bool $check_length @internal
 * @return ?string
 * @since  4.0
 */
function get_random_uniqid(int $length = 14, int $base = 16, bool $check_length = true): ?string
{
    if ($length < 14 && $check_length) {
        trigger_error(sprintf('%s(): Invalid length, min=14', __function__));
        return null;
    }
    if ($base < 10 || $base > 62) {
        trigger_error(sprintf('%s(): Invalid base, min=10, max=62', __function__));
        return null;
    }

    $ret = '';

    while (strlen($ret) < $length) {
        $id = bin2hex(random_bytes(1));

        // Convert non-hex ids.
        $ret .= ($base == 16) ? $id
              : convert_base($id, 16, $base);
    }

    // Crop if needed (usually 1 char only).
    $ret = strcut($ret, $length);

    return $ret;
}

/**
 * Get request id.
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
 * Get temporary directory.
 * @param  string|null $subdir
 * @return string
 * @since  4.0
 */
function get_temporary_directory(string $subdir = null): string
{
    $dir = sys_get_temp_dir() . __dirsep .'froq-temporary'. (
        $subdir ? __dirsep . trim($subdir, __dirsep) : ''
    );

    is_dir($dir) || mkdir($dir, 0755, true);

    return __dirsep . trim($dir, __dirsep);
}

/**
 * Get cache directory.
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
 * @return ?string
 * @since  4.0
 */
function get_real_user(): ?string
{
    try {
        return posix_getpwuid(posix_geteuid())['name'] ?? null;
    } catch (Error $e) {
        return getenv('USER') ?: getenv('USERNAME') ?: null;
    }
}

/**
 * Get real path.
 * @param string $target
 * @param bool   $strict
 * @return ?string
 * @since  4.0
 */
function get_real_path(string $target, bool $strict = false): ?string
{
    $target = trim($target);
    if (!$target) {
        return null;
    }

    $ret = '';
    $exp = explode(__dirsep, $target);

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
                        // Search until finding the right target argument (sadly seems no way else
                        // for that when call stack is chaining from a function to another function).
                        if (empty($trace['args'][0]) || $trace['args'][0] != $target) {
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
 * Get trace (get a bit detailed trace with default options, limit & index option).
 * @param  int|null $options
 * @param  int|null $limit
 * @param  int|null $index
 * @return ?array
 * @since  4.0
 */
function get_trace(int $options = null, int $limit = null, int $index = null): ?array
{
    $trace = debug_backtrace($options ?? 0, $limit ? $limit + 1 : 0);
    array_shift($trace); // Drop self.

    foreach ($trace as $i => &$cur) {
        $cur += [
            'caller'   => null,
            'callee'   => $cur['function'] ?? null,
            'callPath' => $cur['file'] .':'. $cur['line'],
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
 * Tmp (gets system temporary directory).
 * @return string
 * @since  4.0
 */
function tmp(): string
{
    return dirname(get_temporary_directory());
}

/**
 * Tmpdir (creates a folder in system temporary directory).
 * @param  string|null $prefix
 * @param  int         $mode
 * @return string
 * @since  4.0
 */
function tmpdir(string $prefix = null, int $mode = 755): string
{
    $dir = ( // Eg: "/tmp/froq-5f858f253527c91a4006".
        tmp() . __dirsep . ($prefix ?? 'froq-') . get_uniqid(20)
    );

    is_dir($dir) || mkdir($dir, $mode);

    return $dir;
}

/**
 * Mkfile (creates a file with given path).
 * @param  string  $file
 * @param  int     $mode
 * @return ?bool
 * @since  4.0
 */
function mkfile(string $file, int $mode = 0644): ?bool
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return null;
    }

    $file = get_real_path($file);

    if (is_dir($file)) {
        trigger_error(sprintf(
            '%s(): Cannot create %s, it is a directory', __function__, $file
        ));
        return null;
    } elseif (is_file($file)) {
        trigger_error(sprintf(
            '%s(): Cannot create %s, it is already exists', __function__, $file
        ));
        return null;
    }

    $ok = is_dir(dirname($file)) || mkdir(dirname($file), 0755, true);

    return $mode ? ($ok && touch($file) && chmod($file, $mode))
                 : ($ok && touch($file));
}

/**
 * Rmfile (removes a file).
 * @param  string $file
 * @return ?bool
 * @since  4.0
 */
function rmfile(string $file): ?bool
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return null;
    }

    $file = get_real_path($file);

    if (is_dir($file)) {
        trigger_error(sprintf(
            '%s(): Cannot remove %s, it is a directory', __function__, $file
        ));
        return null;
    }

    return is_file($file) && unlink($file);
}

/**
 * Mkdirtemp (creates a folder in system temporary directory).
 * @alias of tmpdir().
 * @since 4.0
 */
function mkdirtemp(...$args): ?string
{
    return tmpdir(...$args);
}

/**
 * Rmdirtemp (removes a folder from system temporary directory).
 * @param  string $dir
 * @return ?bool
 * @since 4.0
 */
function rmdirtemp(string $dir): ?bool
{
    if (dirname($dir) != tmp()) {
        trigger_error(sprintf('%s(): Cannot remove a directory which is outside of %s directory',
            __function__, tmp()));
        return null;
    }

    // Clean inside but not recursive.
    foreach (glob($dir .'/*') as $file) {
        unlink($file);
    }

    return is_dir($dir) && rmdir($dir);
}

/**
 * Mkfiletemp (creates a file in temporary directory).
 * @param  string|null $extension
 * @param  int         $mode
 * @param  bool        $froq_temp
 * @return ?string
 * @since  4.0
 */
function mkfiletemp(string $extension = null, int $mode = 644, bool $froq_temp = true): ?string
{
    $file = ( // Eg: "/tmp/froq-temporary/5f858f253527c91a4006".
        ($froq_temp ? get_temporary_directory() : dirname(get_temporary_directory()))
        . __dirsep . get_uniqid(20)
        . ($extension ? '.'. trim($extension, '.') : '')
    );

    return mkfile($file, $mode) ? $file : null;
}

/**
 * Rmfiletemp (removes a file from in temporary directory).
 * @param  string $file
 * @return ?bool
 * @since  4.0
 */
function rmfiletemp(string $file): ?bool
{
    if (!strpfx($file, tmp())) {
        trigger_error(sprintf('%s(): Cannot remove a file which is outside of %s directory',
            __function__));
        return null;
    }

    return is_file($file) && unlink($file);
}

/**
 * Fopentemp (opens a temporary file).
 * @return ?resource
 * @since  4.0
 */
function fopentemp()
{
    return tmpfile() ?: null;
}

/**
 * Frewind (rewinds the file pointer).
 * @param  resource &$fp
 * @return bool
 * @since  4.0
 */
function frewind(&$fp): bool
{
    return rewind($fp);
}

/**
 * Freset (resets the file pointer contents & position).
 * @param  resource &$fp
 * @param  string    $contents
 * @return bool
 * @since  4.0
 */
function freset(&$fp, string $contents): bool
{
    rewind($fp);

    return ftruncate($fp, 0) && fwrite($fp, $contents) && !fseek($fp, 0);
}

/**
 * Fmeta (gets the file pointer metadata).
 * @param  resource $fp
 * @return array
 * @since  4.0
 */
function fmeta($fp): array
{
    return stream_get_meta_data($fp);
}

/**
 * Finfo (gets the file pointer statistics & metadata).
 * @param  resource $fp
 * @return array
 * @since  4.0
 */
function finfo($fp): array
{
    return fstat($fp) + ['meta' => stream_get_meta_data($fp)];
}

/**
 * Stream set contents (resets the handle contents & position).
 * @param  resource &$handle
 * @param  string    $contents
 * @return bool
 * @throws TypeError
 * @since  4.0
 */
function stream_set_contents(&$handle, string $contents): bool
{
    if (!is_resource($handle) || get_resource_type($handle) != 'stream') {
        throw new TypeError(sprintf(
            '%s() expects parameter 1 to be resource, %s given', __function__, get_type($handle)
        ));
    }

    // Since handle stat size also pointer position is not changing even after ftruncate() for
    // files (not "php://temp" etc), we rewind the handle.
    rewind($handle);

    return ftruncate($handle, 0) // Empty.
        && fwrite($handle, $contents) && !fseek($handle, 0); // Write & rewind.
}

/**
 * Udate (inits a DateTime object by given "when" option & with timezone if given or default timezone).
 * @param  int|float|string $when
 * @param  string|null      $where
 * @return DateTime
 * @throws TypeError
 * @since  4.25
 */
function udate($when = null, string $where = null): DateTime
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
        default:
            throw new TypeError(sprintf(
                'Argument $when must be int|float|string or null, %s given', get_type($when)
            ));
    }

    return $date;
}

/**
 * Utime (gets microtime as float or string).
 * @param  bool $as_string
 * @return float|string
 * @since  4.0
 */
function utime(bool $as_string = false)
{
    return !$as_string ? microtime(true) : sprintf('%.6F', microtime(true));
}

/**
 * Strtoitime (gets an interval time by given format).
 * @param  string   $format
 * @param  int|null $time
 * @return ?int
 * @since  4.0
 */
function strtoitime(string $format, int $time = null): ?int
{
    // Eg: "1 day" or "1D" (instead "60*60*24" or "86400").
    if (preg_match_all('~([+-]?\d+)([smhDMY])~', $format, $matches)) {
        $format_list = null;

        [, $numbers, $formats] = $matches;
        foreach ($formats as $i => $format) {
            switch ($format) {
                case 's': $format_list[] = $numbers[$i] .' second'; break;
                case 'm': $format_list[] = $numbers[$i] .' minute'; break;
                case 'h': $format_list[] = $numbers[$i] .' hour';   break;
                case 'D': $format_list[] = $numbers[$i] .' day';    break;
                case 'M': $format_list[] = $numbers[$i] .' month';  break;
                case 'Y': $format_list[] = $numbers[$i] .' year';   break;
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
 * Preg test (perform a regular expression search returning a bool result).
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
 * Preg remove (perform a regular expression search and remove).
 * @param  string|array  $pattern
 * @param  string|array  $subject
 * @param  int|null      $limit
 * @param  int|null     &$count
 * @return string|array|null
 * @since  4.0
 */
function preg_remove($pattern, $subject, int $limit = null, int &$count = null)
{
    if (is_string($pattern)) {
        $replacement = '';
    } elseif (is_array($pattern)) {
        $replacement = array_fill(0, count($pattern), '');
    }

    return preg_replace($pattern, $replacement, $subject, $limit ?? -1, $count);
}

/**
 * Array clean (cleans given array filtering/dropping non-empty values).
 * @param  array $array
 * @return array
 * @since  4.0
 */
function array_clean(array $array): array
{
    return array_filter($array, function ($value) {
        return ($value !== null && $value !== '' && $value !== []);
    });
}

/**
 * Array apply (apply the given function to each element of the given array).
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
            $ret[$key] = $func($value, $key, $i++);
        } catch (ArgumentCountError $e) {
            $ret[$key] = $func($value);
        }
    }

    return $ret;
}

/**
 * Array isset (tests all given keys are set in given array).
 * @param  array     $array
 * @param  array|... $keys
 * @return ?bool
 * @since  4.0
 */
function array_isset(array $array, ...$keys): ?bool
{
    $keys = array_clean( // Eg: ($array, 'a', 'b' or ['a', 'b']).
        isset($keys[0]) && is_array($keys[0]) ? $keys[0] : $keys
    );

    if (!$keys) {
        trigger_error(sprintf('%s(): No keys given', __function__));
        return null;
    }

    foreach ($keys as $key) {
        if (!isset($array[$key])) {
            return false;
        }
    }

    return true;
}

/**
 * Array unset (drops all given keys from given array).
 * @param  array    &$array
 * @param  array|... $keys
 * @return ?array
 * @since  4.0
 */
function array_unset(array &$array, ...$keys): ?array
{
    $keys = array_clean( // Eg: ($array, 'a', 'b' or ['a', 'b']).
        isset($keys[0]) && is_array($keys[0]) ? $keys[0] : $keys
    );

    if (!$keys) {
        trigger_error(sprintf('%s(): No keys given', __function__));
        return null;
    }

    foreach ($keys as $key) {
        unset($array[$key]);
    }

    return $array;
}

/**
 * Array append.
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
 * Array prepend.
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
 * Array top (for the sake of array_pop()).
 * @param  array &$array
 * @return any
 * @since  4.0
 */
function array_top(array &$array)
{
    return array_shift($array);
}

/**
 * Array unpop (for the sake of array_unshift()).
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
 * Array pad keys (ensures keys padding the given keys on array).
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
 * Array convert keys (convert keys cases mapping by given separator).
 * @param  array       $array
 * @param  int         $case
 * @param  string|null $sep
 * @return ?array
 * @since  4.19
 */
function array_convert_keys(array $array, int $case, string $spliter = null, string $joiner = null): ?array
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
 * Array value exists (checks a value if exists with strict comparison).
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
 * Array columns (selects columns only by given column keys).
 * @param  array           $array
 * @param  array           $column_keys
 * @param  int|string|null $index_key
 * @param  bool            $use_column_keys
 * @return array
 * @since  4.0
 */
function array_columns(array $array, array $column_keys, $index_key = null, bool $use_column_keys = false): array
{
    $ret = [];

    foreach ($array as $i => $value) {
        if (!is_array($value) && !is_object($value)) {
            trigger_error(sprintf(
                '%s(): Non-array/object value encountered at index %s', __function__, $i
            ));
            continue;
        }

        foreach ($column_keys as $ki => $key) {
            $columns = array_column($value, $key, $index_key);
            if ($columns) {
                foreach ($columns as $ci => $column) {
                    $i = !$use_column_keys ? $ki : $key;
                    if ($index_key === null || $index_key === '') {
                        $ret[$i][] = $column;
                    } else {
                        $ret[$i][$ci] = $column;
                    }
                }
            }
        }
    }

    return $ret;
}

/**
 * Array put.
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
 * Array pick.
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $default
 * @param  bool                          $drop
 * @return any|null
 * @since  4.9, 4.13 Default added.
 */
function array_pick(array &$array, $key, $default = null, bool $drop = false)
{
    // Just got sick of "value=array[..] ?? .." stuffs.
    return is_array($key) ? Arrays::getAll($array, $key, $default, $drop)
                          : Arrays::get($array, $key, $default, $drop);
}

/**
 * Array pluck.
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
 * Array rand value.
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
 * Array average.
 * @param  array $array
 * @param  bool  $include_zeros
 * @return float
 * @since  4.5, 4.20 Renamed from array_avg().
 */
function array_average(array $array, bool $include_zeros = true): float
{
    return Arrays::average($array);
}

/**
 * Array aggregate.
 * @param  array      $array
 * @param  callable   $func
 * @param  array|null $carry
 * @return array
 * @since  4.14, 4.15 Renamed from array_agg().
 */
function array_aggregate(array $array, callable $func, array $carry = null): array
{
    return Arrays::aggregate($array, $func, $carry);
}

/**
 * File create (create a file with given path).
 * @param  string $file
 * @param  int    $mode
 * @return ?string
 * @since  4.0
 */
function file_create(string $file, int $mode = 0644): ?string
{
    return mkfile($file, $mode) ? $file : null;
}

/**
 * File create temporary (alias of mkfiletemp()).
 * @since 4.0
 */
function file_create_temporary(...$args)
{
    return mkfiletemp(...$args);
}

/**
 * File remove (alias of rmfile()).
 * @since 4.0
 */
function file_remove(...$args)
{
    return rmfile(...$args);
}

/**
 * File write (alias of file_put_contents()).
 * @since 4.0
 */
function file_write(...$args)
{
    return file_put_contents(...$args) ?: null;
}

/**
 * File read (alias of file_get_contents()).
 * @since 4.0
 */
function file_read(...$args)
{
    return file_get_contents(...$args) ?: null;
}

/**
 * File read buffer (alias of file_get_buffer_contents()).
 * @since 4.0
 */
function file_read_buffer(...$args)
{
    return file_get_buffer_contents(...$args) ?: null;
}

/**
 * File set contents (sets a file contents).
 * @param  string $file
 * @param  string $contents
 * @param  int    $flags
 * @return ?int
 * @since  4.0
 */
function file_set_contents(string $file, string $contents, int $flags = 0): ?int
{
    $ret = file_put_contents($file, $contents, $flags);

    return ($ret !== false) ? $ret : null;
}

/**
 * Load a file & get its buffer (rendered) contents.
 *
 * @param  string     $file
 * @param  array|null $file_data
 * @return ?string
 * @since  4.0
 */
function file_get_buffer_contents(string $file, array $file_data = null): ?string
{
    if (!is_file($file)) {
        trigger_error(sprintf('%s(): No file exists such %s', __function__, $file));
        return null;
    } elseif (!strsfx($file, '.php')) {
        trigger_error(sprintf('%s(): Cannot include non-PHP file such %s', __function__, $file));
        return null;
    }

    ob_start();

    // Data, used in file.
    $file_data && extract($file_data);

    include $file;

    return ob_get_clean();
}

/**
 * File path (alias of get_real_path()).
 * @since 4.0
 */
function file_path(...$args)
{
    return get_real_path(...$args);
}

/**
 * File name (gets a file name).
 * @param  string $file
 * @param  bool   $with_extension
 * @return ?string
 * @since  4.0
 */
function file_name(string $file, bool $with_extension = true): ?string
{
    // Function basename() wants an explicit suffix to remove it from name,
    // but using just a boolean here is more sexy..
    $ret = basename($file);

    if ($ret && !$with_extension && ($extension = file_extension($file))) {
        $ret = substr($ret, 0, -strlen($extension));
    }

    return $ret ?: null;
}

/**
 * File extension (gets a file extension).
 * @param  string $file
 * @param  bool   $with_dot
 * @return ?string
 * @since  4.0
 */
function file_extension(string $file, bool $with_dot = true): ?string
{
    // Function pathinfo() returns ".foo" for example "/some/path/.foo" and
    // if $with_dot false then this function return ".", no baybe!
    $ret = strrchr($file, '.');

    if ($ret && !$with_dot) {
        $ret = ltrim($ret, '.');
    }

    return $ret ?: null;
}

/**
 * File type (gets a file (mime) type).
 * @param  string $file
 * @return ?string
 * @since  4.0
 */
function file_type(string $file): ?string
{
    $ret = null;

    if (is_file($file)) try {
        $ret = mime_content_type($file);
        if ($ret === false) try {
            $exec = exec('file -i '. escapeshellarg($file));
            if ($exec && preg_match('~: *([^/ ]+/[^; ]+)~', $exec, $match)) {
                $ret = $match[1];
                if ($ret == 'inode/directory') {
                    $ret = 'directory';
                }
            }
        } catch (Error $e) {}
    } catch (Error $e) {}

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
 * Get PHP' last error message.
 *
 * @return ?string
 * @since  4.17
 */
function error_message(): ?string
{
    return error_get_last()['message'] ?? null;
}

/**
 * Get PECL' last error message.
 *
 * @return ?string
 * @since  4.17
 */
function preg_error_message(): ?string
{
    // @todo: use preg_last_error_msg() [Froq/5.0, PHP/8.0].
    static $messages = [
        PREG_NO_ERROR              => null,
        PREG_INTERNAL_ERROR        => 'Internal PCRE error',
        PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit is exhausted',
        PREG_RECURSION_LIMIT_ERROR => 'Recursion limit is exhausted',
        PREG_BAD_UTF8_ERROR        => 'Bad UTF8 data',
        PREG_BAD_UTF8_OFFSET_ERROR => 'Bad UTF8 offset',
        PREG_JIT_STACKLIMIT_ERROR  => 'JIT stack limit exhausted',
    ];

    return $messages[preg_last_error()] ?? null;
}

/**
 * Get JSON' last error message.
 *
 * @return ?string
 * @since  4.17
 */
function json_error_message(): ?string
{
    // Check code first instead returning "No error" message.
    return json_last_error() ? json_last_error_msg() : null;
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
 * @param  bool $dashed
 * @return string
 * @since  5.0
 */
function uuid_hash(int $length = 32, bool $format = false): ?string
{
    switch ($length) {
        case 32: $ret = hash('md5', uuid()); break;
        case 40: $ret = hash('sha1', uuid()); break;
        case 64: $ret = hash('sha256', uuid()); break;
        case 16: $ret = hash('fnv1a64', uuid()); break;
        default:
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
