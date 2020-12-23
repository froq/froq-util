<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\util;

use froq\common\object\StaticClass;
use ValueError, ArgumentCountError;

/**
 * Arrays.
 *
 * @package froq\util
 * @object  froq\util\Arrays
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 * @static
 */
final class Arrays extends StaticClass
{
    /**
     * Check whether all keys are "int" in given array.
     *
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
     * Check whether all keys are "string" in given array.
     *
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
     * Put an item into given array, with dot notation support for sub-array paths.
     *
     * @param  array      &$array
     * @param  int|string  $key
     * @param  any         $value
     * @return array
     */
    public static function set(array &$array, int|string $key, $value): array
    {
        // Usage:
        // Arrays::set($array, 'a.b.c', 1) => ['a' => ['b' => ['c' => 1]]]

        if (array_key_exists($key, $array)) { // Direct access.
            $array[$key] = $value;
        } else {
            $key = (string) $key;

            if (!str_contains($key, '.')) {
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
     * Bridge method to set() for multiple items.
     *
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
     * Get an item form given array, with dot notation support for sub-array paths.
     *
     * @param  array      &$array
     * @param  int|string  $key AKA path.
     * @param  any|null    $default
     * @param  bool        $drop
     * @return any|null
     */
    public static function get(array &$array, int|string $key, $default = null, bool $drop = false)
    {
        // Usage:
        // $array = ['a' => ['b' => ['c' => ['d' => 1, 'd.e' => '...']]]]
        // Arrays::get($array, 'a.b.c.d') => 1
        // Arrays::get($array, 'a.b.c.d.e') => '...'

        $value = $default;
        if (empty($array)) {
            return $value;
        }

        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            if ($drop) { // Drop gotten item.
                unset($array[$key]);
            }
        } else {
            $key = (string) $key;

            if (!str_contains($key, '.')) {
                $value = $array[$key] ?? $value;
                if ($drop) { // Drop gotten item.
                    unset($array[$key]);
                }
            } else {
                $keys = explode('.', trim($key, '.'));
                $key  = array_shift($keys);

                // No more dig.
                if (empty($keys)) {
                    if (array_key_exists($key, $array)) {
                        $value = $array[$key];
                        if ($drop) { // Drop gotten item.
                            unset($array[$key]);
                        }
                    }
                }
                // Dig more..
                elseif (isset($array[$key]) && is_array($array[$key])) {
                    $value = self::get($array[$key], implode('.', $keys), $value, $drop);
                }
            }
        }

        return $value;
    }

    /**
     * Bridge method to get() for multiple items. Useful in some times eg. list(..) = Arrays::getAll(..).
     *
     * @param  array             &$array
     * @param  array<int|string>  $keys AKA paths.
     * @param  any|null           $default
     * @param  bool               $drop
     * @return array
     */
    public static function getAll(array &$array, array $keys, $default = null, bool $drop = false): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[] = self::get($array, $key, $default, $drop);
        }

        return $values;
    }

    /**
     * Pull an item from given array by a key.
     *
     * @param  array      &$array
     * @param  int|string  $key
     * @param  any|null    $default
     * @return any|null
     */
    public static function pull(array &$array, $key, $default = null)
    {
        return self::get($array, $key, $default, true);
    }

    /**
     * Bridge method to get() for multiple items. Useful in some times eg. list(..) = Arrays::pullAll(..).
     *
     * @param  array             &$array
     * @param  array<int|string>  $keys
     * @param  any|null           $default
     * @return array
     */
    public static function pullAll(array &$array, array $keys, $default = null): array
    {
        return self::getAll($array, $keys, $default, true);
    }

    /**
     * Remove an item from given array by a key.
     *
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
     * Bridge method to remove() for multiple items.
     *
     * @param  array             &$array
     * @param  array<int|string>  $keys
     * @return array
     * @since  4.0
     */
    public static function removeAll(array &$array, array $keys): array
    {
        self::pullAll($array, $keys);

        return $array;
    }

    /**
     * Get one/multi items from given array randomly.
     *
     * @param  array  &$array
     * @param  int     $limit
     * @param  bool    $pack
     * @param  bool    $drop
     * @return any|null
     * @since  4.12
     */
    public static function getRandom(array &$array, int $limit = 1, bool $pack = false, bool $drop = false)
    {
        return self::random($array, $limit, $pack, $drop);
    }

