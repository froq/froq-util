<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Arrays;

/**
 * Check whether all keys are "int" in given array, or array is a list when strict.
 *
 * @param  array $array
 * @param  bool  $strict
 * @return bool
 */
function is_set_array(array $array, bool $strict = false): bool
{
    return Arrays::isSet($array, $strict);
}

/**
 * Check whether all keys are "string" in given array.
 *
 * @param  array $array
 * @return bool
 */
function is_map_array(array $array): bool
{
    return Arrays::isMap($array);
}

/**
 * Bridge function to Arrays.set()/setAll().
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any                           $value
 * @return array
 * @since  3.0
 */
function array_set(array &$array, int|string|array $key, $value): array
{
    return is_array($key) ? Arrays::setAll($array, $key)
                          : Arrays::set($array, $key, $value);
}

/**
 * Bridge function to Arrays.setAll().
 *
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
 * Bridge function to Arrays.get()/getAll().
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $default
 * @param  bool                          $drop
 * @return any|null
 * @since  3.0
 */
function array_get(array $array, int|string|array $key, $default = null, bool $drop = false)
{
    return is_array($key) ? Arrays::getAll($array, $key, $default, $drop)
                          : Arrays::get($array, $key, $default, $drop);
}

/**
 * Bridge function to Arrays.getAll().

 * @param  array             &$array
 * @param  array<int|string>  $keys
 * @param  any|null           $default
 * @param  bool               $drop
 * @return array
 * @since  3.0
 */
function array_get_all(array $array, array $keys, $default = null, bool $drop = false): array
{
    return Arrays::getAll($array, $keys, $default, $drop);
}

/**
 * Bridge function to Arrays.getRandom().
 *
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
 * Bridge function to Arrays.pull()/pullAll().
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @param  any|null                      $default
 * @return any|null
 * @since  3.0
 */
function array_pull(array &$array, int|string|array $key, $default = null)
{
    return is_array($key) ? Arrays::pullAll($array, $key, $default)
                          : Arrays::pull($array, $key, $default);
}

/**
 * Bridge function to Arrays.pullAll().
 *
 * @param  array             &$array
 * @param  array<int|string>  $keys
 * @param  any|null           $default
 * @return array
 * @since  3.0
 */
function array_pull_all(array &$array, array $keys, $default = null): array
{
    return Arrays::pullAll($array, $keys, $default);
}

/**
 * Bridge function to Arrays.pullRandom().
 *
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
 * Bridge function to Arrays.remove()/removeAll().
 *
 * @param  array                        &$array
 * @param  int|string|array<int|string>  $key
 * @return array
 * @since  4.0
 */
function array_remove(array &$array, int|string|array $key): array
{
    return is_array($key) ? Arrays::removeAll($array, $key)
                          : Arrays::remove($array, $key);
}

/**
 * Bridge function to Arrays.removeAll().
 *
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
 * Bridge function to Arrays.removeRandom().
 *
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
 * Bridge function to Arrays.compose().
 *
 * @param  array<int|string> $keys
 * @param  array             $values
 * @param  any|null          $default
 * @return array
 * @since  4.11
 */
function array_compose(array $keys, array $values, $default = null): array
{
    return Arrays::compose($keys, $values, $default);
}

/**
 * Bridge function to Arrays.complete().
 *
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
 * Bridge function to Arrays.coalesce().
 *
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
 * Bridge function to Arrays.test().
 *
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
 * Bridge function to Arrays.testAll().
 *
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
 * Bridge function to Arrays.find().
 *
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
 * Bridge function to Arrays.findAll().
 *
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
 * Bridge function to Arrays.random().
 *
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
 * Bridge function to Arrays.shuffle().
 *
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
 * Bridge function to Arrays.include().
 *
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
 * Bridge function to Arrays.exclude().
 *
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
 * Bridge function to Arrays.swap().
 *
 * @param  array      &$array
 * @param  int|string  $old_key
 * @param  int|string  $new_key
 * @param  any|null    $default
 * @return array
 * @since  4.2
 */
function array_swap(array &$array, int|string $old_key, int|string $new_key, $default = null): array
{
    return Arrays::swap($array, $old_key, $new_key, $default);
}

/**
 * Bridge function to Arrays.sweep().
 *
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
 * Bridge function to Arrays.default().
 *
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
 * Bridge function to Arrays.index().
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
 * Bridge function to Arrays.keysExist().
 *
 * @param  array             $array
 * @param  array<int|string> $keys
 * @return bool
 */
function array_keys_exist(array $array, array $keys): bool
{
    return Arrays::keysExist($array, $keys);
}

/**
 * Bridge function to Arrays.valuesExist().
 *
 * @param  array $array
 * @param  any   $values
 * @param  bool  $strict
 * @return bool
 */
function array_values_exist(array $array, array $values, bool $strict = true): bool
{
    return Arrays::valuesExist($array, $values, $strict);
}

/**
 * Bridge function to Arrays.searchKeys().
 *
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
 * Bridge function to Arrays.searchValues().
 *
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
