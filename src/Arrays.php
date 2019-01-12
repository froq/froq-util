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

namespace Froq\Util;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Arrays
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final /* static */ class Arrays
{
    /**
     * Dig (with dot notation support for sub-array paths).
     * @param  array      $array
     * @param  int|string $key (aka path)
     * @param  any        $valueDefault
     * @return any
     */
    public static function dig(array $array, $key, $valueDefault = null)
    {
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
     */
    public static function pick(array &$array, $key, $valueDefault = null)
    {
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
     * Include.
     * @param  array $array
     * @param  array $keys
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
     * @return any|null
     */
    public static function first(array $array)
    {
        return $array[0] ?? null;
    }

    /**
     * Last.
     * @param  array $array
     * @return any|null
     */
    public static function last(array $array)
    {
        return $array[count($array)] ?? null;
    }

    /**
     * Fill.
     * @param  array    $array
     * @param  array    $keys
     * @param  any|null $value
     * @return array
     */
    public static function fill(array $array, array $keys, $value = null): array
    {
        // like array_fill_keys(), more like [] + []
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Get int.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return int
     */
    public static function getInt(array $array, $key, $valueDefault = null): int
    {
        return (int) self::dig($array, $key, $valueDefault);
    }

    /**
     * Get float.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return float
     */
    public static function getFloat(array $array, $key, $valueDefault = null): float
    {
        return (float) self::dig($array, $key, $valueDefault);
    }

    /**
     * Get string.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return string
     */
    public static function getString(array $array, $key, $valueDefault = null): string
    {
        return (string) self::dig($array, $key, $valueDefault);
    }

    /**
     * Get bool.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return bool
     */
    public static function getBool(array $array, $key, $valueDefault = null): bool
    {
        return (bool) self::dig($array, $key, $valueDefault);
    }
}
