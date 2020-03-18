<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

use froq\util\Objects;

// Ensure constants.
defined('nil') || require 'sugars-constant.php';

/**
 * Yes man..
 */
function equal($a, $b, ...$c): bool { return in_array($a, [$b, ...$c]); }
function equals($a, $b, ...$c): bool { return in_array($a, [$b, ...$c], true); }

/**
 * Strsrc & strisrc (the ever most most most wanted functions..).
 * @alias of str_contains(),str_ends_with(),str_starts_with()
 * @since 4.0
 */
function strsrc(string $str, string $src, bool $case_insensitive = false): bool
{
    return str_contains($str, $src, $case_insensitive);
}
function strasrc(string $str, string $src, bool $case_insensitive = false): bool
{
    return str_ends_with($str, $src, $case_insensitive);
}
function strbsrc(string $str, string $src, bool $case_insensitive = false): bool
{
    return str_starts_with($str, $src, $case_insensitive);
}

/**
 * Strsub.
 * @param  string   $str
 * @param  int      $start
 * @param  int|null $length
 * @return string
 * @since  4.0
 */
function strsub(string $str, int $start, int $length = null): string
{
    return !$length ? substr($str, $start) : substr($str, $start, $length);
}

/**
 * Strcut.
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
 * Stracut.
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  bool     $case_insensitive
 * @return ?string
 * @since  4.0
 */
function stracut(string $str, string $src, int $length = null, bool $case_insensitive = false): ?string
{
    $pos = !$case_insensitive ? strpos($str, $src) : stripos($str, $src);
    if ($pos !== false) {
        $cut = substr($str, $pos + strlen($src)); // After (a).
        return !$length ? $cut : strcut($cut, $length);
    }
    return null; // Not found.
}

/**
 * Strbcut.
 * @param  string   $str
 * @param  string   $src
 * @param  int|null $length
 * @param  bool     $case_insensitive
 * @return ?string
 * @since  4.0
 */
function strbcut(string $str, string $src, int $length = null, bool $case_insensitive = false): ?string
{
    $pos = !$case_insensitive ? strpos($str, $src) : stripos($str, $src);
    if ($pos !== false) {
        $cut = substr($str, 0, $pos); // Before (b).
        return !$length ? $cut : strcut($cut, $length);
    }
    return null; // Not found.
}


/**
 * Str contains (RFC: http://wiki.php.net/rfc/str_contains).
 * @param  string $str
 * @param  string $src
 * @return bool
 * @since  4.0
 */
function str_contains(string $str, string $src, bool $case_insensitive = false): bool
{
    return (!$case_insensitive ? strpos($str, $src) : stripos($str, $src)) !== false;
}

/**
 * Str starts with (RFC: http://wiki.php.net/rfc/add_str_begin_and_end_functions).
 * @param  string $str
 * @param  string $src
 * @param  bool   $case_insensitive
 * @return bool
 * @since  4.0
 */
function str_starts_with(string $str, string $src, bool $case_insensitive = false): bool
{
    return substr_compare($str, $src, 0, strlen($src), $case_insensitive) === 0;
}

/**
 * Str ends with (RFC: http://wiki.php.net/rfc/add_str_begin_and_end_functions).
 * @param  string $str
 * @param  string $src
 * @param  bool   $case_insensitive
 * @return bool
 * @since  4.0
 */
function str_ends_with(string $str, string $src, bool $case_insensitive = false): bool
{
    return substr_compare($str, $src, -strlen($src), null, $case_insensitive) === 0;
}

/**
 * Str base convert (converts given digits to chars from a chars, orginal source:
 * http://stackoverflow.com/a/4668620/362780).
 * @param  string $digits
 * @param  string $from_chars
 * @param  string $to_chars
 * @return string
 * @since  4.0
 */
