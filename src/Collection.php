<?php
/**
 * Copyright (c) 2016 Kerem Güneş
 *    <k-gun@mail.com>
 *
 * GNU General Public License v3.0
 *    <http://www.gnu.org/licenses/gpl-3.0.txt>
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

namespace Froq\Util;

use Froq\Util\Exceptions\InvalidArgumentTypeException;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Collection
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Collection
{
    /**
     * Dig (with dot notation support for sub-array paths).
     * @param  array      $array
     * @param  int|string $key (aka path)
     * @param  any        $valueDefault
     * @return any
     * @throws Froq\Util\Exceptions\InvalidArgumentTypeException
     */
    final public static function dig(array $array, $key, $valueDefault = null)
    {
        $keyType = gettype($key);
        if ($keyType != 'integer' && $keyType != 'string') {
            throw new InvalidArgumentTypeException("Key type must be int or string, {$keyType} given!");
        }

        // direct access
        if (array_key_exists($key, $array)) {
            $value =& $array[$key];
        } else {
            // path access
            $value =& $array;
            foreach (explode('.', trim((string) $key)) as $key) {
                $value =& $value[$key];
            }
        }

        return ($value !== null) ? $value : $valueDefault;
    }

    /**
     * Pick.
     * @param  array      $array
     * @param  int|string $key
     * @param  any        $value
     * @return any
     * @throws Froq\Util\Exceptions\InvalidArgumentTypeException
     */
    final public static function pick(array &$array, $key, $value = null)
    {
        $keyType = gettype($key);
        if ($keyType != 'integer' && $keyType != 'string') {
            throw new InvalidArgumentTypeException("Key type must be int or string, {$keyType} given!");
        }

        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            // remove element
            unset($array[$key]);
        }

        return $value;
    }

    /**
     * Map.
     * @param  array    $array
     * @param  callable $callback
     * @return array
     */
    final static function map(array $array, callable $callback): array
    {
        return filter_var($array, \FILTER_CALLBACK, ['options' => $callback]);
    }

    /**
     * Map key.
     * @param  array    $array
     * @param  callable $callback
     * @return array
     */
    final static function mapKey(array $array, callable $callback): array
    {
        return array_combine(self::map(array_keys($array), $callback), array_values($array));
    }

    /**
     * Filter.
     * @param  array     $array
     * @param  ?callable $callback
     * @return array
     */
    final public static function filter(array $array, callable $callback = null)
    {
        if (!$callback) return array_filter($array);

        // strlen etc.
        if (is_string($callback)) {
            return array_filter($array, $callback);
        }

        foreach ($array as $key => $value) {
            if (!$callback($key, $value)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Filter key.
     * @param  array    $array
     * @param  ?callable $callback
     * @return array
     */
    final public static function filterKey(array $array, callable $callback = null): array
    {
        return self::filter($array, function($key) use($callback) {
            return $callback($key, null);
        });
    }

    /**
     * Include.
     * @param  array $array
     * @param  array $keysInclude
     * @return array
     */
    final public static function include(array $array, array $keys): array
    {
        $return = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $return[$key] = $array[$key];
            }
        }
        return $return;
    }

    /**
     * Exclude.
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    final public static function exclude(array $array, array $keys): array
    {
        $keys = array_map('strval', $keys);
        $return = [];
        foreach ($array as $key => $value) {
            if (!in_array(strval($key), $keys)) {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    /**
     * First.
     * @param  array $array
     * @return any
     */
    final public static function first(array $array)
    {
        return current($array);
    }

    /**
     * Last.
     * @param  array $array
     * @return any
     */
    final public static function last(array $array)
    {
        return end($array);
    }
}
