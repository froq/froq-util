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

use Froq\Util\Arrays;

/**
 * Array set (with dot notation support for sub-array paths).
 * @param  array      &$array
 * @param  int|string $key
 * @param  any        $valueDefault
 * @return any
 * @since  3.0
 */
function array_set(array &$array, $key, $value): array
{
    return Arrays::set($array, $key, $value);
}

/**
 * Array get (with dot notation support for sub-array paths).
 * @param  array      $array
 * @param  int|string $key
 * @param  any        $valueDefault
 * @return any
 * @since  3.0
 */
function array_get(array $array, $key, $valueDefault = null)
{
    return Arrays::get($array, $key, $valueDefault);
}

/**
 * Array get all (shortcuts like: list(..) = Arrays::getAll(..)).
 * @param  array  $array
 * @param  array  $keys (aka paths)
 * @param  any    $valueDefault
 * @return array
 * @since  3.0
 */
function array_get_all(array $array, array $keys, $valueDefault = null): array
{
    return Arrays::getAll($array, $keys, $valueDefault);
}

/**
 * Array pull.
 * @param  array      &$array
 * @param  int|string $key
 * @param  any        $valueDefault
 * @return any
 * @since  3.0
 */
function array_pull(array &$array, $key, $valueDefault = null)
{
    return Arrays::pull($array, $key, $valueDefault);
}

/**
 * Array pull all.
 * @param  array  &$array
 * @param  array  $keys
 * @param  any    $valueDefault
 * @return array
 * @since  3.0
 */
function array_pull_all(array &$array, array $keys, $valueDefault = null): array
{
    return Arrays::pullAll($array, $keys, $valueDefault);
}

/**
 * Array test (like JavaScript Array.every()).
 * @param  array    $array
 * @param  callable $fn
 * @return bool
 * @since  3.0
 */
function array_test(array $array, callable $fn): bool
{
    return Arrays::test($array, $fn);
}

/**
 * Array test all (like JavaScript Array.every()).
 * @param  array    $array
 * @param  callable $fn
 * @return bool
 * @since  3.0
 */
function array_test_all(array $array, callable $fn): bool
{
    return Arrays::testAll($array, $fn);
}

/**
 * Array include.
 * @param  array $array
 * @param  array $keys
 * @return array
 * @since  3.0
 */
function array_include(array $array, array $keys): array
{
    return Arrays::include($array, $keys);
}

/**
 * Array exclude.
 * @param  array $array
 * @param  array $keys
 * @return array
 * @since  3.0
 */
function array_exclude(array $array, array $keys): array
{
    return Arrays::exclude($array, $keys);
}

/**
 * Array first.
 * @param  array $array
 * @param  any   $valueDefault
 * @return any|null
 * @since  3.0
 */
function array_first(array $array, $valueDefault = null)
{
    return Arrays::first($array, $valueDefault);
}

/**
 * Array last.
 * @param  array $array
 * @param  any   $valueDefault
 * @return any|null
 * @since  3.0
 */
function array_last(array $array, $valueDefault = null)
{
    return Arrays::last($array, $valueDefault);
}

/**
 * Is array key.
 * @param  array $array
 * @param  any   $key Multiple keys accepted.
 * @return bool
 */
function is_array_key(array $array, $key): bool
{
    foreach ((array) $key as $key) {
        if (!array_key_exists($key, $array)) {
            return false;
        }
    }
    return true;
}

/**
 * Is array value.
 * @param  array $array
 * @param  any   $value Multiple values accepted.
 * @param  bool  $strict
 * @return bool
 */
function is_array_value(array $array, $value, bool $strict = false): bool
{
    foreach ((array) $value as $value) {
        if (!in_array($value, $array, $strict)) {
            return false;
        }
    }
    return true;
}

/**
 * Is assoc array.
 * @param  any $input
 * @return bool
 */
function is_assoc_array($input): bool
{
    if (is_array($input)) {
        foreach ($input as $key => $_) {
            if (is_string($key)) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Aliases of is_map_array().
 */
function is_map($input): bool
{
    return is_map_array($input);
}

/**
 * Is map array.
 * @param  any $input
 * @return bool
 */
function is_map_array($input): bool
{
    if ($ret = is_array($input)) {
        foreach ($input as $key => $_) {
            if (!is_string($key)) {
                $ret = false; break;
            }
        }
    }
    return $ret;
}

/**
 * Is int array.
 * @param  any $input
 * @return bool
 */
function is_int_array($input): bool
{
    if ($ret = is_array($input)) {
        foreach ($input as $key => $value) {
            if (!is_int($key) || !is_int($value)) {
                $ret = false; break;
            }
        }
    }
    return $ret;
}

/**
 * Is float array.
 * @param  any $input
 * @return bool
 */
function is_float_array($input): bool
{
    if ($ret = is_array($input)) {
        foreach ($input as $key => $value) {
            if (!is_int($key) || !is_float($value)) {
                $ret = false; break;
            }
        }
    }
    return $ret;
}

/**
 * Is string array.
 * @param  any $input
 * @return bool
 */
function is_string_array($input): bool
{
    if ($ret = is_array($input)) {
        foreach ($input as $key => $value) {
            if (!is_int($key) || !is_string($value)) {
                $ret = false; break;
            }
        }
    }
    return $ret;
}

/**
 * Is bool array.
 * @param  any $input
 * @return bool
 */
function is_bool_array($input): bool
{
    if ($ret = is_array($input)) {
        foreach ($input as $key => $value) {
            if (!is_int($key) || !is_bool($value)) {
                $ret = false; break;
            }
        }
    }
    return $ret;
}