function str_base_convert(string $digits, string $from_chars, string $to_chars): string
{
    [$digits_length, $from_base, $to_base] = [
        strlen($digits), strlen($from_chars), strlen($to_chars)];

    $numbers = [];
    for ($i = 0; $i < $digits_length; $i++) {
        $numbers[$i] = strpos($from_chars, $digits[$i]);
    }

    $ret = '';

    $old_len = $digits_length;
    do {
        $new_len = $div = 0;

        for ($i = 0; $i < $old_len; $i++) {
            $div = ($div * $from_base) + $numbers[$i];
            if ($div >= $to_base) {
                $numbers[$new_len++] = ($div / $to_base) | 0;
                $div = $div % $to_base;
            } elseif ($new_len > 0) {
                $numbers[$new_len++] = 0;
            }
        }

        $old_len = $new_len;

        // Prepend chars(n).
        $ret = $to_chars[$div] . $ret;
    } while ($new_len != 0);

    return $ret;
}

/**
 * Constant exists.
 * @param  object|string $class
 * @param  string        $name
 * @param  bool          $scope_check
 * @return ?bool
 * @since  4.0
 */
function constant_exists($class, string $name, bool $scope_check = true): ?bool
{
    if ($scope_check) {
        $callerClass =@ debug_backtrace(2, 2)[1]['class'];
        if ($callerClass) {
            return ($callerClass == Objects::getName($class))
                && Objects::hasConstant($class, $name);
        }
        return defined(Objects::getName($class) .'::'. $name);
    }
    return Objects::hasConstant($class, $name);
}

/**
 * Get class constants.
 * @param  string|object $class
 * @param  bool          $with_names
 * @param  bool          $scope_check
 * @return ?array
 * @since  4.0
 */
function get_class_constants($class, bool $with_names = true, bool $scope_check = true): ?array
{
    if ($scope_check) {
        $callerClass =@ debug_backtrace(2, 2)[1]['class'];
        if ($callerClass) {
            $all = ($callerClass == Objects::getName($class));
        }
    }
    return Objects::getConstantValues($class, $all ?? false, $with_names);
}

/**
 * Get class properties.
 * @param  string|object $class
 * @param  bool          $with_names
 * @param  bool          $scope_check
 * @return ?array
 * @since  4.0
 */
