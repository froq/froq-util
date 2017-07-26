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

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Arrays
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Arrays
{
    /**
     * Dig (with dot notation support for sub-array paths).
     * @param  array      $array
     * @param  int|string $key (aka path)
     * @param  any        $valueDefault
     * @return any
     * @throws \InvalidArgumentException
     */
    public static function dig(array $array, $key, $valueDefault = null)
    {
        self::checkKeyType($key);

        if (array_key_exists($key, $array)) {
            $value = $array[$key]; // direct access
        } else {
            $value =& $array;       // path access
            foreach (explode('.', trim((string) $key)) as $key) {
                if (!is_array($value)) {
                    $value = null;
                    break;
                }
                $value =& $value[$key];
            }
        }

        return $value ?? $valueDefault;
    }

    /**
     * Dig all (shortcut like: list(..) = Arrays::digAll(..)).
     * @param  array  $array
     * @param  array  $keys (aka paths)
     * @param  any    $valueDefault
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function digAll(array $array, array $keys, $valueDefault = null): array
    {
        $values = [];
        foreach ($keys as $key) {
            if (is_array($key)) { // default value given as array
                [$key, $valueDefault] = $key;
            }
            $values[] = self::dig($array, $key, $valueDefault);
        }

        return $values;
    }

    /**
     * Pick.
     * @param  array      $array
     * @param  int|string $key
     * @param  any        $valueDefault
     * @return any
     * @throws \InvalidArgumentException
     */
    public static function pick(array &$array, $key, $valueDefault = null)
    {
        self::checkKeyType($key);

        $value = $valueDefault;
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            unset($array[$key]); // remove picked element
        }

        return $value;
    }

    /**
     * Pick all.
     * @param  array  &$array
     * @param  array  $keys
     * @param  any    $valueDefault
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function pickAll(array &$array, array $keys, $valueDefault = null): array
    {
        $values = [];
        foreach ($keys as $key) {
            if (is_array($key)) { // default value given as array
                [$key, $valueDefault] = $key;
            }
            $values[] = self::pick($array, $key, $valueDefault);
        }

        return $values;
    }

    /**
     * Map.
     * @param  array    $array
     * @param  callable $callback
     * @return array
     */
    public static function map(array $array, callable $callback): array
    {
        // strlen etc.
        if (is_string($callback)) {
            return array_map($callback, $array);
        }

        foreach ($array as $key => $value) {
            $array[$key] = $callback($key, $value);
        }

        return $array;
    }

    /**
     * Map key.
     * @param  array    $array
     * @param  callable $callback
     * @return array
     */
    public static function mapKey(array $array, callable $callback): array
    {
        return array_combine(self::map(array_keys($array), $callback), array_values($array));
    }

    /**
     * Filter.
     * @param  array     $array
     * @param  ?callable $callback
     * @return array
     */
    public static function filter(array $array, callable $callback = null)
    {
        if ($callback == null) {
            return array_filter($array);
        }

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
    public static function filterKey(array $array, callable $callback = null): array
    {
        return self::filter($array, function($key) use($callback) {
            return $callback($key, null);
        });
    }

    /**
     * Index.
     * @param  array $array
     * @param  any   $valueSearch
     * @param  bool  $strict
     * @return int|string|null
     */
    public static function index(array $array, $valueSearch, bool $strict = false)
    {
        return false !== ($index = array_search($valueSearch, $array, $strict)) ? $index : null;
    }

    /**
     * Index.
     * @param  array $array
     * @param  any   $valueSearch
     * @param  bool  $strict
     * @return int|string|null
     */
    public static function indexLast(array $array, $valueSearch, bool $strict = false)
    {
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                if (!$strict ? $value == $valueSearch : $value === $valueSearch) {
                    return $key;
                }
            } else {
                if (!$strict ? $value == $valueSearch : $value === $valueSearch) {
                    $index = $key;
                }
            }
        }

        return $index ?? null;
    }

    /**
     * Include.
     * @param  array $array
     * @param  array $keysInclude
     * @return array
     */
    public static function include(array $array, array $keys): array
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
    public static function exclude(array $array, array $keys): array
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
    public static function first(array $array)
    {
        if (!empty($array)) {
            reset($array);
            return current($array);
        }
    }

    /**
     * Last.
     * @param  array $array
     * @return any
     */
    public static function last(array $array)
    {
        if (!empty($array)) {
            reset($array);
            return end($array);
        }
    }

    /**
     * Check key type.
     * @param  int|string $key
     * @return void
     * @throws \InvalidArgumentException
     */
    private static function checkKeyType($key)
    {
        $keyType = gettype($key);
        if ($keyType != 'integer' && $keyType != 'string') {
            throw new \InvalidArgumentException("Key type must be int or string, {$keyType} given!");
        }
    }
}
