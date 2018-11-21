<?php
/**
 * Copyright (c) 2016 Kerem Güneş
 *     <k-gun@mail.com>
 *
 * GNU General Public License v3.0
 *     <http://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

use \stdClass as object;

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
    return (PHP_SAPI === 'cli');
}

/**
 * Is cli server.
 * @return bool
 */
function is_cli_server(): bool
{
    return (PHP_SAPI === 'cli-server');
}

/**
 * Is array key.
 * @param  array $array
 * @param  any   $keys
 * @return bool
 */
function is_array_key(array $array, $keys): bool
{
    foreach ((array) $keys as $key) {
        if (!array_key_exists($key, $array)) {
            return false;
        }
    }

    return true;
}

/**
 * Is array value.
 * @param  array $array
 * @param  any   $values
 * @param  bool  $strict
 * @return bool
 */
function is_array_value(array $array, $values, bool $strict = false): bool
{
    foreach ((array) $values as $value) {
        if (!in_array($value, $array, $strict)) {
            return false;
        }
    }

    return true;
}

/**
 * Is array assoc.
 * @param  array|any $input
 * @return bool
 */
function is_array_assoc($input): bool
{
    if (is_array($input)) {
        foreach ($input as $key => $_) {
            if (is_string($key)) return true;
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
    if ($return && !empty($keys)) {
        if (is_array($input)) {
            foreach ($keys as $key) {
                if (!isset($input[$key])) return false;
            }
        } elseif ($input instanceof object) {
            foreach ($keys as $key) {
                if (!isset($input->{$key})) return false;
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
        if (is_array($input) || $input instanceof object) {
            $input = (array) $input;
            if (empty($input)) return true;
        }
    }

    return false;
}

/**
 * Is nil.
 * @param  any $input
 * @return bool
 */
function is_nil($input): bool
{
    return ($input === nil);
}

/**
 * Is nils.
 * @param  any $input
 * @return bool
 */
function is_nils($input): bool
{
    return ($input === nils);
}
