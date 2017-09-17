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
use stdClass as object;

/**
 * To iter.
 * @param  iter $input
 * @return Froq\Util\Iter
 */
function to_iter($input): Iter
{
    return new Iter($input);
}

/**
 * To array.
 * @param  array|object $input
 * @param  bool         $deep
 * @return array
 */
function to_array($input, bool $deep = true): array
{
    $input = (array) $input;
    if ($input && $deep) {
        foreach ($input as $key => $value) {
            $input[$key] = is_iter($value)
                ? to_array($value, $deep) : $value;
        }
    }

    return $input;
}

/**
 * To object.
 * @param  array|object $input
 * @param  bool         $deep
 * @return object
 */
function to_object($input, bool $deep = true): object
{
    $input = (object) $input;
    if ($input && $deep) {
        foreach ($input as $key => $value) {
            $input->{$key} = is_iter($value) && is_array_assoc($value)
                ? to_object($value, $deep) : $value;
        }
    }

    return $input;
}

/**
 * To snake from dash (Foo-Bar -> Foo_Bar | foo_bar).
 * @param  string $input
 * @param  bool   $lower
 * @return string
 */
function to_snake_from_dash(string $input = null, bool $lower = true): string
{
    $input = str_replace('-', '_', (string) $input);

    if ($lower) {
        $input = strtolower($input);
    }

    return $input;
}

/**
 * To dash from upper (FooBar -> Foo-Bar | foo-bar).
 * @param  string $input
 * @param  bool   $lower
 * @return string
 */
function to_dash_from_upper(string $input = null, bool $lower = true): string
{
    $input = (string) preg_replace_callback('~(?!^)([A-Z])~', function($match) {
        return '-'. $match[0];
    }, $input);

    if ($lower) {
        $input = strtolower($input);
    }

    return $input;
}

/**
 * To snake from upper (fooBar | FooBar -> foo_bar)
 * @param  string $input
 * @param  bool   $lower
 * @return string
 */
function to_snake_from_upper(string $input = null, bool $lower = true): string
{
    $input = (string) preg_replace_callback('~(?!^)([A-Z])~', function($match) {
        return '_'. $match[1];
    }, $input);

    if ($lower) {
        $input = strtolower($input);
    }

    return $input;
}

/**
 * To upper from dash (foo-bar | Foo-Bar -> FooBar)
 * @param  string $input
 * @return string
 */
function to_upper_from_dash(string $input = null): string
{
    $input = (string) preg_replace_callback('~-([a-z])~i', function($match) {
        return ucfirst($match[1]);
    }, ucfirst((string) $input));

    return $input;
}

/**
 * To query string.
 * @param  array  $query
 * @param  string $keyIgnored
 * @return string
 */
function to_query_string(array $query, string $keyIgnored = ''): string
{
    $keyIgnored = explode(',', $keyIgnored);

    foreach ($query as $key => $_) {
        if (in_array($key, $keyIgnored)) unset($query[$key]);
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
