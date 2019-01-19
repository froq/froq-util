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

/**
 * Is local.
 * @return bool
 */
function is_local(): bool
{
    return (local === true);
}

/**
 * Is cli.
 * @return bool
 */
function is_cli(): bool
{
    return (PHP_SAPI == 'cli');
}

/**
 * Is cli server.
 * @return bool
 */
function is_cli_server(): bool
{
    return (PHP_SAPI == 'cli-server');
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
 * Is plain object.
 * @param  any $input
 * @return bool
 */
function is_plain_object($input): bool
{
    return $input instanceof \stdClass;
}

/**
 * Is array like.
 * @param  any $input
 * @return bool
 */
function is_array_like($input): bool
{
    return is_array($input) || is_plain_object($input);
}

/**
 * Is iter.
 * @param  any $input
 * @return bool
 */
function is_iter($input): bool
{
    return is_iterable($input) || is_plain_object($input);
}

/**
 * Is set.
 * @param  any        $input
 * @param  array|null $keys
 * @return bool
 */
function is_set($input, array $keys = null): bool
{
    $return = isset($input);
    if ($return && $keys != null && is_array_like($input)) {
        $input = (array) $input;
        foreach ($keys as $key) {
            if (!isset($input[$key])) {
                return false;
            }
        }
    }

    return $return;
}

/**
 * Is empty.
 * @param  ... $inputs
 * @return bool
 */
function is_empty(...$inputs): bool
{
    foreach ($inputs as $input) {
        if (empty($input)) {
            return true;
        }

        if (is_array_like($input)) {
            $input = (array) $input;
            if (empty($input)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Is between.
 * @param  number $input
 * @param  number $smallValue
 * @param  number $bigValue
 * @return bool
 */
function is_between($input, $smallValue, $bigValue): bool
{
    return is_numeric($input) && ($input >= $smallValue && $input <= $bigValue);
}

/**
 * Is nil.
 * @param  any $input
 * @return bool
 */
function is_nil($input): bool
{
    return ($input === nil); // null
}

/**
 * Is nils.
 * @param  any $input
 * @return bool
 */
function is_nils($input): bool
{
    return ($input === nils); // null string ('')
}
