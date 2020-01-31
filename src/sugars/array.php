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

use froq\util\Arrays;

/**
 * Array set (with dot notation support for sub-array paths).
 * @param  array<int|string, any> &$array
 * @param  int|string              $key
 * @param  any                     $value
 * @return array<int|string, any>
 * @since  3.0
 */
function array_set(array &$array, $key, $value): array
{
    return Arrays::set($array, $key, $value);
}

/**
* Array set all (with dot notation support for sub-array paths).
* @param  array &$array
* @param  array  $items
* @return array
* @since  4.0
*/
function array_set_all(array &$array, array $items): array
{
    return Arrays::setAll($array, $items);
}

/**
 * Array get (with dot notation support for sub-array paths).
 * @param  array<int|string, any> &$array
 * @param  int|string              $key AKA path.
 * @param  any|null                $value_default
 * @return any|null
 * @since  3.0
 */
function array_get(array $array, $key, $value_default = null)
{
    return Arrays::get($array, $key, $value_default);
}

/**
 * Array get all (shortcuts like: list(..) = Arrays::getAll(..)).
 * @param  array<int|string, any> &$array
 * @param  array<int|string>       $keys AKA paths.
 * @param  any|null                $value_default
 * @return array
 * @since  3.0
 */
function array_get_all(array $array, array $keys, $value_default = null): array
{
    return Arrays::getAll($array, $keys, $value_default);
}

/**
 * Array pull.
 * @param  array<int|string, any> &$array
 * @param  int|string              $key
 * @param  any|null                $value_default
 * @return any|null
 * @since  3.0
 */
function array_pull(array &$array, $key, $value_default = null)
{
    return Arrays::pull($array, $key, $value_default);
}

/**
 * Array pull all (shortcuts like: list(..) = Arrays::pullAll(..)).
 * @param  array<int|string, any> &$array
 * @param  array<int|string>       $keys
 * @param  any|null                $value_default
 * @return array
 * @since  3.0
 */
function array_pull_all(array &$array, array $keys, $value_default = null): array
{
    return Arrays::pullAll($array, $keys, $value_default);
}

/**
 * Array test (like JavaScript Array.every()).
 * @param  array    $array
 * @param  callable $func
 * @return bool
 * @since  3.0
 */
function array_test(array $array, callable $func): bool
{
    return Arrays::test($array, $func);
}

/**
 * Array test all (like JavaScript Array.every()).
 * @param  array    $array
 * @param  callable $func
 * @return bool
 * @since  3.0
 */
function array_test_all(array $array, callable $func): bool
{
    return Arrays::testAll($array, $func);
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
 * @param  array &$array
 * @param  any    $value_default
 * @param  bool   $drop
 * @return any|null
 * @since  3.0
 */
function array_first(array $array, $value_default = null, bool $drop = false)
{
    return Arrays::first($array, $value_default, $drop);
}

/**
 * Array last.
 * @param  array &$array
 * @param  any    $value_default
 * @param  bool   $$drop
 * @return any|null
 * @since  3.0
 */
function array_last(array $array, $value_default = null, bool $drop = false)
{
    return Arrays::last($array, $value_default, $drop);
}

/**
 * Array keys exists.
 * @param  array $array
 * @param  array $keys
 * @return bool
 */
function array_keys_exists(array $array, array $keys): bool
{
    return Arrays::keysExists($array, $keys);
}

/**
 * Array values exists.
 * @param  array $array
 * @param  any   $values Multiple values accepted.
 * @param  bool  $strict
 * @return bool
 */
function array_values_exists(array $array, array $values, bool $strict = true): bool
{
    return Arrays::valuesExists($array, $values, $strict);
}

/**
 * Is sequential array.
 * @param  any $input
 * @return bool
 */
function is_sequential_array($input): bool
{
    return is_array($input) && Arrays::isSequentialArray($input);
}

/**
 * Is associative array.
 * @param  any $input
 * @return bool
 */
function is_associative_array($input): bool
{
    return is_array($input) && Arrays::isAssociativeArray($input);
}