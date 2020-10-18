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

namespace froq\util;

use froq\common\objects\StaticClass;
use froq\common\exceptions\InvalidArgumentException;

/**
 * Arrays.
 * @package froq\util
 * @object  froq\util\Arrays
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 * @static
 */
final class Arrays extends StaticClass
{
    /**
     * Is set.
     * @param  array $array
     * @return bool
     */
    public static function isSet(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Is map.
     * @param  array $array
     * @return bool
     */
    public static function isMap(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (!is_string($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set (with dot notation support for sub-array paths).
     * @param  array      &$array
     * @param  int|string  $key
     * @param  any         $value
     * @return array
     */
    public static function set(array &$array, $key, $value): array
    {
        // Usage:
        // Arrays::set($array, 'a.b.c', 1) => ['a' => ['b' => ['c' => 1]]]

        if (array_key_exists($key, $array)) { // Direct access.
            $array[$key] = $value;
        } else {
            $key = strval($key);

            if (strpos($key, '.') === false) {
                $array[$key] = $value;
            } else {
                $keys = explode('.', trim($key, '.'));

                if (count($keys) <= 1) { // Direct access.
                    $array[$keys[0]] = $value;
                } else { // Path access (with dot notation).
                    $current =& $array;

                    foreach ($keys as $key) {
                        if (isset($current[$key])) {
                            $current[$key] = (array) $current[$key];
                        }
                        $current =& $current[$key];
                    }

                    $current = $value;
                    unset($current);
                }
            }
        }

        return $array;
    }

    /**
     * Set all (with dot notation support for sub-array paths).
     * @param  array &$array
     * @param  array  $items
     * @return array
     * @since  4.0
     */
    public static function setAll(array &$array, array $items): array
    {
        foreach ($items as $key => $value) {
            self::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Get (with dot notation support for sub-array paths).
     * @param  array      &$array
     * @param  int|string  $key AKA path.
     * @param  any|null    $valueDefault
     * @param  bool        $drop
     * @return any|null
     */
    public static function get(array &$array, $key, $valueDefault = null, bool $drop = false)
    {
        // Usage:
        // $array = ['a' => ['b' => ['c' => ['d' => 1, 'd.e' => '...']]]]
        // Arrays::get($array, 'a.b.c.d') => 1
        // Arrays::get($array, 'a.b.c.d.e') => '...'

        $value = $valueDefault;
        if (empty($array)) {
            return $value;
        }

        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            if ($drop) { // Drop gotten item.
                unset($array[$key]);
            }
        } else {
            $key = strval($key);

            if (strpos($key, '.') === false) {
                $value = $array[$key] ?? $value;
                if ($drop) { // Drop gotten item.
                    unset($array[$key]);
                }
            } else {
                $keys = explode('.', trim($key, '.'));
                $key  = array_shift($keys);

                if (empty($keys)) {
                    if (array_key_exists($key, $array)) {
                        $value = $array[$key];
                        if ($drop) { // Drop gotten item.
                            unset($array[$key]);
                        }
                    }
                } elseif (isset($array[$key]) && is_array($array[$key])) { // Dig more..
                    $keys  = implode('.', $keys);
                    $value = self::get($array[$key], $keys, $value, $drop);
                }
            }
        }

        return $value;
    }

    /**
     * Get all (shortcuts like: list(..) = Arrays::getAll(..)).
     * @param  array   &$array
     * @param  array    $keys AKA paths.
     * @param  any|null $valueDefault
     * @param  bool     $drop
     * @return array
     */
    public static function getAll(array &$array, array $keys, $valueDefault = null, bool $drop = false): array
    {
        $values = [];

        foreach ($keys as $key) {
            if (is_array($key)) { // Default value given as array (eg: $keys=[['x',1], 'y']).
                @ [$_key, $_valueDefault] = $key;
                $values[] = self::get($array, $_key, $_valueDefault, $drop);
            } else {
                $values[] = self::get($array, $key, $valueDefault, $drop);
            }
        }

        return $values;
    }

    /**
     * Pull.
     * @param  array      &$array
     * @param  int|string  $key
     * @param  any|null    $valueDefault
     * @return any|null
     */
    public static function pull(array &$array, $key, $valueDefault = null)
    {
        return self::get($array, $key, $valueDefault, true);
    }

    /**
     * Pull all (shortcuts like: list(..) = Arrays::pullAll(..)).
     * @param  array    &$array
     * @param  array     $keys
     * @param  any|null  $valueDefault
     * @return array
     */
    public static function pullAll(array &$array, array $keys, $valueDefault = null): array
    {
        return self::getAll($array, $keys, $valueDefault, true);
    }

    /**
     * Remove.
     * @param  array      &$array
     * @param  int|string  $key
     * @return array
     * @since  4.0
     */
    public static function remove(array &$array, $key): array
    {
        self::pull($array, $key);

        return $array;
    }

    /**
     * Remove all.
     * @param  array &$array
     * @param  array  $keys
     * @return array
     * @since  4.0
     */
    public static function removeAll(array &$array, array $keys): array
    {
        self::pullAll($array, $keys);

        return $array;
    }

    /**
     * Compose.
     * @param  array    $keys
     * @param  array    $values
     * @param  any|null $valuesDefault
     * @return array
     * @since  4.11
     */
    public static function compose(array $keys, array $values, $valuesDefault = null): array
    {
        $ret = [];

        foreach ($keys as $i => $key) {
            $ret[$key] = $values[$i] ?? $valuesDefault;
        }

        return $ret;
    }

    /**
     * Test (like JavaScript Array.some()).
     * @param  array    $array
     * @param  callable $func
     * @return bool
     */
    public static function test(array $array, callable $func): bool
    {
        foreach ($array as $key => $value) {
            if ($func($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Test all (like JavaScript Array.every()).
     * @param  array    $array
     * @param  callable $func
     * @return bool
     */
    public static function testAll(array $array, callable $func): bool
    {
        foreach ($array as $key => $value) {
            if (!$func($value, $key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Find.
     * @param  array    $array
     * @param  callable $func
     * @return any|null
     * @since  4.10
     */
    public static function find(array $array, callable $func)
    {
        $i = 0; $ret = null;

        foreach ($array as $key => $value) {
            if ($func($value, $key, $i++)) {
                $ret = $value;
                break;
            }
        }

        return $ret;
    }

    /**
     * Find all.
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $useKeys
     * @return array
     * @since  4.10
     */
    public static function findAll(array $array, callable $func, bool $useKeys = false): array
    {
        $i = 0; $ret = [];

        foreach ($array as $key => $value) {
            if ($func($value, $key, $i++)) {
                !$useKeys ? $ret[] = $value : $ret[$key] = $value;
            }
        }

        return $ret;
    }

    /**
     * Random.
     * @param  array &$array
     * @param  int    $limit
     * @param  bool   $pack Return as [key,value] pairs.
     * @param  bool   $drop
     * @return any|null
     * @throws froq\common\exceptions\InvalidArgumentException
     */
    public static function random(array &$array, int $limit = 1, bool $pack = false, bool $drop = false)
    {
        $count = count($array);
        if ($count == 0) {
            return null;
        }

        if ($limit < 1) {
            throw new InvalidArgumentException('Minimum limit must be 1, %s given', [$limit]);
        } elseif ($limit > $count) {
            throw new InvalidArgumentException('Maximum limit must not be greater than %s, '.
                'given limit %s is exceeding the count of items', [$count, $limit]);
        }

        $keys = array_keys($array);
        shuffle($keys);

        $ret = [];
        while ($limit--) {
            $key = $keys[$limit];
            $value = $array[$key];

            !$pack ? $ret[] = $value : $ret[$key] = $value;

            // Drop used item.
            if ($drop) {
                unset($array[$key]);
            }
        }

        if (count($ret) == 1) {
            $ret = !$pack ? current($ret) : [key($ret), current($ret)];
        }

        return $ret;
    }

    /**
     * Shuffle.
     * @param  array &$array
     * @param  bool   $keepKeys
     * @return array
     */
    public static function shuffle(array &$array, bool $keepKeys = true): array
    {
        if (!$keepKeys) {
            shuffle($array);
        } else {
            $keys = array_keys($array);
            shuffle($keys);

            $shuffledArray = [];
            foreach ($keys as $key) {
                $shuffledArray[$key] = $array[$key];
            }
            $array = $shuffledArray;

            // Nope.. (cos killing speed and also randomness).
            // uasort($array, function () {
            //     return rand(-1, 1);
            // });
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
        return array_filter($array, fn($key) => in_array($key, $keys, true), 2);
    }

    /**
     * Exclude.
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function exclude(array $array, array $keys): array
    {
        return array_filter($array, fn($key) => !in_array($key, $keys, true), 2);
    }

    /**
     * Flatten.
     * @param  array $array
     * @param  bool  $useKeys
     * @param  bool  $fixKeys
     * @param  bool  $oneDimension
     * @return array
     * @since  4.0
     */
    public static function flatten(array $array, bool $useKeys = false, bool $fixKeys = false,
        bool $oneDimension = false): array
    {
        $ret = [];

        if (!$oneDimension) {
            $i = 0;
            // Seems short functions (=>) not work here [ref (&) issue].
            array_walk_recursive($array, function ($value, $key) use (&$ret, &$i, $useKeys, $fixKeys) {
                !$useKeys ? $ret[] = $value : (
                    !$fixKeys ? $ret[$key] = $value // Use original keys.
                              : $ret[is_string($key) ? $key : $i++] = $value // Re-index integer keys.
                );
            });
        } else {
            $ret = array_merge(...array_map(fn($value) => (array) $value, $array));
        }

        return $ret;
    }

    /**
     * Swap.
     * @param  array      &$array
     * @param  int|string  $oldKey
     * @param  int|string  $newKey
     * @param  any|null    $newValueDefault
     * @return array
     * @since  4.2
     */
    public static function swap(array &$array, $oldKey, $newKey, $newValueDefault = null): array
    {
        $newValue = self::pull($array, $oldKey);

        if ($newValue !== null) {
            self::set($array, $newKey, $newValue);
        } elseif (func_num_args() === 4) { // Create directive.
            self::set($array, $newKey, $newValueDefault);
        }

        return $array;
    }

    /**
     * Sweep.
     * @param  array      &$array
     * @param  array|null  $ignoredKeys
     * @return array
     * @since  4.0
     */
    public static function sweep(array &$array, array $ignoredKeys = null): array
    {
        // Memoize test function.
        static $test; $test or $test = (
            fn($v) => ($v !== '' && $v !== null && $v !== [])
        );

        if ($ignoredKeys == null) {
            $array = array_filter($array, $test);
        } else {
            foreach ($array as $key => $value) {
                if (!in_array($key, $ignoredKeys, true) && !$test($value)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    /**
     * Average.
     * @param  array $array
     * @param  bool  $includeZeros
     * @return float
     * @since  4.5
     */
    public static function average(array $array, bool $includeZeros = true): float
    {
        $array = array_filter($array, fn($v) => (
            $includeZeros ? is_numeric($v) : is_numeric($v) && ($v > 0)
        ));

        return array_sum($array) / count($array);
    }

    /**
     * Default.
     * @param  array    $array
     * @param  array    $keys
     * @param  bool     $useKeys
     * @param  any|null $default
     * @return array
     * @since  4.0
     */
    public static function default(array $array, array $keys, bool $useKeys = true, $default = null): array
    {
        $default = array_replace(array_fill_keys($keys, $default), $array);

        return $useKeys ? $default : array_values($default);
    }

    /**
     * Index.
     * @param  array $array
     * @param  any   $value
     * @param  bool  $strict
     * @return int|string|null
     * @since  4.0
     */
    public static function index(array $array, $value, bool $strict = true)
    {
        $key = array_search($value, $array, $strict);

        return ($key !== false) ? $key : null;
    }

    /**
     * First.
     * @param  array    &$array
     * @param  any|null  $valueDefault
     * @param  bool      $drop
     * @return any|null
     */
    public static function first(array &$array, $valueDefault = null, bool $drop = false)
    {
        $key   = array_key_first($array);
        $value = $valueDefault;

        if ($key !== null) {
            $value = $array[$key];
            if ($drop) {
                unset($array[$key]);
            }
        }

        return $value;
    }

    /**
     * Last.
     * @param  array    &$array
     * @param  any|null  $valueDefault
     * @param  bool      $drop
     * @return any|null
     */
    public static function last(array &$array, $valueDefault = null, bool $drop = false)
    {
        $key   = array_key_last($array);
        $value = $valueDefault;

        if ($key !== null) {
            $value = $array[$key];
            if ($drop) {
                unset($array[$key]);
            }
        }

        return $value;
    }

    /**
     * Keys exists.
     * @param  array $array
     * @param  array $keys
     * @return bool
     */
    public static function keysExists(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Values exists.
     * @param  array $array
     * @param  array $values
     * @param  bool  $strict
     * @return bool
     */
    public static function valuesExists(array $array, array $values, bool $strict = true): bool
    {
        foreach ($values as $value) {
            if (!in_array($value, $array, $strict)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Search keys.
     * @param  array $array
     * @param  array $keys
     * @return array
     * @since  4.0
     */
    public static function searchKeys(array $array, array $keys): array
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $values[] = $array[$key];
            }
        }
        return $values ?? [];
    }

    /**
     * Search values.
     * @param  array $array
     * @param  array $values
     * @param  bool  $strict
     * @return array
     * @since  4.0
     */
    public static function searchValues(array $array, array $values, bool $strict = true): array
    {
        foreach ($values as $value) {
            if (($key = array_search($value, $array, $strict)) !== false) {
                $keys[] = $key;
            }
        }
        return $keys ?? [];
    }

    /**
     * Get int.
     * @param  array      &$array
     * @param  int|string  $key
     * @param  int|null    $valueDefault
     * @param  bool        $drop
     * @return int
     */
    public static function getInt(array &$array, $key, int $valueDefault = null, bool $drop = false): int
    {
        return (int) self::get($array, $key, $valueDefault, $drop);
    }

    /**
     * Get float.
     * @param  array      &$array
     * @param  int|string  $key
     * @param  float|null  $valueDefault
     * @param  bool        $drop
     * @return float
     */
    public static function getFloat(array &$array, $key, float $valueDefault = null, bool $drop = false): float
    {
        return (float) self::get($array, $key, $valueDefault, $drop);
    }

    /**
     * Get string.
     * @param  array       &$array
     * @param  int|string   $key
     * @param  string|null  $valueDefault
     * @param  bool         $drop
     * @return string
     */
    public static function getString(array &$array, $key, string $valueDefault = null, bool $drop = false): string
    {
        return (string) self::get($array, $key, $valueDefault, $drop);
    }

    /**
     * Get bool.
     * @param  array      &$array
     * @param  int|string  $key
     * @param  bool|null   $valueDefault
     * @param  bool        $drop
     * @return bool
     */
    public static function getBool(array &$array, $key, bool $valueDefault = null, bool $drop = false): bool
    {
        return (bool) self::get($array, $key, $valueDefault, $drop);
    }
}
