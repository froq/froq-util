<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\Arrays;

/**
 * Is set array.
 * @param  any $in
 * @return bool
 */
function is_set_array($in): bool
{
    return is_array($in) && Arrays::isSet($in);
}

/**
 * Is map array.
 * @param  any $in
 * @return bool
 */
function is_map_array($in): bool
{
    return is_array($in) && Arrays::isMap($in);
}

/**
 * Array set.
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any                           $value
 * @return array
 * @since  3.0
 */
function array_set(array &$array, $key, $value): array
{
    return is_array($key) ? Arrays::setAll($array, $key)
                          : Arrays::set($array, $key, $value);
}

/**
 * Array set all.
 * @param  array &$array
 * @param  array  $items
 * @return array
 * @since  4.0
 */
function array_set_all(array &$array, array $items): array
{
    return Arrays::setAll($array, $items);
}

/**
 * Array get.
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $value_default
 * @param  bool                          $drop
 * @return any|null
 * @since  3.0
 */
function array_get(array $array, $key, $value_default = null, bool $drop = false)
{
    return is_array($key) ? Arrays::getAll($array, $key, $value_default, $drop)
                          : Arrays::get($array, $key, $value_default, $drop);
}

/**
 * Array get all.
 * @param  array             &$array
 * @param  array<int|string>  $keys
 * @param  any|null           $value_default
 * @param  bool               $drop
 * @return array
 * @since  3.0
 */
function array_get_all(array $array, array $keys, $value_default = null, bool $drop = false): array
{
    return Arrays::getAll($array, $keys, $value_default, $drop);
}

/**
 * Array get random.
 * @param  array  &$array
 * @param  int     $limit
 * @param  bool    $pack
 * @param  bool    $drop
 * @return any|null
 * @since  4.12
 */
function array_get_random(array &$array, int $limit = 1, bool $pack = false, bool $drop = false)
{
    return Arrays::getRandom($array, $limit, $pack, $drop);
}

/**
 * Array pull.
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $value_default
 * @return any|null
 * @since  3.0
 */
function array_pull(array &$array, $key, $value_default = null)
{
    return is_array($key) ? Arrays::pullAll($array, $key, $value_default)
                          : Arrays::pull($array, $key, $value_default);
}

/**
 * Array pull all.
 * @param  array             &$array
 * @param  array<int|string>  $keys
 * @param  any|null           $value_default
 * @return array
 * @since  3.0
 */
function array_pull_all(array &$array, array $keys, $value_default = null): array
{
    return Arrays::pullAll($array, $keys, $value_default);
}

/**
 * Array pull random.
 * @param  array  &$array
 * @param  int     $limit
 * @param  bool    $pack
 * @return any|null
 * @since  4.12
 */
function array_pull_random(array &$array, int $limit = 1, bool $pack = false)
{
    return Arrays::pullRandom($array, $limit, $pack);
}

/**
 * Remove.
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @return array
 * @since  4.0
 */
function array_remove(array &$array, $key): array
{
    return is_array($key) ? Arrays::removeAll($array, $key)
                          : Arrays::remove($array, $key);
}

/**
 * Remove all.
 * @param  array             &$array
 * @param  array<int|string>  $keys
 * @return array
 * @since  4.0
 */
function array_remove_all(array &$array, array $keys): array
{
    return Arrays::removeAll($array, $keys);
}

/**
 * Array remove random.
 * @param  array  &$array
 * @param  int     $limit
 * @return any|null
 * @since  4.12
 */
function array_remove_random(array &$array, int $limit = 1): array
{
    return Arrays::removeRandom($array, $limit);
}

/**
 * Array compose.
 * @param  array<int|string> $keys
 * @param  array             $values
 * @param  any|null          $values_default
 * @return array
 * @since  4.11
 */
function array_compose(array $keys, array $values, $values_default = null): array
{
    return Arrays::compose($keys, $values, $values_default);
}

/**
 * Array complete.
 * @param  bool     $null_strings
 * @param  array    $keys
 * @param  array ...$arrays
 * @return array
 * @since  4.14
 */
function array_complete(bool $null_strings, array $keys, array ...$arrays): array
{
    return Arrays::complete($null_strings, $keys, ...$arrays);
}

/**
 * Array coalesce.
 * @param  bool     $null_strings
 * @param  array ...$arrays
 * @return array
 * @since  4.14
 */
function array_coalesce(bool $null_strings, array ...$arrays): array
{
    return Arrays::coalesce($null_strings, ...$arrays);
}

/**
 * Array test (like JavaScript Array.every()).
 * @param  array    $array
 * @param  callable $func
 * @return bool
 * @since  3.0
 */
function array_test(array $array, callable $func): bool
{
    return Arrays::test($array, $func);
}

/**
 * Array test all (like JavaScript Array.every()).
 * @param  array    $array
 * @param  callable $func
 * @return bool
 * @since  3.0
 */