    /**
     * Pull one/multi items from given array randomly.
     *
     * @param  array  &$array
     * @param  int     $limit
     * @param  bool    $pack
     * @return any|null
     * @since  4.12
     */
    public static function pullRandom(array &$array, int $limit = 1, bool $pack = false)
    {
        return self::random($array, $limit, $pack, true);
    }

    /**
     * Pull one/multi items from given array randomly.
     *
     * @param  array  &$array
     * @param  int     $limit
     * @return any|null
     * @since  4.12
     */
    public static function removeRandom(array &$array, int $limit = 1): array
    {
        self::random($array, $limit, false, true);

        return $array;
    }

    /**
     * Compose an array with given keys/values, unlike errorizing array_combine() when keys/values count
     * not match.
     *
     * @param  array    $keys
     * @param  array    $values
     * @param  any|null $default
     * @return array
     * @since  4.11
     */
    public static function compose(array $keys, array $values, $default = null): array
    {
        $ret = [];

        foreach ($keys as $i => $key) {
            $ret[$key] = $values[$i] ?? $default;
        }

        return $ret;
    }

    /**
     * Complete an array keys checking given other arrays to find non-null/non-null string value.
     *
     * @param  bool     $nullStrings
     * @param  array    $keys
     * @param  array ...$arrays
     * @return array
     * @since  4.14
     */
    public static function complete(bool $nullStrings, array $keys, array ...$arrays): array
    {
        $ret = [];

        foreach ($keys as $key) {
            foreach ($arrays as $array) {
                $test = $nullStrings ? !isset($ret[$key]) || $ret[$key] === ''
                                     : !isset($ret[$key]);

                if ($test) { // Try array.key, ret.key (current) or set null (not ret.key ??= ..).
                    $ret[$key] = $array[$key] ?? $ret[$key] ?? null;
                }
            }
        }

        return $ret;
    }

