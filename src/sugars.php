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

use froq\util\{Arrays, Objects};

// Ensure constants.
defined('nil') || require 'sugars-constant.php';

/**
 * Yes man..
 */
function equal($a, $b, ...$c): bool {
    return ($a == $b) || ($c && in_array($a, [$b, ...$c])); }
function equals($a, $b, ...$c): bool {
    return ($a === $b) || ($c && in_array($a, [$b, ...$c], true)); }

/**
 * The ever most wanted functions (finally come with 8.0, but without case option).
 * @alias of str_has(),str_has_prefix(),str_has_suffix()
 * @since 4.0
 */
function strsrc(...$args): bool { return str_has(...$args); }        // Search.
function strpfx(...$args): bool { return str_has_prefix(...$args); } // Search prefix.
function strsfx(...$args): bool { return str_has_suffix(...$args); } // Search suffix.

/**
 * Strsub (fun for substr() with null-length option).
 * @since 4.0
 */
function strsub(...$args): string
{
    if (!isset($args[2])) { // Check null-length.
         unset($args[2]);
    }

    return substr(...$args);
}

/**
 * Strran.
 * @param  string   $str
 * @param  int|null $length
 * @return ?string
 * @since  4.1, 4.6 Changed from strrnd().
 */
function strran(string $str, int $length = null): ?string
{
    if ($str == '') {
        trigger_error(sprintf('%s(): Empty string given', __function__));
        return null;
    }
    if ($length && $length < 1) {
        trigger_error(sprintf('%s(): Length must be minimum 1 or null', __function__));
        return null;
    }

    return !$length ? str_shuffle($str) : substr(str_shuffle($str), 0, $length);
}

/**
 * Strpad.
 * @param  string $str
 * @param  string $pstr
 * @param  int    $length
 * @param  int    $side
 * @return ?string
 * @since  4.6
 */
