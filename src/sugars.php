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
    return substr_compare($str, $search, 0, strlen($search), $case_insensitive) === 0;
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
    return substr_compare($str, $search, -strlen($search), null, $case_insensitive) === 0;
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
    $exp = explode(DIRECTORY_SEPARATOR, $target);

    foreach ($exp as $i => $cur) {
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
 * @param  bool     $tmp
 * @return bool
 * @since  4.0
 */
function mkfile(string $file, int $mode = null, bool $tmp = false): bool
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    $file = get_real_path(!$tmp ? $file : (
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
 * @param  bool   $tmp
 * @return bool
 * @since  4.0
 */
function rmfile(string $file, bool $tmp = false): bool
{
    $file = trim($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    $file = get_real_path(!$tmp ? $file : (
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
 * Stream set contents.
 * @param  resource &$handle
 * @param  string    $contents
 * @param  bool      $swap
 * @return bool
 * @since  4.0
 */
function stream_set_contents(&$handle, string $contents, bool $swap = true): bool
{
    if (!is_resource($handle) || get_resource_type($handle) != 'stream') {
        trigger_error(sprintf(
            '%s(): Handle must be a stream resource, %s given', __function__, gettype($handle)
        ));
        return false;
    }

    // Since handle stat.size also pointer position is not changing even after ftruncate() for
    // files (not "php://temp" etc), we swap the handles closing old one.
    if ($swap) {
        $meta = stream_get_meta_data($handle);
        fclose($handle);

        $fp     = fopen($meta['uri'], 'w+b');
        $ok     = fwrite($fp, $contents) && rewind($fp);
        $handle = $fp; // Assign new.

        return $ok;
    }

    return ftruncate($handle, 0)      // Empty.
        && fwrite($handle, $contents) // Write.
        && rewind($handle);           // Rewind.
}