    /**
     * Coalesce an array keys checking given other arrays to find non-null/non-null string value.
     *
     * @param  bool     $nullStrings
     * @param  array ...$arrays
     * @return array
     * @since  4.14
     */
    public static function coalesce(bool $nullStrings, array ...$arrays): array
    {
        $ret = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $test = $nullStrings ? !isset($ret[$key]) || $ret[$key] === ''
                                     : !isset($ret[$key]);

                if ($test) { // Try value, ret.key (current) or set null (not ret.key ??= ..).
                    $ret[$key] = $value ?? $ret[$key] ?? null;
                }
            }
        }

        return $ret;
    }

    /**
     * Test, like JavaScript Array.some().
     *
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
     * Test all, like JavaScript Array.every().
     *
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
     * Find first item that fulfills given test function.
     *
     * @param  array    $array
     * @param  callable $func
     * @return any|null
     * @since  4.10
     */
    public static function find(array $array, callable $func)
    {
        $ret = null; $i = 0;

        foreach ($array as $key => $value) {
            if ($func($value, $key, $i++)) {
                $ret = $value;
                break;
            }
        }

        return $ret;
    }

    /**
     * Find all all items that fulfill given test function.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $useKeys
     * @return array
     * @since  4.10
     */
    public static function findAll(array $array, callable $func, bool $useKeys = false): array
    {
        $ret = []; $i = 0;

        foreach ($array as $key => $value) {
            if ($func($value, $key, $i++)) {
                !$useKeys ? $ret[] = $value : $ret[$key] = $value;
            }
        }

        return $ret;
    }

    /**
     * Randomize given array, optionally returning as [key,value] pairs.
     *
     * @param  array &$array
     * @param  int    $limit
     * @param  bool   $pack
     * @param  bool   $drop
     * @return any|null
     */
    public static function random(array &$array, int $limit = 1, bool $pack = false, bool $drop = false)
    {
        $count = count($array);
        if ($count == 0) {
            return null;
        }

        // Prevent trivial corruption from limit errors, but notice.
        if ($limit < 1) {
            throw new ValueError(sprintf(
                '%s(): Minimum limit must be 1, %s given', $limit
            ));
        } elseif ($limit > $count) {
            throw new ValueError(sprintf(
                '%s(): Maximum limit must not be greater than %s, given limit %s is exceeding '.
                'count of given array(%s)', __method__, $count, $limit, $count
            ));
        }

        $ret = [];

        // Get & arrayify single keys (limit=1).
        $keys = (array) array_rand($array, $limit);

        foreach ($keys as $key) {
            !$pack ? $ret[] = $array[$key]
                   : $ret[$key] = $array[$key];

            // Drop used item.
            if ($drop) {
                unset($array[$key]);
            }
        }

        // Assign return by pack option.
        if (count($ret) == 1) {
            $ret = !$pack ? current($ret)
                          : [key($ret), current($ret)];
        }

        return $ret;
    }

    /**
     * Shuffle given array, keeping keys as default.
     *
     * @param  array &$array
     * @param  bool   $keepKeys
     * @return array
     */
    public static function shuffle(array &$array, bool $keepKeys = true): array
    {
        if ($keepKeys) {
            $keys = array_keys($array);
            shuffle($keys);

            $temp = [];
            foreach ($keys as $key) {
                $temp[$key] = $array[$key];
            }
            $array = $temp;

            // Nope.. (cos killing speed and also randomness).
            // uasort($array, function () {
            //     return rand(-1, 1);
            // });
        } else {
            shuffle($array);
        }

        return $array;
    }

    /**
     * Filter given array including given keys.
     *
     * @param  array             $array
     * @param  array<int|string> $keys
     * @return array
     */
    public static function include(array $array, array $keys): array
    {
        return array_filter($array, fn($key) => in_array($key, $keys, true), 2);
    }

    /**
     * Filter given array excluding given keys.
     *
     * @param  array             $array
     * @param  array<int|string> $keys
     * @return array
     */
    public static function exclude(array $array, array $keys): array
    {
        return array_filter($array, fn($key) => !in_array($key, $keys, true), 2);
    }

    /**
     * Flatten given array.
     *
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
     * Swap two keys on given array.
     *
     * @param  array      &$array
     * @param  int|string  $oldKey
     * @param  int|string  $newKey
     * @param  any|null    $default
     * @return array
     * @since  4.2
     */
    public static function swap(array &$array, $oldKey, $newKey, $default = null): array
    {
        $newValue = self::pull($array, $oldKey);

        if ($newValue !== null) {
            self::set($array, $newKey, $newValue);
        } elseif (func_num_args() == 4) { // Create directive.
            self::set($array, $newKey, $default);
        }

        return $array;
    }

    /**
     * Sweep given array filtering null, '' and [] values.
     *
     * @param  array      &$array
     * @param  array|null  $ignoredKeys
     * @return array
     * @since  4.0
     */
    public static function sweep(array &$array, array $ignoredKeys = null): array
    {
        // Memoize test function.
        static $test; $test ??= fn($v) => $v !== null && $v !== '' && $v !== [];

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
     * Filte an array with default=null to ensure given keys.
     *
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
     * Find index of given value.
     *
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
     * Get first item from given array.
     *
     * @param  array    &$array
     * @param  any|null  $default
     * @param  bool      $drop
     * @return any|null
     */
    public static function first(array &$array, $default = null, bool $drop = false)
    {
        $key   = array_key_first($array);
        $value = $default;

        if ($key !== null) {
            $value = $array[$key];
            if ($drop) {
                unset($array[$key]);
            }
        }

        return $value;
    }

    /**
     * Get last item from given array.
     *
     * @param  array    &$array
     * @param  any|null  $default
     * @param  bool      $drop
     * @return any|null
     */
    public static function last(array &$array, $default = null, bool $drop = false)
    {
        $key   = array_key_last($array);
        $value = $default;

        if ($key !== null) {
            $value = $array[$key];
            if ($drop) {
                unset($array[$key]);
            }
        }

        return $value;
    }

    /**
     * Check whether given keys exist in given array.
     *
     * @param  array             $array
     * @param  array<int|string> $keys
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
     * Check whether given values exist given array.
     *
     * @param  array $array
     * @param  array $values
     * @param  bool  $strict
     * @return bool
     */
    public static function valuesExists(array $array, array $values, bool $strict = true): bool
    {
        foreach ($values as $value) {
            if (!array_value_exists($value, $array, $strict)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Search given keys returning found values.
     *
     * @param  array             $array
     * @param  array<int|string> $keys
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
     * Search values returning found keys.
     *
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
     * Filter, unlike array_filter() using [value,key,i,array] notation but fallback to [value] notation when
     * ArgumentCountError occurs.
     *
     * @param  array         $array
     * @param  callable|null $func
     * @param  bool          $keepKeys
     * @return array
     */
    public static function filter(array $array, callable $func = null, bool $keepKeys = true): array
    {
        // Set default tester.
        $func ??= fn($v) => $v !== null && $v !== '' && $v !== [];

        $ret = []; $i = 0;

        foreach ($array as $key => $value) try {
            $func($value, $key, $i++, $array) && $ret[$key] = $value;
        } catch (ArgumentCountError) {
            $func($value) && $ret[$key] = $value;
        }

        return $keepKeys ? $ret : array_values($ret);
    }

    /**
     * Map, unlike array_map() using [value,key,i,array] notation but fallback to [value] notation when
     * ArgumentCountError occurs.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $keepKeys
     * @return array
     */
    public static function map(array $array, callable $func, bool $keepKeys = true): array
    {
        $ret = []; $i = 0;

        foreach ($array as $key => $value) try {
            $ret[$key] = $func($value, $key, $i++, $array);
        } catch (ArgumentCountError) {
            $ret[$key] = $func($value);
        }

        return $keepKeys ? $ret : array_values($ret);
    }

    /**
     * Reduce, unlike array_reduce() using [value,key,i,array] notation but fallback to [value] notation when
     * ArgumentCountError occurs.
     *
     * @param  array    $array
     * @param  any      $carry
     * @param  callable $func
     * @return any
     */
    public static function reduce(array $array, $carry, callable $func)
    {
        $ret = $carry; $i = 0;

        foreach ($array as $key => $value) try {
            $ret = $func($ret, $value, $key, $i++, $array);
        } catch (ArgumentCountError) {
            $ret = $func($ret, $value);
        }

        return $ret;
    }

    /**
     * Aggregate an array with given carry variable that must be ref'ed like `fn(&$carry, $value, ..) => ..`
     * in given aggregate function.
     *
     * @param  array      $array
     * @param  callable   $func
     * @param  array|null $carry
     * @return array
     * @since  4.14
     */
    public static function aggregate(array $array, callable $func, array $carry = null): array
    {
        $carry ??= []; $i = 0;

        foreach ($array as $key => $value) {
            // @cancel: Return can always be an array..
            // // Note: when "return" not used carry must be ref'ed (eg: (&$carry, $value, ..)).
            // // $ret = $func($carry, $value, $key, $i++, $array);
            // // When "return" used.
            // // if ($ret && is_array($ret)) {
            // //     $carry = $ret;
            // // }

            // Note: carry must be ref'ed (eg: (&$carry, $value, ..)).
            $func($carry, $value, $key, $i++, $array);

            $carry = (array) $carry;
        }

        return $carry;
    }

    /**
     * Get average of values.
     *
     * @param  array $array
     * @param  bool  $zeros
     * @return float
     * @since  4.5
     */
    public static function average(array $array, bool $zeros = true): float
    {
        $array = array_filter($array, fn($v) => $zeros ? is_numeric($v) : is_numeric($v) && $v > 0);

        return fdiv(array_sum($array), count($array));
    }

    /**
     * Get an item as int.
     *
     * @param  array      &$array
     * @param  int|string  $key
     * @param  int|null    $default
     * @param  bool        $drop
     * @return int
     */
    public static function getInt(array &$array, $key, int $default = null, bool $drop = false): int
    {
        return (int) self::get($array, $key, $default, $drop);
    }

    /**
     * Get an item as float.
     *
     * @param  array      &$array
     * @param  int|string  $key
     * @param  float|null  $default
     * @param  bool        $drop
     * @return float
     */
    public static function getFloat(array &$array, $key, float $default = null, bool $drop = false): float
    {
        return (float) self::get($array, $key, $default, $drop);
    }

    /**
     * Get an item as string.
     *
     * @param  array       &$array
     * @param  int|string   $key
     * @param  string|null  $default
     * @param  bool         $drop
     * @return string
     */
    public static function getString(array &$array, $key, string $default = null, bool $drop = false): string
    {
        return (string) self::get($array, $key, $default, $drop);
    }

    /**
     * Get an item as bool.
     *
     * @param  array      &$array
     * @param  int|string  $key
     * @param  bool|null   $default
     * @param  bool        $drop
     * @return bool
     */
    public static function getBool(array &$array, $key, bool $default = null, bool $drop = false): bool
    {
        return (bool) self::get($array, $key, $default, $drop);
    }
}
