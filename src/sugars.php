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
    return substr_compare($input, $search, 0, strlen($search), $case_insensitive) === 0;
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
    return substr_compare($input, $search, -strlen($search), null, $case_insensitive) === 0;
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
 * Mkfile.
 * @param  string   $file
 * @param  int|null $mode
 * @param  bool     $tmp
 * @return bool
 * @since  4.0
 */
function mkfile(string $file, int $mode = null, bool $tmp = false): bool
{
    $file = !$tmp ? $file : sys_get_temp_dir() .'/'. ltrim($file, '/');

    if (is_file($file)) {
        return true;
    }

    $dir = is_dir(dirname($file)) || mkdir(dirname($file), $mode ?? 0777, true);

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
    $file = !$tmp ? $file : sys_get_temp_dir() .'/'. ltrim($file, '/');

    return is_file($file) && unlink($file);
}

/**
 * Mktmpfile.
 * @param  string   $file
 * @param  int|null $mode
 * @return bool
 * @since  4.0
 */
function mktmpfile(string $file, int $mode = null): bool
{
    return mkfile($file, $mode, true);
}

/**
 * Rmtmpfile.
 * @param  string $file
 * @return bool
 * @since  4.0
 */
function rmtmpfile(string $file): bool
{
    return rmfile($file, true);
}