function array_test_all(array $array, callable $func): bool
{
    return Arrays::testAll($array, $func);
}

/**
 * Array find.
 * @param  array    $array
 * @param  callable $func
 * @return any|null
 * @since  4.10
 */
function array_find(array $array, callable $func)
{
    return Arrays::find($array, $func);
}

/**
 * Array find all.
 * @param  array    $array
 * @param  callable $func
 * @param  bool     $useKeys
 * @return array
 * @since  4.10
 */
function array_find_all(array $array, callable $func, bool $useKeys = false): array
{
    return Arrays::findAll($array, $func, $useKeys);
}

/**
 * Array random.
 * @param  array &$array
 * @param  int    $limit
 * @param  bool   $pack Return as [key,value] pairs.
 * @param  bool   $drop
 * @return any|null
 * @since  4.0
 */
function array_random(array &$array, int $limit = 1, bool $pack = false, bool $drop = false)
{
    return Arrays::random($array, $limit, $pack, $drop);
}

/**
 * Array shuffle.
 * @param  array &$array
 * @param  bool   $keep_keys
 * @return array
 * @since  4.0
 */
function array_shuffle(array &$array, bool $keep_keys = true): array
{
    return Arrays::shuffle($array, $keep_keys);
}

/**
 * Array include.
 * @param  array             $array
 * @param  array<int|string> $keys
 * @return array
 * @since  3.0
 */
function array_include(array $array, array $keys): array
{
    return Arrays::include($array, $keys);
}

/**
 * Array exclude.
 * @param  array             $array
 * @param  array<int|string> $keys
 * @return array
 * @since  3.0
 */
function array_exclude(array $array, array $keys): array
{
    return Arrays::exclude($array, $keys);
}

/**
 * Array flatten.
 * @param  array $array
 * @param  bool $use_keys
 * @param  bool $fix_keys
 * @param  bool $one_dimension
 * @return array
 * @since  4.0
 */
function array_flatten(array $array, bool $use_keys = false, bool $fix_keys = false,
        bool $one_dimension = false): array
{
    return Arrays::flatten($array, $use_keys, $fix_keys, $one_dimension);
}

/**
 * Array swap.
 * @param  array      &$array
 * @param  int|string  $old_key
 * @param  int|string  $new_key
 * @param  any|null    $new_value_default
 * @return array
 * @since  4.2
 */
function array_swap(array &$array, $old_key, $new_key, $new_value_default = null): array
{
    return Arrays::swap($array, $old_key, $new_key, $new_value_default);
}

/**
 * Array sweep.
 * @param  array      &$array
 * @param  array|null  $ignored_keys
 * @return array
 * @since  4.0
 */
function array_sweep(array &$array, array $ignored_keys = null): array
{
    return Arrays::sweep($array, $ignored_keys);
}

/**
 * Array default.
 * @param  array    $array
 * @param  array    $keys
 * @param  bool     $use_keys
 * @param  any|null $default
 * @return array
 * @since  4.0
 */
function array_default(array $array, array $keys, bool $use_keys = true, $default = null): array
{
    return Arrays::default($array, $keys, $use_keys, $default);
}

/**
 * Array index.
 *
 * @param  array $array
 * @param  any   $value
 * @param  bool  $strict
 * @return int|string|null
 * @since  4.0
 */
function array_index(array $array, $value, bool $strict = true)
{
    return Arrays::index($array, $value, $strict);
}

/**
 * Array first.
 * @param  array &$array
 * @param  any    $value_default
 * @param  bool   $drop
 * @return any|null
 * @since  3.0
 */
function array_first(array $array, $value_default = null, bool $drop = false)
{
    return Arrays::first($array, $value_default, $drop);
}

/**
 * Array last.
 * @param  array &$array
 * @param  any    $value_default
 * @param  bool   $drop
 * @return any|null
 * @since  3.0
 */
function array_last(array $array, $value_default = null, bool $drop = false)
{
    return Arrays::last($array, $value_default, $drop);
}

/**
 * Array keys exists.
 * @param  array             $array
 * @param  array<int|string> $keys
 * @return bool
 */
function array_keys_exists(array $array, array $keys): bool
{
    return Arrays::keysExists($array, $keys);
}

/**
 * Array values exists.
 * @param  array $array
 * @param  any   $values
 * @param  bool  $strict
 * @return bool
 */
function array_values_exists(array $array, array $values, bool $strict = true): bool
{
    return Arrays::valuesExists($array, $values, $strict);
}

/**
 * Array search keys.
 * @param  array             $array
 * @param  array<int|string> $keys
 * @return array
 * @since  4.0
 */
function array_search_keys(array $array, array $keys): array
{
    return Arrays::searchKeys($array, $keys);
}

/**
 * Array search values.
 * @param  array $array
 * @param  array $values
 * @param  bool  $strict
 * @return array
 * @since  4.0
 */
function array_search_values(array $array, array $values, bool $strict = true): array
{
    return Arrays::searchValues($array, $values, $strict);
}
