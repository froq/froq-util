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

use Froq\Util\Iter;

/*** "TO" function module. ***/

/**
 * Iter.
 * @param  iter $arg
 * @return Froq\Util\Iter
 */
function to_iter($arg): Iter
{
    return new Iter(to_iter_array($arg));
}

/**
 * Iter array.
 * @return iter $arg
 * @return ?array
 */
function to_iter_array($arg)
{
    $return = null;
    if (is_array($arg) || $arg instanceof \stdClass) {
        $return = (array) $arg;
    } elseif ($arg instanceof \Traversable) {
        $return = iterator_to_array($arg);
    } elseif (is_object($arg) && method_exists($arg, 'toArray')) {
        $return = $arg->toArray();
    }

    return $return;
}

/**
 * Iter object.
 * @param  iter $arg
 * @return ?\stdClass
 */
function to_iter_object($arg)
{
    $return = to_iter_array($arg);
    if ($return) {
        $return = (object) $return;
    }

    return $return;
}

/**
 * Array.
 * @param  iter $arg
 * @param  bool $deep
 * @return array
 */
function to_array($arg, bool $deep = true): array
{
    $arg = to_iter_array($arg);
    if ($arg  && $deep) {
        foreach ($arg as $key => $value) {
            $arg[$key] = is_iter($value)
                ? to_array($value, $deep) : $value;
        }
    }

    return $arg;
}

/**
 * Object.
 * @param  iter $arg
 * @param  bool $deep
 * @return \stdClass
 */
function to_object($arg, bool $deep = true): \stdClass
{
    $arg = to_iter_object($arg);
    if ($arg  && $deep) {
        foreach ($arg as $key => $value) {
            $arg->{$key} = is_iter($value)
                ? to_object($value, $deep) : $value;
        }
    }

    return $arg;
}

/**
 * Snake from dash (Foo-Bar -> Foo_Bar | foo_bar).
 * @param  string $arg
 * @param  bool   $lower
 * @return string
 */
function to_snake_from_dash(string $arg = null, bool $lower = true): string
{
    $arg = str_replace('-', '_', (string) $arg);
    if ($lower) {
        $arg = strtolower($arg);
    }

    return $arg;
}

/**
 * Dash from upper (FooBar -> Foo-Bar | foo-bar).
 * @param  string $arg
 * @param  bool   $lower
 * @return string
 */
function to_dash_from_upper(string $arg = null, bool $lower = true): string
{
    $arg = (string) preg_replace_callback('~([A-Z])~', function($match){
        return '-'. $match[0];
    }, $arg);
    $arg = trim($arg, '-');
    if ($lower) {
        $arg = strtolower($arg);
    }

    return $arg;
}

/**
 * Upper from dash (foo-bar | Foo-Bar -> FooBar)
 * @param  string $arg
 * @return string
 */
function to_upper_from_dash(string $arg = null): string
{
    $arg = (string) preg_replace_callback('~-([a-z])~i', function($match) {
        return ucfirst($match[1]);
    }, ucfirst((string) $arg));

    return $arg;
}

/**
 * Query string.
 * @param  array  $query
 * @param  string $keyIgnored
 * @return string
 */
function to_query_string(array $query, string $keyIgnored = ''): string
{
    $keyIgnored = explode(',', $keyIgnored);

    foreach ($query as $key => $_) {
        if (in_array($key, $keyIgnored)) {
            unset($query[$key]);
        }
    }

    $query = http_build_query($query);

    // strip tags
    if (false !== strpos($query, '%3C')) {
        $query = preg_replace('~%3C[\w]+(%2F)?%3E~simU', '', $query);
    }

    // normalize arrays
    if (false !== strpos($query, '%5D')) {
        $query = preg_replace('~%5B([\d]+)%5D~simU', '[]', $query);
        $query = preg_replace('~%5B([\w\.-]+)%5D~simU', '[\\1]', $query);
    }

    return trim($query);
}