function get_class_properties($class, bool $with_names = true, bool $scope_check = true): ?array
{
    if ($scope_check) {
        $callerClass =@ debug_backtrace(2, 2)[1]['class'];
        if ($callerClass) {
            $all = ($callerClass == Objects::getName($class));
        }
    }
    return Objects::getPropertyValues($class, $all ?? false, $with_names);
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
    $type = gettype($var);

    if ($type == 'object') {
        $ret = get_class($var);
        // Anonymous class.
        if (($pos = strpos($ret, "\0")) > -1) {
            $ret = strsub($ret, 0, $pos);
        }
    } else {
        static $scalars_array   = ['int', 'float', 'string', 'bool'];
        static $translate_array = [
            'NULL'   => 'null',  'integer' => 'int',
            'double' => 'float', 'boolean' => 'bool'
        ];

        $ret = strtr($type, $translate_array);

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
 * Get uniqid.
 * @param  bool $long
 * @param  bool $convert
 * @return string
 * @since  4.0
 */
function get_uniqid(bool $long = false, bool $convert = false): string
{
    $parts = explode('.', uniqid('', true));

    if (!$long) {
        $ret = substr($parts[0], 0, 13);
    } else {
        if (!$convert) {
            $ret = str_pad($parts[0] . dechex($parts[1]), 21, '0');
        } else {
            $ret = base_convert(join($parts), 16, 36);
            while (strlen($ret) < 21) {
                $ret .= str_shuffle(BASE36_CHARACTERS)[0];
            }
        }
    }

    return $ret;
}

/**
 * Get nano uniqid.
 * @param  int    $length
 * @param  bool   $convert
 * @return string
 * @since  4.0
 */
function get_nano_uniqid(bool $convert = false): string
{
    // Use parts apart to prevent big number -> float issue.
    $parts = hrtime();

    if (!$convert) {
        $ret = dechex($parts[0]) . dechex($parts[1]);
        $ret = str_pad($ret, 13, '0');
    } else {
        $ret = base_convert(join($parts), 10, 36);
        while (strlen($ret) < 13) {
            $ret .= str_shuffle(BASE36_CHARACTERS)[0];
        }
    }

    return $ret;
}

/**
 * Get random uniqid.
 * @param  int    $length
 * @param  bool   $convert
 * @return string
 * @since  4.0
 */
function get_random_uniqid(int $length = 13, bool $convert = false): string
{
    $bytes = random_bytes(($length / 2) | 0);

    if (!$convert) {
        $pad = ''. rand(0, 9); // With a random number (only occurs if length / 2 is a float).
        $ret = str_pad(bin2hex($bytes), $length, $pad);
    } else {
        $ret = base_convert(bin2hex($bytes), 16, 36);
        while (strlen($ret) < $length) {
            $ret .= str_shuffle(BASE36_CHARACTERS)[0];
        }
    }

    return $ret;
}

/**
 * Get request id.
 * @return string
 * @since  4.0
 */
function get_request_id(): string
{
    sscanf(microtime(), '%d.%s %s', $_, $msec, $sec);

    return sprintf('%s-%s-%s', $sec, $msec,
        // Use an ephemeral port if no port exists (~$ cat /proc/sys/net/ipv4/ip_local_port_range)
        $_SERVER['REMOTE_PORT'] ?? rand(32768, 60999));
}

/**
 * Get temporary directory.
 * @return string
 * @since  4.0
 */
function get_temporary_directory(): string
{
    $dir = sys_get_temp_dir() . __dirsep .'froq-temporary';

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    return __dirsep . trim($dir, __dirsep);
}

/**
 * Get cache directory.
 * @return string
 * @since  4.0
 */
function get_cache_directory(): string
{
    $dir = dirname(get_temporary_directory()) . __dirsep .'froq-cache';

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    return $dir;
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
    if ($path = realpath($target)) {
        return $path;
    }

    $ret = '';
    $tmp = explode(__dirsep, $target);

    foreach ($tmp as $i => $cur) {
        $cur = trim($cur);
        if ($i == 0) {
            if ($cur == '~') { // Home path (eg: ~/Desktop).
                $ret = getenv('HOME') ?: '';
                continue;
            } elseif ($cur == '.' || $cur == '..') {
                if (!$ret) {
                    $ret = ($cur == '.') ? getcwd() : dirname(getcwd());
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

    if ($strict && !realpath($ret)) {
        $ret = null;
    }

    return $ret;
}

/**
 * Gettmp (gets system temporary dirname).
 * @return string
 * @since  4.0
 */
function gettmp(): string
{
    return dirname(get_temporary_directory());
}

/**
 * Mkfile (creates a new file).
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

    $ok = is_dir(dirname($file)) || mkdir(dirname($file), 0777, true);

    return $mode ? ($ok && touch($file) && chmod($file, $mode))
                 : ($ok && touch($file));
}

/**
 * Mkfiletemp (creates a new file in temporary directory).
 * @param  string|null $extension
 * @param  bool        $froq_temp
 * @return ?string
 * @since  4.0
 */
function mkfiletemp(string $extension = null, bool $froq_temp = true): ?string
{
    $file = get_real_path(
        ($froq_temp ? get_temporary_directory() : dirname(get_temporary_directory()))
        . __dirsep . get_uniqid(true)
        . ($extension ? '.'. trim($extension, '.') : '')
    );

    return mkfile($file) ? $file : null;
}

/**
 * Rmfile.
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
 * Ftopen (opens a temporary file).
 * @return ?resource
 * @since  4.0
 */
function ftopen()
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
 * @since  4.0
 */
function stream_set_contents(&$handle, string $contents): bool
{
    if (!is_resource($handle) || get_resource_type($handle) != 'stream') {
        throw new TypeError(sprintf(
            '%s() expects parameter 1 to be resource, %s given', __function__, gettype($handle)
        ));
    }

    // Since handle stat size also pointer position is not changing even after ftruncate() for
    // files (not "php://temp" etc), we rewind the handle.
    rewind($handle);

    return ftruncate($handle, 0) // Empty.
        && fwrite($handle, $contents) && !fseek($handle, 0); // Write & rewind.
}

/**
 * Times (gets time, microtime, time + microtime).
 * @return array
 * @since 4.0
 */
function times(): array
{
    sscanf(microtime(), '%f %i', $msec, $sec);

    return [$sec, $msec, ($sec + $msec)];
}

/**
 * Gmtime (gets Greenwich Mean time).
 * @return int
 * @since 4.0
 */
function gmtime(): int
{
    return time() - date('Z');
}

/**
 * Array clean (cleans given array filtering/dropping non-empty values).
 * @param  array $array
 * @return array
 * @since  4.0
 */
function array_clean(array $array): array
{
    return array_filter($array, fn($v) => ($v !== '' && $v !== null && $v !== []));
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
    $keys = array_clean(
        // Eg: ($array, 'a', 'b' or ['a', 'b']).
        isset($keys[0]) && is_array($keys[0]) ? $keys[0] : $keys
    );

    if (empty($keys)) {
        trigger_error(sprintf('%s(): No keys given', __function__));
        return null;
    }

    foreach ($keys as $key) {
        if (!isset($key)) {
            return false;
        }
    }

    return true;
}

/**
 * Array isset (drops all given keys from given array).
 * @param  array    &$array
 * @param  array|... $keys
 * @return ?array
 * @since  4.0
 */
function array_unset(array &$array, ...$keys): ?array
{
    $keys = array_clean(
        // Eg: ($array, 'a', 'b' or ['a', 'b']).
        isset($keys[0]) && is_array($keys[0]) ? $keys[0] : $keys
    );

    if (empty($keys)) {
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
 * Array value exists (checks a value if exists with strict comparison).
 * @param  any   $value
 * @param  array $array
 * @return bool
 * @since  4.0
 */
function array_value_exists($value, array $array): bool
{
    return in_array($value, $array, true);
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
                '%s(): Non-array/object value encountered at index %i', __function__, $i
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
 * Preg remove (perform a regular expression search and remove).
 * @param  string|array  $pattern
 * @param  string|array  $subject
 * @param  int|null      $limit
 * @param  int|null     &$count
 * @return string|array|null
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
 * File create (create a new file or a new temporary file).
 * @param  string|null $file
 * @param  int|null    $mode
 * @return ?string
 * @since  4.0
 */
function file_create(string $file = null, int $mode = 0644): ?string
{
    if ($file === null) {
        return mkfiletemp();
    }
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
 * File read (alias of file_get_contents()).
 * @since 4.0
 */
function file_read(...$args)
{
    return file_get_contents(...$args) ?: null;
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
 * File get buffer contents (loads & gets a file (rendered) buffer contents).
 * @param  string $file
 * @param  array  $file_data
 * @return ?string
 * @since  4.0
 */
function file_get_buffer_contents(string $file, array $file_data = null): ?string
{
    if (!is_file($file)) {
        trigger_error(sprintf(
            '%s(): No file exists such %s', __function__, $file
        ));
        return null;
    }

    ob_start();

    if ($file_data) {
        extract($file_data);
    }
    include $file;

    return ob_get_clean();
}

/**
 * File get type (gets a file (mime) type).
 * @param  string $file
 * @return ?string
 * @since  4.0
 */
function file_get_type(string $file): ?string
{
    $ret = null;

    if (is_file($file)) {
        try {
            $ret = function_exists('mime_content_type') ? mime_content_type($file) : false;
            if ($ret === false && function_exists('exec')) {
                $exec = exec('file -i '. escapeshellarg($file));
                if ($exec && preg_match('~: *([^/ ]+/[^; ]+)~', $exec, $match)) {
                    $ret = $match[1];
                    if ($ret == 'inode/directory') {
                        $ret = 'directory';
                    }
                }
            }
        } catch (Error $e) {}
    }

    // Try by extension.
    if (!$ret) {
        $extension = file_get_extension($file, false);
        if ($extension) {
            $extension = strtolower($extension);

            static $cache; // Some speed..

            if (empty($cache[$extension])) {
                foreach (include ('statics/mime.php') as $type => $extensions) {
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
 * File get extension (gets a file extension).
 * @param  string $file
 * @param  bool   $dotted
 * @return ?string
 * @since  4.0
 */
function file_get_extension(string $file, bool $dotted = true): ?string
{
    $ret = strrchr($file, '.');

    if ($ret && !$dotted) {
        $ret = ltrim($ret, '.');
    }

    return $ret ?: null;
}
