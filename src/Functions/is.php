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

/*** "IS" function module. ***/

/**
 * Local.
 * @return bool
 */
function is_local(): bool
{
    return (local === true);
}

/**
 * In.
 * @param  array        $array
 * @param  string|array $values
 * @return bool
 */
function is_in(array $array, $values): bool
{
    foreach ((array) $values as $value) {
        if (in_array($value, $array)) {
            return true;
        }
    }

    return false;
}

/**
 * In key.
 * @param  array        $array
 * @param  string|array $keys
 * @return bool
 */
function is_in_key(array $array, $keys): bool
{
    foreach ((array) $keys as $key) {
        if (array_key_exists($key, $array)) {
            return true;
        }
    }

    return false;
}

/**
 * Iter.
 * @param  any $arg
 * @return bool
 */
function is_iter($arg): bool
{
    return is_array($arg)
        || ($arg instanceof \stdClass)
        || ($arg instanceof \Traversable);
}

/**
 * Empty.
 * @param  ... $args
 * @return bool
 */
function is_empty(...$args): bool
{
    foreach ($args as $arg) {
        if (empty($arg)) {
            return true;
        }
        if ($arg instanceof \stdClass && empty((array) $arg)) {
            return true;
        }
    }

    return false;
}

/**
 * Nil.
 * @param  any $arg
 * @return bool
 */
function is_nil($arg): bool
{
    return ($arg === nil);
}

/**
 * None.
 * @param  any $arg
 * @return bool
 */
function is_none($arg): bool
{
    return ($arg === none);
}

/**
 * UInt.
 * @param  any $arg
 * @return bool
 */
function is_uint($arg): bool
{
    return is_numeric($arg) && !is_float($arg) && !strpbrk((string) $arg, '-.');
}
