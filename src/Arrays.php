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
 * @since      1.0
 */
final /* static */ class Arrays
{
    /**
     * Set (with dot notation support for sub-array paths).
     * @param  array      &$array
     * @param  int|string $key
     * @param  any        $valueDefault
     * @return any
     * @since  3.0
     */
    public static function set(array &$array, $key, $value): array
    {
        if (array_key_exists($key, $array)) { // direct access
            $array[$key] = $value;
        } else {
            $keys = explode('.', (string) $key);
            if (count($keys) <= 1) { // direct access
                $array[$key] = $value;
            } else { // path access (with dot notation)
                $current = &$array;
                foreach($keys as $key) {
                    $current = &$current[$key];
                }
                $current = $value;
                unset($current);
            }
        }

        return $array;
    }

    /**
     * Get (with dot notation support for sub-array paths).
     * @param  array      $array
     * @param  int|string $key
     * @param  any        $valueDefault
     * @return any
     */
    public static function get(array $array, $key, $valueDefault = null)
    {
        if (array_key_exists($key, $array)) { // direct access
            $value = $array[$key];
        } else {
            $keys = explode('.', (string) $key);
            if (count($keys) <= 1) { // direct access
                $value = $array[$key] ?? null;
            } else { // path access (with dot notation)
                $value = &$array;
                foreach ($keys as $key) {
                    if (!is_array($value) || !array_key_exists($key, $value)) {
                        $value = null;
                        break;
                    }
                    $value = &$value[$key];
                }
            }
        }

        return $value ?? $valueDefault;
    }

    /**
     * Get all (shortcuts like: list(..) = Arrays::getAll(..)).
     * @param  array  $array
     * @param  array  $keys (aka paths)
     * @param  any    $valueDefault
     * @return array
     */
    public static function getAll(array $array, array $keys, $valueDefault = null): array
    {
        $values = [];
        foreach ($keys as $key) {
            if (is_array($key)) { // default value given as array
                @ [$key, $valueDefault] = $key;
            }
            $values[] = self::get($array, $key, $valueDefault);
        }

        return $values;
    }

    /**
     * Pull.
     * @param  array      &$array
     * @param  int|string $key
     * @param  any        $valueDefault
     * @return any
     * @since  3.0
     */
    public static function pull(array &$array, $key, $valueDefault = null)
    {
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            unset($array[$key]); // remove pulled item
        }

        return $value ?? $valueDefault;
    }

    /**
     * Pull all.
     * @param  array  &$array
     * @param  array  $keys
     * @param  any    $valueDefault
     * @return array
     * @since  3.0
     */
    public static function pullAll(array &$array, array $keys, $valueDefault = null): array
    {
        $values = [];
        foreach ($keys as $key) {
            if (is_array($key)) { // default value given as array
                @ [$key, $valueDefault] = $key;
            }
            $values[] = self::pull($array, $key, $valueDefault);
        }

        return $values;
    }

    /**
     * Test (like JavaScript Array.some()).
     * @param  array    $array
     * @param  \Closure $func
     * @return bool
     */
    public static function test(array $array, \Closure $func): bool
    {
        foreach ($array as $key => $value) {
            if ($func($value, $key)) { return true; }
        }
        return false;
    }

    /**
     * Test all (like JavaScript Array.every()).
     * @param  array    $array
     * @param  \Closure $func
     * @return bool
     */
    public static function testAll(array $array, \Closure $func): bool
    {
        foreach ($array as $key => $value) {
            if (!$func($value, $key)) { return false; }
        }
        return true;
    }

    /**
     * Sort.
     * @param  array        &$array
     * @param  callable|null $func
     * @param  callable|null $ufunc
     * @param  int           $flags
     * @return array
     * @throws Froq\Util\UtilException
     */
    public static function sort(array &$array, callable $func = null, callable $ufunc = null, int $flags = 0): array
    {
        if ($func == null) {
            sort($array, $flags);
        } elseif ($func instanceof \Closure) {
            usort($array, $func);
        } elseif (is_string($func)) {
            if ($func[0] == 'u' && $ufunc == null) {
                throw new UtilException("Second argument must be callable when usort,uasort,".
                    "uksort given");
            }
            $arguments = [&$array, $flags];
            if ($ufunc != null) {
                if (in_array($func, ['sort', 'asort', 'ksort'])) {
                    $func = 'u'. $func; // update to user function
                }
                $arguments[1] = $ufunc; // replace flags with ufunc
            }
            call_user_func_array($func, $arguments);
        }

        return $array;
    }

    /**
     * Include.
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function include(array $array, array $keys): array
    {
        return array_filter($array, function($_, $key) use($keys) {
            return in_array($key, $keys);
        }, 1);
    }

    /**
     * Exclude.
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function exclude(array $array, array $keys): array
    {
        return array_filter($array, function($_, $key) use($keys) {
            return !in_array($key, $keys);
        }, 1);
    }

    /**
     * First.
     * @param  array $array
     * @param  any   $valueDefault
     * @return any|null
     */
    public static function first(array $array, $valueDefault = null)
    {
        return array_values($array)[0] ?? $valueDefault;
    }

    /**
     * Last.
     * @param  array $array
     * @param  any   $valueDefault
     * @return any|null
     */
    public static function last(array $array, $valueDefault = null)
    {
        return array_values($array)[count($array) - 1] ?? $valueDefault;
    }

    /**
     * Get int.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return int
     * @since  3.0
     */
    public static function getInt(array $array, $key, $valueDefault = null): int
    {
        return (int) self::get($array, $key, $valueDefault);
    }

    /**
     * Get float.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return float
     * @since  3.0
     */
    public static function getFloat(array $array, $key, $valueDefault = null): float
    {
        return (float) self::get($array, $key, $valueDefault);
    }

    /**
     * Get string.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return string
     * @since  3.0
     */
    public static function getString(array $array, $key, $valueDefault = null): string
    {
        return (string) self::get($array, $key, $valueDefault);
    }

    /**
     * Get bool.
     * @param  array      $array
     * @param  int|string $key
     * @param  any|null   $valueDefault
     * @return bool
     * @since  3.0
     */
    public static function getBool(array $array, $key, $valueDefault = null): bool
    {
        return (bool) self::get($array, $key, $valueDefault);
    }
}