function strpad(string $str, string $pstr, int $length, int $side = STR_PAD_RIGHT): ?string
{
    return str_pad($str, $length, $pstr, $side) ?: null;
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
 * Strbcut.
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
 * Stracut.
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
 * Str base convert (converts given digits to chars from a chars, orginal source:
 * http://stackoverflow.com/a/4668620/362780).
 * @param  string     $digits
 * @param  int|string $from_chars
 * @param  int|string $to_chars
 * @return ?string
 * @throws TypeError
 * @since  4.0
 * @todo   Use "union" types for $from_chars & $to_chars.
 */
function str_base_convert(string $digits, $from_chars, $to_chars): ?string
{
    if (!is_int($from_chars) && !is_string($from_chars)) {
        throw new TypeError(sprintf(
            '%s() expects parameter 1 to be int|string, %s given', __function__, get_type($from_chars)
        ));
    } elseif (!is_int($to_chars) && !is_string($to_chars)) {
        throw new TypeError(sprintf(
            '%s() expects parameter 1 to be int|string, %s given', __function__, get_type($to_chars)
        ));
    }

    if (is_int($from_chars)) {
        if ($from_chars < 2 || $from_chars > 62) {
            trigger_error(sprintf('%s(): Invalid base for from chars, min=2 & max=62', __function__));
            return null;
        }
        $from_chars = strcut(BASE_62_CHARS, $from_chars);
    }
    if (is_int($to_chars)) {
        if ($to_chars < 2 || $to_chars > 62) {
            trigger_error(sprintf('%s(): Invalid base for to chars, min=2 & max=62', __function__));
            return null;
        }
        $to_chars = strcut(BASE_62_CHARS, $to_chars);
    }

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
    if (is_object($var)) {
        $ret = get_class($var);
        // Anonymous class.
        if ($pos = strpos($ret, "\0")) {
            $ret = strsub($ret, 0, $pos);
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
 * Get uniqid.
 * @param  bool $convert
 * @param  bool $extend
 * @return string
 * @since  4.0
 */
function get_uniqid(bool $convert = false, bool $extend = false): string
{
    $parts = explode('.', uniqid('', true));

    if (!$extend) {
        $ret = substr($parts[0], 0, 14);
        if (!$convert) {
            return $ret;
        }

        $ret = base_convert($ret, 16, 36); // Normally 11-length.
        $ret = str_pad($ret, 14, str_shuffle(BASE_36_CHARS));
    } else {
        if (!$convert) {
            $ret = substr($parts[0] . dechex($parts[1]), 0, 20);
            $ret = str_pad($ret, 20, '0'); // If it needs.
        } else {
            $ret = base_convert($parts[0], 16, 36) . base_convert($parts[1], 10, 36);
            $ret = str_pad($ret, 20, str_shuffle(BASE_36_CHARS)); // Yes it needs.
        }
    }

    return $ret;
}

/**
 * Get nano uniqid.
 * @param  int  $length
 * @param  bool $convert
 * @return string
 * @since  4.0
 */
function get_nano_uniqid(bool $convert = false): string
{
    // Use parts apart to prevent big number (to float) issue.
    $parts = hrtime();

    if (!$convert) {
        $ret = dechex($parts[0]) . dechex($parts[1]);
        $ret = str_pad($ret, 14, '0');
    } else {
        $ret = base_convert($parts[0], 10, 36) . base_convert($parts[1], 10, 36);
        $ret = str_pad($ret, 14, str_shuffle(BASE_36_CHARS));
    }

    return $ret;
}

/**
 * Get random uniqid.
 * @param  bool $convert
 * @param  int  $length
 * @return string
 * @since  4.0
 */
function get_random_uniqid(bool $convert = false, int $length = 14): string
{
    $rands = '';
    while (strlen($rands) < $length) {
        $rands .= strran(BASE_16_CHARS, 1);
    }

    if (!$convert) {
        $ret = $rands;
    } else {
        $ret = base_convert($rands, 16, 36);
        $ret = str_pad($ret, $length, str_shuffle(BASE_36_CHARS));
    }

    return $ret;
}

/**
 * Get extended uniqid.
 * @param  bool $convert
 * @return string
 * @since  4.8
 */
function get_extended_uniqid(bool $convert = false): string
{
    return get_uniqid($convert, true);
}

/**
 * Get request id.
 * @return string
 * @since  4.0
 */
function get_request_id(): string
{
    sscanf(microtime(), '%d.%s %s', $_, $msec, $sec);

    // Use an ephemeral port if no port exists (~$ cat /proc/sys/net/ipv4/ip_local_port_range)
    $port = $_SERVER['REMOTE_PORT'] ?? rand(32768, 60999);

    return sprintf('%s-%s-%s', $sec, $msec, $port);
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
        mkdir($dir, 0755, true);
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
        mkdir($dir, 0755, true);
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
    $trace = debug_backtrace($options ?? 0, $limit ?? 0);
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

    $ok = is_dir(dirname($file)) || mkdir(dirname($file), 0755, true);

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
    $file = ( // Seems like "/tmp/froq-temporary/5f858f253527c91a4006" fully.
        ($froq_temp ? get_temporary_directory() : dirname(get_temporary_directory()))
        . __dirsep . get_extended_uniqid()
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
 * Utime (gets microtime as string or float).
 * @param  bool $float
 * @return string|float
 * @since  4.0
 */
function utime(bool $float = false)
{
    return !$float ? utimes()[3] : utimes()[2];
}

/**
 * Utimes (gets time, microtime, time + microtime & time + microtime string).
 * @return array
 * @since  4.0
 */
function utimes(): array
{
    sscanf(microtime(), '%f %i', $msec, $sec);

    return [$sec, $msec, $fsec = ($sec + $msec), number_format($fsec, 6, '.', '')];
}

/**
 * Gmtime (gets Greenwich Mean time).
 * @return int
 * @since  4.0
 */
function gmtime(): int
{
    return time() - date('Z');
}

/**
 * Strtoitime (gets an interval time by given format, eg: "1 day" or "1D" instead of "60*60*24" or "86400").
 * @param  string   $format
 * @param  int|null $time
 * @return ?int
 * @since  4.0
 */
function strtoitime(string $format, int $time = null): ?int
{
    if (preg_match_all('~([+-]?\d+)([smhDMY])~', $format, $matches)) {
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

        if (isset($format_list)) {
            $format = join(' ', $format_list);
        }
    }

    $time = $time ?? time();

    return strtotime($format, $time) - $time;
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
        return ($value !== '' && $value !== null && $value !== []);
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
    foreach ($array as $key => $value) {
        // Because array_map() tricky with array_keys() only for value => key notation, and also
        // just warns about argument count (e.g: if $func is strval()) and foolishly making all
        // values NULL; simply use this way here with try/catch, catching ArgumentCountError only.
        try {
            $ret[$key] = $func($value, $key);
        } catch (ArgumentCountError $e) {
            $ret[$key] = $func($value);
        }
    }

    return $ret ?? [];
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
 * Array avg (for the sake of array_sum()).
 * @param  array $array
 * @param  bool  $include_empties
 * @return float
 * @since  4.5
 */
function array_avg(array $array, bool $include_empties = true): float
{
    return Arrays::average($array, $include_empties);
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
    if (is_null($file)) {
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
 * File get buffer contents (loads & gets a file (rendered) buffer contents).
 * @param  string     $file
 * @param  array|null $file_data
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
 * @param  bool   $extensioned
 * @return ?string
 * @since  4.0
 */
function file_name(string $file, bool $extensioned = true): ?string
{
    // Function basename() wants an explicit suffix to remove it from name, but using
    // just a boolean here is more sexy..
    $ret = basename($file);

    if ($ret && !$extensioned && ($extension = file_extension($file))) {
        $ret = substr($ret, 0, -strlen($extension));
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
        $extension = file_extension($file, false);
        if ($extension) {
            $extension = strtolower($extension);

            static $cache; // Some speed..

            if (empty($cache[$extension])) {
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
 * File extension (gets a file extension).
 * @param  string $file
 * @param  bool   $dotted
 * @return ?string
 * @since  4.0
 */
function file_extension(string $file, bool $dotted = true): ?string
{
    // Function pathinfo() returns ".foo" for example "/some/path/.foo" and if $dotted false
    // then this function return ".", no baybe!
    $ret = strrchr($file, '.');

    if ($ret && !$dotted) {
        $ret = ltrim($ret, '.');
    }

    return $ret ?: null;
}
