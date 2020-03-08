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

/**
 * Strsrc & strisrc (the ever most most most wanted functions..).
 * @param  string $str
 * @param  string $src
 * @param  int    $offset
 * @return bool
 * @since  4.0
 */
function strsrc(string $str, string $src, int $offset = 0): bool
{
    return strpos($str, $src, $offset) !== false;
}
function strisrc(string $str, string $src, int $offset = 0): bool
{
    return stripos($str, $src, $offset) !== false;
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
        $cut = substr($str, $pos + 1); // After (a).
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
 * Get uniqid.
 * @param  bool $convert
 * @return string
 * @since  4.0
 */
function get_uniqid(bool $convert = false): string
{
    if (!$convert) {
        return uniqid();
    }

    $parts = explode('.', uniqid('', true));

    return str_pad($parts[0] . dechex($parts[1]), 21, '0');
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
        // Base36 characters.
        static $chars = '0123456789abcdefghijklmnopqrstuvwxyz';

        $ret = base_convert(join($parts), 10, 36);
        while (strlen($ret) < 13) {
            $ret .= str_shuffle($chars)[0];
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
function get_random_uniqid(int $length = 10, bool $convert = false): string
{
    $bytes = random_bytes($length / 2);

    if (!$convert) {
        $ret = bin2hex($bytes);
    } else {
        // Base36 characters.
        static $chars = '0123456789abcdefghijklmnopqrstuvwxyz';

        $ret = base_convert(bin2hex($bytes), 16, 36);
        while (strlen($ret) < $length) {
            $ret .= str_shuffle($chars)[0];
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
        // Use an ephemeral port range if no port exists (http://www.ncftp.com/ncftpd/doc/misc/ephemeral_ports.html)
        $_SERVER['REMOTE_PORT'] ?? rand(49152, 65535));
}

/**
 * Get cache directory.
 * @return string
 * @since  4.0
 */
function get_cache_directory(): string
{
    $dir = get_temporary_directory() . DIRECTORY_SEPARATOR .'froq-cache';
    if (!is_dir($dir)) {
        mkdir($dir);
    }

    return $dir;
}

/**
 * Get temporary directory.
 * @return string
 * @since  4.0
 */
function get_temporary_directory(): string
{
    $dir = sys_get_temp_dir();
    if (!$dir || !is_dir($dir)) {
        $dir = DIRECTORY_SEPARATOR .'tmp';
        mkdir($dir);
    }

    return DIRECTORY_SEPARATOR . trim($dir, DIRECTORY_SEPARATOR);
}

/**
 * Get real user.
 * @return ?string
 * @since  4.0
 */
function get_real_user(): ?string
{
    if (function_exists('posix_geteuid')) {
        return posix_getpwuid(posix_geteuid())['name'] ?? null;
    } elseif (function_exists('exec')) {
        return exec('whoami');
    } else {
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
    $tmp = explode(DIRECTORY_SEPARATOR, $target);

    foreach ($tmp as $i => $cur) {
        $cur = trim($cur);
        if ($i == 0) {
            if ($cur == '~') { // Home path (eg: ~/Desktop).
                $ret = getenv('HOME');
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

        $ret .= DIRECTORY_SEPARATOR . $cur;
    }

    if ($strict && !realpath($ret)) {
        $ret = null;
    }

    return $ret;
}

/**
 * Mkfile.
 * @param  string   $file
 * @param  int|null $mode
 * @param  bool     $temp
 * @return bool
 * @since  4.0
 */
function mkfile(string $file, int $mode = null, bool $temp = false): bool
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    $file = get_real_path(!$temp ? $file : (
        get_temporary_directory() . DIRECTORY_SEPARATOR . $file
    ));

    if (is_dir($file)) {
        trigger_error(sprintf(
            '%s(): Cannot create %s, it is a directory', __function__, $file
        ));
        return false;
    } elseif (is_file($file)) {
        trigger_error(sprintf(
            '%s(): Cannot create %s, it is already exists', __function__, $file
        ));
        return false;
    }

    $dir = is_dir(dirname($file)) || mkdir(dirname($file), 0777, true);

    return !$mode ? ($dir && touch($file))
                  : ($dir && touch($file) && chmod($file, $mode));
}

/**
 * Rmfile.
 * @param  string $file
 * @param  bool   $temp
 * @return bool
 * @since  4.0
 */
function rmfile(string $file, bool $temp = false): bool
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    $file = get_real_path(!$temp ? $file : (
        get_temporary_directory() . DIRECTORY_SEPARATOR . $file
    ));

    if (is_dir($file)) {
        trigger_error(sprintf(
            '%s(): Cannot remove %s, it is a directory', __function__, $file
        ));
        return false;
    }

    return is_file($file) && unlink($file);
}

/**
 * Mkfiletemp (creates a new temporary file in temporary directory).
 * @param  bool $add_extension
 * @return string
 * @since  4.0
 */
function mkfiletemp(bool $add_extension = false): string
{
    if (!$add_extension) {
        $file = tempnam(get_temporary_directory(), 'froq-tmp-');
    } else {
        $file = get_temporary_directory() . DIRECTORY_SEPARATOR . uniqid('froq-') .'.tmp';
        if (!touch($file)) {
            $file = ''; // Error
        }
    }

    return $file;
}

/**
 * Rmfiletemp (alias of rmfile() for temporary files).
 * @param  string $file
 * @return bool
 * @since  4.0
 */
function rmfiletemp(string $file): bool
{
    return rmfile($file);
}

/**
 * Ftopen (opens a temporary file).
 * @return resource
 * @since  4.0
 */
function ftopen()
{
    return tmpfile();
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
 * Mtime (gets time, microtime, time + microtime).
 * @return array
 * @since 4.0
 */
function mtime(): array
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
    return (time() - date('Z'));
}

/**
 * Array pad keys (ensures keys padding the given keys on array).
 * @param  array  $array
 * @param  array  $keys
 * @param  any    $value
 * @return array
 * @since  4.0
 */
function array_pad_keys(array $array, array $keys, $value): array
{
    return array_replace(array_fill_keys($keys, $value), $array);
}

/**
 * Array value exists (checks a value if exists with strict comparison).
 *
 * @param  any   $value
 * @param  array $array
 * @return bool
 * @since  4.0
 */
function array_value_exists($value, array $array): bool
{
    return in_array($value, $array, true);
}
