<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

/**
 * Array utility class.
 *
 * @package froq\util
 * @class   froq\util\Arrays
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final class Arrays extends \StaticClass
{
    /**
     * Check whether all keys are "int" in given array, or given array is a list when strict.
     *
     * @param  array $array
     * @param  bool  $strict
     * @return bool
     */
    public static function isListArray(array $array, bool $strict = true): bool
    {
        if ($strict) {
            return array_is_list($array);
        }

        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether given array is an associative array.
     *
     * @param  array $array
     * @return bool
     */
    public static function isAssocArray(array $array): bool
    {
        if (array_is_list($array)) {
            return false;
        }

        foreach (array_keys($array) as $key) {
            if (is_string($key) || $key < 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether all keys are "string" in given array.
     *
     * @param  array $array
     * @return bool
     */
    public static function isMapArray(array $array): bool
    {
        if (array_is_list($array)) {
            return false;
        }

        foreach (array_keys($array) as $key) {
            if (!is_string($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether all values are "unique" in given array.
     *
     * @param  array $array
     * @param  bool  $strict
     * @return bool
     */
    public static function isSetArray(array $array, bool $strict = true): bool
    {
        $search = array_shift($array);
        foreach ($array as $value) {
            if ($strict ? $search === $value : $search == $value) {
                return false;
            }
            $search = $value;
        }
        return true;
    }

    /**
     * Put an item into given array, with dot notation support for sub-array paths.
     *
     * @param  array      &$array
     * @param  int|string $key
     * @param  mixed      $value
     * @return array
     */
    public static function set(array &$array, int|string $key, mixed $value): array
    {
        // Usage:
        // Arrays::set($array, 'a.b.c', 1) => ['a' => ['b' => ['c' => 1]]]

        // Direct access.
        if (array_key_exists($key, $array) || is_int($key)) {
            $array[$key] = $value;
        } else {
            // Direct access.
            if (!str_contains($key, '.')) {
                $array[$key] = $value;
            } else {
                $keys = explode('.', $key);

                // Direct access.
                if (count($keys) <= 1) {
                    $array[$keys[0]] = $value;
                }
                // Path access (with dot notation).
                else {
                    $current = &$array;

                    foreach ($keys as $key) {
                        if (isset($current[$key])) {
                            $current[$key] = (array) $current[$key];
                        }
                        $current = &$current[$key];
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
     * @param  array $items
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
     * @param  int|string $key
     * @param  mixed|null $default
     * @param  bool       $drop
     * @return mixed|null
     */
    public static function &get(array &$array, int|string $key, mixed $default = null, bool $drop = false): mixed
    {
        // Usage:
        // $array = ['a' => ['b' => ['c' => ['d' => 1, 'd.e' => '...']]]]
        // Arrays::get($array, 'a.b.c.d') => 1
        // Arrays::get($array, 'a.b.c.d.e') => '...'

        $value = null;

        // Direct access.
        if (array_key_exists($key, $array) || is_int($key)) {
            $value = &$array[$key];
            $drop && array_unset($array, $key);
        } else {
            // Direct access.
            if (!str_contains($key, '.')) {
                $value = &$array[$key];
                $drop && array_unset($array, $key);
            }
            // Path access (with dot notation).
            else {
                $keys = explode('.', $key);
                $key  = array_shift($keys);

                if (!$keys) {
                    $value = &$array[$key];
                    $drop && array_unset($array, $key);
                }
                // Dig more..
                elseif (isset($array[$key]) && is_array($array[$key])) {
                    $value = &self::get($array[$key], implode('.', $keys), $default, $drop);
                }
            }
        }

        // Ref'ed default (@important).
        if ($value === null) {
            $value = &$default;
        }

        return $value;
    }

    /**
     * Bridge method to get() for multiple items. Useful in some times eg. list(..) = Arrays::getAll(..).
     *
     * @param  array             &$array
     * @param  array<int|string> $keys
     * @param  array|null        $defaults
     * @param  bool              $drop
     * @return array
     */
    public static function &getAll(array &$array, array $keys, array $defaults = null, bool $drop = false): array
    {
        $values = [];

        foreach ($keys as $i => $key) {
            $default    = $defaults[$i] ?? null;
            $values[$i] = &self::get($array, $key, $default, $drop);
        }

        return $values;
    }

    /**
     * Pull an item from given array by a key.
     *
     * @param  array      &$array
     * @param  int|string $key
     * @param  mixed|null $default
     * @return mixed|null
     */
    public static function pull(array &$array, int|string $key, mixed $default = null): mixed
    {
        return self::get($array, $key, $default, true);
    }

    /**
     * Bridge method to get() for multiple items. Useful in some times eg. list(..) = Arrays::pullAll(..).
     *
     * @param  array             &$array
     * @param  array<int|string> $keys
     * @param  array|null        $defaults
     * @return array
     */
    public static function pullAll(array &$array, array $keys, array $defaults = null): array
    {
        return self::getAll($array, $keys, $defaults, true);
    }

    /**
     * Remove an item from given array by a key.
     *
     * @param  array      &$array
     * @param  int|string $key
     * @return array
     * @since  4.0
     */
    public static function remove(array &$array, int|string $key): array
    {
        self::pull($array, $key);

        return $array;
    }

    /**
     * Bridge method to remove() for multiple items.
     *
     * @param  array             &$array
     * @param  array<int|string> $keys
     * @return array
     * @since  4.0
     */
    public static function removeAll(array &$array, array $keys): array
    {
        self::pullAll($array, $keys);

        return $array;
    }

    /**
     * Get one/many items from given array randomly.
     *
     * @param  array  &$array
     * @param  int    $limit
     * @param  bool   $pack
     * @param  bool   $drop
     * @return mixed|null
     * @since  4.12
     */
    public static function getRandom(array &$array, int $limit = 1, bool $pack = false, bool $drop = false): mixed
    {
        return self::random($array, $limit, $pack, $drop);
    }

    /**
     * Pull one/many items from given array randomly.
     *
     * @param  array  &$array
     * @param  int    $limit
     * @param  bool   $pack
     * @return mixed|null
     * @since  4.12
     */
    public static function pullRandom(array &$array, int $limit = 1, bool $pack = false): mixed
    {
        return self::random($array, $limit, $pack, true);
    }

    /**
     * Pull one/many items from given array randomly.
     *
     * @param  array  &$array
     * @param  int    $limit
     * @return array
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
     * @param  array      $keys
     * @param  array      $values
     * @param  mixed|null $default
     * @return array
     * @since  4.11
     */
    public static function compose(array $keys, array $values, mixed $default = null): array
    {
        $ret = [];

        foreach ($keys as $i => $key) {
            $ret[$key] = $values[$i] ?? $default;
        }

        return $ret;
    }

    /**
     * Concat all given items.
     *
     * @param  array    $array
     * @param  mixed ...$items
     * @return array
     * @since  5.30
     */
    public static function concat(array $array, mixed ...$items): array
    {
        // Note: Array items won't be merged.
        return [...$array, ...$items];
    }

    /**
     * Union all given array(s) returning a unique'd array with strict comparison.
     *
     * @param  array    $array1
     * @param  array    $array2
     * @param  array ...$arrays
     * @return array
     * @since  5.0
     */
    public static function union(array $array1, array $array2, array ...$arrays): array
    {
        $ret = [];

        foreach (array_merge($array1, $array2, ...$arrays) as $key => $value) {
            in_array($value, $ret, true) || $ret[$key] = $value;
        }

        return $ret;
    }

    /**
     * Get "really" unique items with strict comparison as default since array_unique()
     * comparison non-strict (eg: 1 == '1' is true).
     *
     * @param  array     $array
     * @param  bool      $strict
     * @param  bool|null $list
     * @return 5.22, 5.25
     */
    public static function dedupe(array $array, bool $strict = true, bool $list = null): array
    {
        $ret = [];

        $list ??= array_is_list($array);

        foreach ($array as $key => $value) {
            in_array($value, $ret, $strict) || $ret[$key] = $value;
        }

        $list && $ret = array_list($ret); // Re-index.

        return $ret;
    }

    /**
     * Refine given array filtering given or null, "" and [] values as default.
     *
     * @param  array      $array
     * @param  array|null $values
     * @param  bool|null  $list
     * @return array
     * @since  6.0
     */
    public static function refine(array $array, array $values = null, bool $list = null): array
    {
        $func = self::makeFilterFunction(null, $values);

        $list ??= array_is_list($array);

        $ret = array_filter($array, $func);

        $list && $ret = array_list($ret); // Re-index.

        return $ret;
    }

    /**
     * Create a groupped result from given array (@see https://wiki.php.net/rfc/array_column_results_grouping).
     *
     * @param  array      $array
     * @param  int|string $field
     * @return array
     * @since  5.31, 6.0
     */
    public static function group(array $array, int|string $field): array
    {
        $ret = [];

        foreach ($array as $row) {
            $row = (array) $row;
            if (array_key_exists($field, $row)) {
                $key = $row[$field];
                $ret[$key][] = $row;
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
     * Find first/last item that satisfies given test function.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $reverse
     * @return mixed|null
     * @since  4.10
     */
    public static function find(array $array, callable $func, bool $reverse = false): mixed
    {
        $reverse && $array = array_reverse($array);

        foreach ($array as $key => $value) {
            if ($func($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Find all items that satisfy given test function.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $reverse
     * @return array|null
     * @since  4.10
     */
    public static function findAll(array $array, callable $func, bool $reverse = false): array|null
    {
        $reverse && $array = array_reverse($array);

        foreach ($array as $key => $value) {
            if ($func($value, $key)) {
                $ret[$key] = $value;
            }
        }

        return $ret ?? null;
    }

    /**
     * Find first/last item key that satisfies given test function.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $reverse
     * @return int|string|null
     * @since  5.31
     */
    public static function findKey(array $array, callable $func, bool $reverse = false): int|string|null
    {
        $reverse && $array = array_reverse($array);

        foreach ($array as $key => $value) {
            if ($func($value, $key)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Find all keys that satisfy given test function.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $reverse
     * @return array|null
     * @since  5.31
     */
    public static function findKeys(array $array, callable $func, bool $reverse = false): array|null
    {
        $reverse && $array = array_reverse($array);

        foreach ($array as $key => $value) {
            if ($func($value, $key)) {
                $ret[] = $key;
            }
        }

        return $ret ?? null;
    }

    /**
     * Swap two keys on given array.
     *
     * @param  array      &$array
     * @param  int|string $oldKey
     * @param  int|string $newKey
     * @return array
     * @since  4.2
     */
    public static function swap(array &$array, int|string $oldKey, int|string $newKey): array
    {
        if (array_key_exists($oldKey, $array)) {
            $array[$newKey] = $array[$oldKey];
            unset($array[$oldKey]);
        }

        return $array;
    }

    /**
     * Swap two values on given array.
     *
     * @param  array &$array
     * @param  mixed $oldValue
     * @param  mixed $newValue
     * @return array
     */
    public static function swapValue(array &$array, mixed $oldValue, mixed $newValue): array
    {
        if (array_value_exists($oldValue, $array, key: $key)) {
            $array[$key] = $newValue;
        }

        return $array;
    }

    /**
     * Randomize given array, optionally returning as [key,value] pairs.
     *
     * @param  array &$array
     * @param  int   $limit
     * @param  bool  $pack
     * @param  bool  $drop
     * @return mixed|null
     * @throws ArgumentError
     */
    public static function random(array &$array, int $limit = 1, bool $pack = false, bool $drop = false): mixed
    {
        $count = count($array);
        if (!$count) {
            return null;
        }

        // Prevent trivial corruption from limit errors.
        if ($limit < 1) {
            throw new \ArgumentError('Minimum limit must be 1, %s given', $limit);
        } elseif ($limit > $count) {
            throw new \ArgumentError(
                'Maximum limit must not be greater than %s, given limit %s is '.
                'exceeding count of given array(%s)', [$count, $limit, $count]
            );
        }

        $ret = [];

        // Pick some keys by given limit.
        $keys = (new \Random\Randomizer)->pickArrayKeys($array, $limit);

        foreach ($keys as $key) {
            $pack ? $ret[$key] = $array[$key] : $ret[] = $array[$key];

            // Drop used item.
            $drop && array_unset($array, $key);
        }

        if (count($ret) === 1) {
            $ret = $pack ? [key($ret), current($ret)] : current($ret);
        }

        return $ret;
    }

    /**
     * Shuffle given array, keeping keys as default.
     *
     * @param  array     $array
     * @param  bool|null $assoc
     * @return array
     */
    public static function shuffle(array $array, bool $assoc = null): array
    {
        $assoc ??= self::isAssocArray($array);

        if (!$assoc) {
            $array = (new \Random\Randomizer)->shuffleArray($array);
        } else {
            $keys = (new \Random\Randomizer)->shuffleArray(array_keys($array));

            $temp = [];
            foreach ($keys as $key) {
                $temp[$key] = $array[$key];
            }
            $array = $temp;

            // Nope.. (cos killing speed and also randomness).
            // uasort($array, function () {
            //     return rand(-1, 1);
            // });
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
        return array_filter($array, fn(int|string $key): bool => (
            in_array($key, $keys, true)
        ), ARRAY_FILTER_USE_KEY);
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
        return array_filter($array, fn(int|string $key): bool => (
            !in_array($key, $keys, true)
        ), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Split an array preserving string keys, not like `array_chunk()`.
     *
     * @param  array  $array
     * @param  int    $length
     * @param  bool   $keepKeys
     * @return array
     * @since  6.0
     */
    public static function split(array $array, int $length, bool $keepKeys = false): array
    {
        $ret = [];

        if (!$array) {
            return $ret;
        }

        $chunks = array_chunk($array, $length, true);
        if ($keepKeys) {
            return $chunks;
        }

        foreach ($chunks as $i => $chunk) {
            $j = 0;
            foreach ($chunk as $key => $value) {
                is_string($key)
                    ? $ret[$i][$key] = $value
                    : $ret[$i][$j++] = $value;
            }
        }

        return $ret;
    }

    /**
     * Flat given array.
     *
     * @param  array $array
     * @param  bool  $keepKeys
     * @param  bool  $fixKeys
     * @param  bool  $multi
     * @return array
     * @since  4.0
     */
    public static function flat(array $array, bool $keepKeys = false, bool $fixKeys = false, bool $multi = true): array
    {
        $ret = [];

        if ($multi) {
            $i = 0;
            // Seems short functions (=>) not work here [ref (&) issue].
            array_walk_recursive($array, function ($value, $key) use (&$ret, &$i, $keepKeys, $fixKeys) {
                !$keepKeys ? $ret[] = $value : (
                    !$fixKeys ? $ret[$key] = $value // Use original keys.
                              : $ret[is_string($key) ? $key : $i++] = $value // Re-index integer keys.
                );
            });
        } else {
            $ret = array_merge(...array_map(
                fn(mixed $value): array => (array) $value,
                $array
            ));
        }

        return $ret;
    }

    /**
     * Compact given keys with given vars.
     *
     * @param  int|string|array    $keys
     * @param  mixed            ...$vars
     * @return array
     * @since  6.0
     */
    public static function compact(int|string|array $keys, mixed ...$vars): array
    {
        $ret = [];

        // Compact comma-separated keys.
        if (is_string($keys) && str_contains($keys, ',')) {
            $keys = split(' *, *', $keys);
        }

        foreach ((array) $keys as $i => $key) {
            $ret[$key] = $vars[$i] ?? null;
        }

        return $ret;
    }

    /**
     * Extract given keys to given vars with refs.
     *
     * @param  int|string|array     $keys
     * @param  mixed            &...$vars
     * @return int
     * @since  6.0
     */
    public static function extract(array $array, int|string|array $keys, mixed &...$vars): int
    {
        $ret = 0;

        // Extract comma-separated keys.
        if (is_string($keys) && str_contains($keys, ',')) {
            $keys = split(' *, *', $keys);
        }
        // Extract all keys.
        elseif ($keys === '*') {
            $keys = array_keys($array);
        }

        foreach ((array) $keys as $i => $key) {
            if (isset($array[$key])) {
                $vars[$i] = $array[$key];
                $ret++;
            }
        }

        return $ret;
    }

    /**
     * Export given array keys to given vars as list or named argument.
     *
     * @param  array     $array
     * @param  mixed &...$vars
     * @return int
     * @since  6.0
     */
    public static function export(array $array, mixed &...$vars): int
    {
        $ret = 0;

        if (array_is_list($vars)) {
            // List stuff.
            $list = array_list($array);
            foreach ($vars as $i => $_) {
                if (isset($list[$i])) {
                    $vars[$i] = $list[$i];
                    $ret++;
                }
            }
        } else {
            // Named stuff.
            $keys = array_keys($vars);
            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $vars[$key] = $array[$key];
                    $ret++;
                }
            }
        }

        return $ret;
    }

    /**
     * Check whether given keys exist in given array.
     *
     * @param  array $array
     * @param  array $keys
     * @return bool
     */
    public static function keysExist(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }

        return $array && $keys;
    }

    /**
     * Check whether given values exist given array.
     *
     * @param  array $array
     * @param  array $values
     * @param  bool  $strict
     * @return bool
     */
    public static function valuesExist(array $array, array $values, bool $strict = true): bool
    {
        foreach ($values as $value) {
            if (!array_value_exists($value, $array, $strict)) {
                return false;
            }
        }

        return $array && $values;
    }

    /**
     * Search given value's key.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  bool   $strict
     * @param  bool   $last
     * @return int|string|null
     * @since  5.3
     */
    public static function searchKey(array $array, mixed $value, bool $strict = true, bool $last = false): int|string|null
    {
        $ret = $last ? array_search($value, array_reverse($array, true), $strict)
                     : array_search($value, $array, $strict);

        return ($ret !== false) ? $ret : null;
    }

    /**
     * Search given value returning all found keys.
     *
     * @param  array $array
     * @param  array $value
     * @param  bool  $strict
     * @param  bool  $reverse
     * @return array<int|string>|null
     * @since  4.0
     */
    public static function searchKeys(array $array, mixed $value, bool $strict = true, bool $reverse = false): array|null
    {
        $ret = $reverse ? array_keys(array_reverse($array, true), $value, $strict)
                        : array_keys($array, $value, $strict);

        return $ret ?: null;
    }

    /**
     * Ensure an array keys.
     *
     * @param  array      $array
     * @param  array      $keys
     * @param  mixed|null $value
     * @param  bool       $isset
     * @return array
     * @since  4.0, 6.0
     */
    public static function padKeys(array $array, array $keys, mixed $value = null, bool $isset = false): array
    {
        foreach ($keys as $key) {
            if (!($isset ? isset($array[$key]) : array_key_exists($key, $array))) {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Convert key cases to lower.
     *
     * @param  array $array
     * @param  bool  $recursive
     * @return array
     * @since  6.0
     */
    public static function lowerKeys(array $array, bool $recursive = false): array
    {
        return self::mapKeys($array, fn(int|string $key): int|string => (
            is_string($key) ? lower($key) : $key
        ), $recursive);
    }

    /**
     * Convert key cases to upper.
     *
     * @param  array $array
     * @param  bool  $recursive
     * @return array
     * @since  6.0
     */
    public static function upperKeys(array $array, bool $recursive = false): array
    {
        return self::mapKeys($array, fn(int|string $key): int|string => (
            is_string($key) ? upper($key) : $key
        ), $recursive);
    }

    /**
     * Convert key cases to given case.
     *
     * @param  array       $array
     * @param  string|int  $case
     * @param  string|null $exploder
     * @param  string|null $imploder
     * @param  bool        $recursive
     * @return array
     * @since  6.0
     */
    public static function convertKeys(array $array, string|int $case, string $exploder = null, string $imploder = null, bool $recursive = false): array
    {
        return self::mapKeys($array, fn(int|string $key): int|string => (
            is_string($key) ? convert_case($key, $case, $exploder, $imploder) : $key
        ), $recursive);
    }

    /**
     * Ensure given keys with/without given default.
     *
     * @param  array      $array
     * @param  array      $keys
     * @param  mixed|null $default
     * @return array
     * @since  4.0
     */
    public static function default(array $array, array $keys, mixed $default = null): array
    {
        return array_replace(array_fill_keys($keys, $default), $array);
    }

    /**
     * Make an options array with/without defaults.
     *
     * @param  array|null $options
     * @param  array|null $defaults
     * @param  bool       $recursive
     * @param  bool       $map
     * @return array
     * @since  5.44
     */
    public static function options(array|null $options, array|null $defaults, bool $recursive = true, bool $map = true): array
    {
        $ret = $recursive ? array_replace_recursive((array) $defaults, (array) $options)
                          : array_replace((array) $defaults, (array) $options);

        // When options are wanted as map (string keyed only).
        $map && $ret = array_filter_keys($ret, 'is_string', $recursive);

        return $ret;
    }

    /**
     * Get first value from given array.
     *
     * @param  array $array
     * @return mixed|null
     */
    public static function first(array $array): mixed
    {
        return $array[array_key_first($array)] ?? null;
    }

    /**
     * Get last value from given array.
     *
     * @param  array $array
     * @return mixed|null
     */
    public static function last(array $array): mixed
    {
        return $array[array_key_last($array)] ?? null;
    }

    /**
     * Apply a regular/callback sort on given array.
     *
     * @param  array             $array
     * @param  callable|int|null $func
     * @param  int               $flags
     * @param  bool|null         $assoc
     * @return array
     * @since  5.3
     */
    public static function sort(array $array, callable|int $func = null, int $flags = 0, bool $assoc = null): array
    {
        $func = self::makeSortFunction($func);
        $assoc ??= self::isAssocArray($array);

        if ($assoc) {
            $func ? uasort($array, $func) : asort($array, $flags);
        } else {
            $func ? usort($array, $func) : sort($array, $flags);
        }

        return $array;
    }

    /**
     * Apply a regular/callback key sort on given array.
     *
     * @param  array             $array
     * @param  callable|int|null $func
     * @param  int               $flags
     * @return array
     * @since  5.3
     */
    public static function sortKey(array $array, callable|int $func = null, int $flags = 0): array
    {
        $func = self::makeSortFunction($func);

        $func ? uksort($array, $func) : ksort($array, $flags);

        return $array;
    }

    /**
     * Apply a locale sort on given array.
     *
     * @param  array       $array
     * @param  string|null $locale
     * @param  bool|null   $assoc
     * @return array
     * @since  5.3
     */
    public static function sortLocale(array $array, string $locale = null, bool $assoc = null): array
    {
        $assoc ??= self::isAssocArray($array);

        // Use current locale.
        if (!$locale) {
            $assoc ? uasort($array, 'strcoll') : usort($array, 'strcoll');
        } else {
            // Get & cache.
            static $currentLocale;
            $currentLocale ??= getlocale(LC_COLLATE);

            // Should change?
            if ($locale !== $currentLocale) {
                setlocale(LC_COLLATE, $locale);
            }

            $assoc ? uasort($array, 'strcoll') : usort($array, 'strcoll');

            // Restore (if needed).
            if ($locale !== $currentLocale && $currentLocale !== null) {
                setlocale(LC_COLLATE, $currentLocale);
            }
        }

        return $array;
    }

    /**
     * Apply a natural sort on given array.
     *
     * @param  array $array
     * @param  bool  $icase
     * @return array
     * @since  5.3
     */
    public static function sortNatural(array $array, bool $icase = false): array
    {
        // To act like other sort functions.
        $list = array_is_list($array);

        $icase ? natcasesort($array) : natsort($array);

        $list && $array = array_list($array); // Re-index.

        return $array;
    }

    /**
     * Each wrapper for scoped function calls on given array or just for syntactic sugar.
     *
     * @param  array    $array
     * @param  callable $func
     * @return void
     */
    public static function each(array $array, callable $func): void
    {
        foreach ($array as $key => $value) {
            $func($value, $key);
        }
    }

    /**
     * Filter.
     *
     * @param  array         $array
     * @param  callable|null $func
     * @param  bool          $recursive
     * @param  bool          $useKeys
     * @param  bool          $keepKeys
     * @return array
     */
    public static function filter(array $array, callable $func = null, bool $recursive = false, bool $useKeys = false, bool $keepKeys = true): array
    {
        $func = self::makeFilterFunction($func);

        $ret = [];

        if ($recursive) {
            if ($useKeys) {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        $ret[$key] = self::filter($value, $func, true, true, $keepKeys);
                        continue;
                    }

                    $func($value, $key) && $ret[$key] = $value;
                }
            } else {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        $ret[$key] = self::filter($value, $func, true, false, $keepKeys);
                        continue;
                    }

                    $func($value) && $ret[$key] = $value;
                }
            }
        } else {
            if ($useKeys) {
                foreach ($array as $key => $value) {
                    $func($value, $key) && $ret[$key] = $value;
                }
            } else {
                foreach ($array as $key => $value) {
                    $func($value) && $ret[$key] = $value;
                }
            }
        }

        $keepKeys || $ret = array_values($ret);

        return $ret;
    }

    /**
     * Filter keys.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $recursive
     * @return array
     */
    public static function filterKeys(array $array, callable $func, bool $recursive = false): array
    {
         $ret = [];

        foreach ($array as $key => $value) {
            if ($recursive && is_array($value)) {
                $ret[$key] = self::filterKeys($value, $func, true);
                continue;
            }

            $func($key) && $ret[$key] = $value;
        }

        return $ret;
    }

    /**
     * Map.
     *
     * @param  array                 $array
     * @param  callable|string|array $func
     * @param  bool                  $recursive
     * @param  bool                  $useKeys
     * @param  bool                  $keepKeys
     * @return array
     */
    public static function map(array $array, callable|string|array $func, bool $recursive = false, bool $useKeys = false, bool $keepKeys = true): array
    {
        $func = self::makeMapFunction($func);

        $ret = [];

        if ($recursive) {
            if ($useKeys) {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        $ret[$key] = self::map($value, $func, true, true, $keepKeys);
                        continue;
                    }

                    $ret[$key] = $func($value, $key);
                }
            } else {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        $ret[$key] = self::map($value, $func, true, false, $keepKeys);
                        continue;
                    }

                    $ret[$key] = $func($value);
                }
            }
        } else {
            if ($useKeys) {
                foreach ($array as $key => $value) {
                    $ret[$key] = $func($value, $key);
                }
            } else {
                foreach ($array as $key => $value) {
                    $ret[$key] = $func($value);
                }
            }
        }

        $keepKeys || $ret = array_values($ret);

        return $ret;
    }

    /**
     * Map keys.
     *
     * @param  array                 $array
     * @param  callable|string|array $func
     * @param  bool                  $recursive
     * @return array
     */
    public static function mapKeys(array $array, callable|string|array $func, bool $recursive = false): array
    {
        $func = self::makeMapFunction($func);

        $ret = [];

        foreach ($array as $key => $value) {
            $key = $func($key);

            if ($recursive && is_array($value)) {
                $ret[$key] = self::mapKeys($value, $func, true);
                continue;
            }

            $ret[$key] = $value;
        }

        return $ret;
    }

    /**
     * Reduce.
     *
     * @param  array         $array
     * @param  mixed         $carry
     * @param  callable|null $func
     * @param  bool          $right
     * @return mixed
     */
    public static function reduce(array $array, mixed $carry, callable $func = null, bool $right = false): mixed
    {
        // When carry is a function.
        if (is_callable($carry)) {
            [$func, $carry] = [$carry, $func];
        }

        // Reduce right option.
        if ($right) {
            $array = array_reverse($array, true);
        }

        $ret = $carry;

        foreach ($array as $key => $value) {
            $ret = $func($ret, $value, $key);
        }

        return $ret;
    }

    /**
     * Reduce keys.
     *
     * @param  array         $array
     * @param  mixed         $carry
     * @param  callable|null $func
     * @param  bool          $right
     * @return mixed
     */
    public static function reduceKeys(array $array, mixed $carry, callable $func = null, bool $right = false): mixed
    {
        return self::reduce(array_keys($array), $carry, $func, $right);
    }

    /**
     * Reduce right.
     *
     * @param  array         $array
     * @param  mixed         $carry
     * @param  callable|null $func
     * @return mixed
     */
    public static function reduceRight(array $array, mixed $carry, callable $func = null): mixed
    {
        return self::reduce($array, $carry, $func, true);
    }

    /**
     * Apply.
     *
     * @param  array    $array
     * @param  callable $func
     * @param  bool     $recursive
     * @param  bool     $list
     * @return array
     */
    public static function apply(array $array, callable $func, bool $recursive = false, bool $list = false): array
    {
        foreach ($array as $key => $value) {
            if ($recursive && is_array($value)) {
                $array[$key] = self::apply($value, $func, true);
            } else {
                $array[$key] = $func($value, $key);
            }
        }

        $list && $array = array_list($array);

        return $array;
    }

    /**
     * Aggregate.
     *
     * Note: Carry must be ref'ed like `fn(&$carry, $value, ..) => ..` in calls.
     *
     * @param  array      $array
     * @param  callable   $func
     * @param  array|null $carry
     * @return array
     */
    public static function aggregate(array $array, callable $func, array $carry = null): array
    {
        $carry ??= [];

        foreach ($array as $key => $value) {
            // @cancel: Return can always be an array.
            // Note: when "return" not used carry must be ref'ed (eg: (&$carry, $value, ..)).
            // $ret = $func($carry, $value, $key, $array);
            // When "return" used.
            // if ($ret && is_array($ret)) {
            //     $carry = $ret;
            // }

            // Note: carry must be ref'ed (eg: (&$carry, $value, ..)).
            $func($carry, $value, $key);

            $carry = (array) $carry;
        }

        return $carry;
    }

    /**
     * Average.
     *
     * @param  array $array
     * @param  bool  $zeros
     * @return float
     */
    public static function average(array $array, bool $zeros = true): float
    {
        $array = array_filter($array, fn($v): bool => (
            $zeros ? is_numeric($v) : is_numeric($v) && ($v > 0)
        ));

        return $array ? fdiv(array_sum($array), count($array)) : 0.0;
    }

    /**
     * Check whether all given keys were set in given array.
     *
     * @param  array         $array
     * @param  int|string ...$keys
     * @return bool
     * @throws ArgumentError
     * @since  4.0, 6.0
     */
    public static function isset(array $array, int|string ...$keys): bool
    {
        $keys || throw new \ArgumentError('No key/keys given');

        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Drop all given keys from given array.
     *
     * @param  array         &$array
     * @param  int|string ...$keys
     * @return array
     * @throws ArgumentError
     * @since  4.0, 6.0
     */
    public static function unset(array &$array, int|string ...$keys): array
    {
        $keys || throw new \ArgumentError('No key/keys given');

        $list = array_is_list($array);

        foreach ($keys as $key) {
            unset($array[$key]);
        }

        $list && $array = array_list($array); // Re-index.

        return $array;
    }

    /**
     * Check whether any of given values exists in given array.
     *
     * @param  array    $array
     * @param  mixed ...$values
     * @return bool
     * @throws ArgumentError
     * @since  5.0, 6.0
     */
    public static function contains(array $array, mixed ...$values): bool
    {
        $values || throw new \ArgumentError('No value/values given');

        foreach ($values as $value) {
            if (array_value_exists($value, $array)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether any of given keys exists in given array.
     *
     * @param  array         $array
     * @param  int|string ...$keys
     * @return bool
     * @throws ArgumentError
     * @since  5.3, 6.0
     */
    public static function containsKey(array $array, int|string ...$keys): bool
    {
        $keys || throw new \ArgumentError('No key/keys given');

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Delete given values from given array if exist.
     *
     * @param  array    &$array
     * @param  mixed ...$values
     * @return array
     * @throws ArgumentError
     * @since  5.0, 6.0
     */
    public static function delete(array &$array, mixed ...$values): array
    {
        $values || throw new \ArgumentError('No value/values given');

        $list = array_is_list($array);

        foreach ($values as $value) {
            $keys = array_keys($array, $value, true);
            foreach ($keys as $key) {
                unset($array[$key]);
            }
        }

        $list && $array = array_list($array); // Re-index.

        return $array;
    }

    /**
     * Delete given keys from given array if exist.
     *
     * @param  array         &$array
     * @param  int|string ...$keys
     * @return array
     * @throws ArgumentError
     * @since  5.31, 6.0
     */
    public static function deleteKey(array &$array, int|string ...$keys): array
    {
        $keys || throw new \ArgumentError('No key/keys given');

        $list = array_is_list($array);

        foreach ($keys as $key) {
            unset($array[$key]);
        }

        $list && $array = array_list($array); // Re-index.

        return $array;
    }

    /**
     * Append given values to an array, returning given array back.
     *
     * @param  array    &$array
     * @param  mixed ...$values
     * @return array
     * @throws ArgumentError
     * @since  4.0, 6.0
     */
    public static function append(array &$array, mixed ...$values): array
    {
        $values || throw new \ArgumentError('No value/values given');

        array_push($array, ...$values);

        return $array;
    }

    /**
     * Prepend given values to an array, returning given array back.
     *
     * @param  array    &$array
     * @param  mixed ...$values
     * @return array
     * @throws ArgumentError
     * @since  4.0, 6.0
     */
    public static function prepend(array &$array, mixed ...$values): array
    {
        $values || throw new \ArgumentError('No value/values given');

        array_unshift($array, ...$values);

        return $array;
    }

    /**
     * Convert an array to list with/without desired length.
     *
     * @param  array    $array
     * @param  int|null $length
     * @return array
     * @throws ArgumentError
     */
    public static function list(array $array, int $length = null): array
    {
        if ($length === null) {
            return array_values($array);
        }
        if ($length <= 0) {
            throw new \ArgumentError('Argument $length must be greater than 0');
        }

        return ($length > count($array))
             ? array_pad(array_values($array), $length, null) // Pad missing fields.
             : array_slice(array_values($array), 0, $length); // Cut exceeding fields.
    }

    /**
     * Get given array items as entries.
     *
     * @param  array $array
     * @return array
     * @since  5.19, 6.0
     */
    public static function entries(array $array): array
    {
        $ret = [];

        foreach ($array as $key => $value) {
            $ret[] = [$key, $value];
        }

        return $ret;
    }

    /**
     * Like `array_push()` but taking key/value pairs.
     *
     * @param  array &$array
     * @param  array $entry
     * @return array
     * @throws ArgumentError
     * @since  5.22, 6.0
     */
    public static function pushEntry(array &$array, array $entry): array
    {
        if (count($entry) !== 2) {
            throw new \ArgumentError('Entry must contain key/value pairs');
        }

        [$key, $value] = $entry;

        // Drop old one (in case).
        unset($array[$key]);

        $array[$key] = $value;

        return $array;
    }

    /**
     * Like `array_pop()` but returning key/value pairs.
     *
     * @param  array      &$array
     * @param  array|null $default
     * @return array|null
     * @since  5.22, 6.0
     */
    public static function popEntry(array &$array, array $default = null): array|null
    {
        $key = array_key_last($array);

        if ($key !== null) {
            return [$key, array_pop($array)];
        }

        return $default;
    }

    /**
     * Like `array_unshift()` but taking key/value pairs.
     *
     * @param  array &$array
     * @param  array $entry
     * @return array
     * @throws ArgumentError
     * @since  5.22, 6.0
     */
    public static function unshiftEntry(array &$array, array $entry): array
    {
        if (count($entry) !== 2) {
            throw new \ArgumentError('Entry must contain key/value pairs');
        }

        [$key, $value] = $entry;

        $array = [$key => $value] + $array;

        return $array;
    }

    /**
     * Like `array_shift()` but returning key/value pairs.
     *
     * @param  array      &$array
     * @param  array|null $default
     * @return array|null
     * @since  5.22, 6.0
     */
    public static function shiftEntry(array &$array, array $default = null): array|null
    {
        $key = array_key_first($array);

        if ($key !== null) {
            if (array_is_list($array)) {
                return [$key, array_shift($array)];
            }

            // Keep assoc keys (do not modify).
            return [$key, self::get($array, $key, drop: true)];
        }

        return $default;
    }

    /**
     * Like `array_push()` but taking key/value arguments.
     *
     * @param  array  &$array
     * @param  string $key
     * @param  mixed  $value
     * @return array
     * @since  6.0
     */
    public static function pushKey(array &$array, int|string $key, mixed $value): array
    {
        return self::pushEntry($array, [$key, $value]);
    }

    /**
     * Like `array_pop()` but taking key argument.
     *
     * @param  array      &$array
     * @param  string     $key
     * @param  mixed|null $default
     * @return array
     * @since  6.0
     */
    public static function popKey(array &$array, int|string $key, mixed $default = null): mixed
    {
        return self::get($array, $key, $default, drop: true);
    }

    /**
     * Like `array_unshift()` but more semantic.
     *
     * @param  array    &$array
     * @param  mixed    $value
     * @param  mixed ...$values
     * @return array
     * @since  6.0
     */
    public static function pushLeft(array &$array, mixed $value, mixed ...$values): array
    {
        array_unshift($array, $value, ...$values);

        return $array;
    }

    /**
     * Like `array_shift()` but more semantic.
     *
     * @param  array      &$array
     * @param  mixed|null $default
     * @return mixed
     * @since  6.0
     */
    public static function popLeft(array &$array, mixed $default = null): mixed
    {
        return array_shift($array) ?? $default;
    }

    /**
     * Choose an item from an array by given key(s).
     *
     * @param  array                        $array
     * @param  int|string|array<int|string> $key
     * @param  mixed|null                   $default
     * @return mixed|null
     * @since  6.0
     */
    public static function choose(array $array, int|string|array $key, mixed $default = null): mixed
    {
        if (!$array) {
            return $default;
        }

        // Choose comma-separated keys.
        if (is_string($key) && str_contains($key, ',')) {
            $key = split(' *, *', $key);
        }

        foreach ((array) $key as $key) {
            if (isset($array[$key])) {
                $value = $array[$key];
                break;
            }
        }

        return $value ?? $default;
    }

    /**
     * Select item(s) from an array by given key(s), optionally combining keys/values.
     *
     * @param  array                        $array
     * @param  int|string|array<int|string> $key
     * @param  mixed|null                   $default
     * @param  bool                         $combine
     * @return mixed|null
     * @since  5.0, 6.0
     */
    public static function select(array $array, int|string|array $key, mixed $default = null, bool $combine = false): mixed
    {
        if (!$array) {
            return $default;
        }

        // Select comma-separated keys.
        if (is_string($key) && str_contains($key, ',')) {
            $key = split(' *, *', $key);
        }

        $keys   = (array) $key;
        $values = [];

        if ($single = !is_array($key)) {
            $values[] = $array[$key] ?? $default;
        } else {
            $defaults = (array) $default;
            foreach ($keys as $i => $key) {
                $default    = $defaults[$i] ?? null;
                $values[$i] = $array[$key]  ?? $default;
            }
        }

        $combine && $values = array_combine($keys, $values);

        return ($single && !$combine) ? $values[0] : $values;
    }

    /**
     * Make filter function.
     */
    private static function makeFilterFunction(callable|null $func, array $values = null): callable
    {
        if (is_null($func)) {
            // Default filter values.
            $values ??= [null, "", []];

            $func = fn($value): bool => !in_array($value, $values, true);
        }

        return $func;
    }

    /**
     * Make map function.
     */
    private static function makeMapFunction(callable|string|array $func): callable
    {
        if (!is_callable($func)) {
            $funcs = $func;

            if (is_string($func)) {
                // When a built-in type given.
                static $types = '~^(?:int|float|string|bool|array|object|null)$~';
                $type = $func;

                // Provide a mapper using settype().
                if (preg_test($types, $type)) {
                    return function ($value) use ($type) {
                        settype($value, $type);
                        return $value;
                    };
                }

                // When multiple functions given.
                $funcs = explode('|', $func);
            }

            if (is_array($funcs)) {
                return function ($value, $key = null) use ($funcs) {
                    foreach ($funcs as $func) {
                        $value = ($key !== null) // If using keys.
                            ? $func($value, $key) : $func($value);
                    }
                    return $value;
                };
            }
        }

        return $func;
    }

    /**
     * Make a sort function.
     *
     * @throws ValueError
     */
    private static function makeSortFunction(callable|int|null $func): callable|null
    {
        // As as shortcut for reversed (-1) sorts actually.
        if (is_int($func)) {
            $func = match ($func) {
                -1      => fn($a, $b): int => $a > $b ? -1 : 1,
                 1      => fn($a, $b): int => $a < $b ? -1 : 1,
                default => throw new \ValueError('Only 1, -1 accepted as int')
            };
        }

        return $func;
    }
}
