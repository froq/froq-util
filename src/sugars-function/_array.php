<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\Arrays;

/** Basics. */

/**
 * @alias Arrays.isListArray()
 * @since 6.0
 */
function is_list_array(mixed $var, bool $strict = true): bool
{
    return is_array($var) && Arrays::isListArray($var, $strict);
}

/**
 * @alias Arrays.isAssocArray()
 * @since 6.0
 */
function is_assoc_array(mixed $var): bool
{
    return is_array($var) && Arrays::isAssocArray($var);
}

/**
 * @alias Arrays.set(), Arrays.setAll()
 * @since 3.0, 6.0
 **/
function array_set(array &$array, int|string|array $key, mixed $value = null): array
{
    if (is_array($key)) {
        return Arrays::setAll($array, $key);
    }
    return Arrays::set($array, $key, $value);
}

/**
 * @alias Arrays.setAll()
 * @since 4.0, 6.0
 */
function array_set_all(array &$array, array $items): array
{
    return Arrays::setAll($array, $items);
}

/**
 * @alias Arrays.get(), Arrays.getAll()
 * @since 3.0, 6.0
 */
function &array_get(array &$array, int|string|array $key, mixed $default = null, bool $drop = false): mixed
{
    if (is_array($key)) {
        return Arrays::getAll($array, $key, (array) $default, $drop);
    }
    return Arrays::get($array, $key, $default, $drop);
}

/**
 * @alias Arrays.getAll()
 * @since 3.0, 6.0
 */
function &array_get_all(array &$array, array $keys, array $defaults = null, bool $drop = false): array
{
    return Arrays::getAll($array, $keys, $defaults, $drop);
}

/**
 * @alias Arrays.pull(), Arrays.pullAll()
 * @since 3.0, 6.0
 */
function array_pull(array &$array, int|string|array $key, mixed $default = null): mixed
{
    if (is_array($key)) {
        return Arrays::pullAll($array, $key, (array) $default);
    }
    return Arrays::pull($array, $key, $default);
}

/**
 * @alias Arrays.pullAll()
 * @since 3.0, 6.0
 */
function array_pull_all(array &$array, array $keys, array $defaults = null): array
{
    return Arrays::pullAll($array, $keys, $defaults);
}

/**
 * @alias Arrays.remove(), Arrays.removeAll()
 * @since 4.0, 6.0
 */
function array_remove(array &$array, int|string|array $key): array
{
    if (is_array($key)) {
        return Arrays::removeAll($array, $key);
    }
    return Arrays::remove($array, $key);
}

/**
 * @alias Arrays.removeAll()
 * @since 4.0, 6.0
 */
function array_remove_all(array &$array, array $keys): array
{
    return Arrays::removeAll($array, $keys);
}

/**
 * @alias Arrays.getRandom()
 * @since 4.12, 6.0
 */
function array_get_random(array &$array, int $limit = 1, bool $pack = false, bool $drop = false): mixed
{
    return Arrays::getRandom($array, $limit, $pack, $drop);
}

/**
 * @alias Arrays.pullRandom()
 * @since 4.12, 6.0
 */
function array_pull_random(array &$array, int $limit = 1, bool $pack = false): mixed
{
    return Arrays::pullRandom($array, $limit, $pack);
}

/**
 * @alias Arrays.removeRandom()
 * @since 4.12, 6.0
 */
function array_remove_random(array &$array, int $limit = 1): array
{
    return Arrays::removeRandom($array, $limit);
}

/**
 * @alias Arrays.compose()
 * @since 4.11, 5.38, 6.0
 */
function array_compose(array $keys, array $values, mixed $default = null): array
{
    return Arrays::compose($keys, $values, $default);
}

/**
 * @alias Arrays.concat()
 * @since 5.30, 6.0
 */
function array_concat(array $array, mixed ...$items): array
{
    return Arrays::concat($array, ...$items);
}

/**
 * @alias Arrays.concat()
 * @since 5.0, 6.0
 */
function array_union(array $array1, array $array2, array ...$arrays): array
{
    return Arrays::union($array1, $array2, ...$arrays);
}

/**
 * @alias Arrays.dedupe()
 * @since 5.25, 6.0
 */
function array_dedupe(array $array, bool $strict = true, bool $list = null): array
{
    return Arrays::dedupe($array, $strict, $list);
}

/**
 * @alias Arrays.refine()
 * @since 6.0
 */
function array_refine(array $array, array $values = null, bool $list = null): array
{
    return Arrays::refine($array, $values, $list);
}

/**
 * @alias Arrays.group()
 * @since 5.31, 6.0
 */
function array_group(array $array, int|string|callable $field): array
{
    return Arrays::group($array, $field);
}

/**
 * @alias Arrays.collect()
 * @since 7.0
 */
function array_collect(array $array, int|string|callable $mapper, int|string|callable $field = null): array
{
    return Arrays::collect($array, $mapper, $field);
}

/**
 * @alias Arrays.take()
 * @since 7.0
 */
function array_take(array $array, int $limit, callable $filter = null, callable $map = null): array
{
    return Arrays::take($array, $limit, $filter, $map);
}

/**
 * @alias Arrays.test()
 * @since 3.0, 6.0
 */
function array_test(array $array, callable $func): bool
{
    return Arrays::test($array, $func);
}

/**
 * @alias Arrays.testAll()
 * @since 3.0, 6.0
 */
function array_test_all(array $array, callable $func): bool
{
    return Arrays::testAll($array, $func);
}

/**
 * @alias Arrays.find()
 * @since 5.0, 5.31, 6.0
 */
function array_find(array $array, callable $func, bool $reverse = false): mixed
{
    return Arrays::find($array, $func, $reverse);
}

/**
 * @alias Arrays.findAll()
 * @since 5.31, 6.0
 */
function array_find_all(array $array, callable $func, bool $reverse = false): array|null
{
    return Arrays::findAll($array, $func, $reverse);
}

/**
 * @alias Arrays.findKey()
 * @since 5.0, 6.0
 */
function array_find_key(array $array, callable $func, bool $reverse = false): int|string|null
{
    return Arrays::findKey($array, $func, $reverse);
}

/**
 * @alias Arrays.findKeys()
 * @since 5.31, 6.0
 */
function array_find_keys(array $array, callable $func, bool $reverse = false): array|null
{
    return Arrays::findKeys($array, $func, $reverse);
}

/**
 * @alias Arrays.swap()
 * @since 4.2, 6.0
 */
function array_swap(array &$array, int|string $old_key, int|string $new_key): array
{
    return Arrays::swap($array, $old_key, $new_key);
}

/**
 * @alias Arrays.swapValue()
 * @since 6.0
 */
function array_swap_value(array &$array, mixed $old_value, mixed $new_value): array
{
    return Arrays::swapValue($array, $old_value, $new_value);
}

/**
 * @alias Arrays.random()
 * @since 4.0, 6.0
 */
function array_random(array &$array, int $limit = 1, bool $pack = false, bool $drop = false): mixed
{
    return Arrays::random($array, $limit, $pack, $drop);
}

/**
 * @alias Arrays.shuffle()
 * @since 4.0, 5.25, 6.0
 */
function array_shuffle(array $array, bool $assoc = null): array
{
    return Arrays::shuffle($array, $assoc);
}

/**
 * @alias Arrays.include()
 * @since 3.0, 6.0
 */
function array_include(array $array, array $keys): array
{
    return Arrays::include($array, $keys);
}

/**
 * @alias Arrays.exclude()
 * @since 3.0, 6.0
 */
function array_exclude(array $array, array $keys): array
{
    return Arrays::exclude($array, $keys);
}

/**
 * @alias Arrays.split()
 * @since 6.0
 */
function array_split(array $array, int $length, bool $keep_keys = false): array
{
    return Arrays::split($array, $length, $keep_keys);
}

/**
 * @alias Arrays.divide()
 * @since 7.0
 */
function array_divide(array $array, int|string $offset): array
{
    return Arrays::divide($array, $offset);
}

/**
 * @alias Arrays.insert()
 * @since 7.0
 */
function array_insert(array $array, int|string $offset, array $entry): array
{
    return Arrays::insert($array, $offset, $entry);
}

/**
 * @alias Arrays.flat()
 * @since 4.0, 6.0
 */
function array_flat(array $array, bool $keep_keys = false, bool $fix_keys = false, bool $multi = true): array
{
    return Arrays::flat($array, $keep_keys, $fix_keys, $multi);
}

/**
 * @alias Arrays.compact()
 * @since 6.0
 */
function array_compact(int|string|array $keys, mixed ...$vars): array
{
    return Arrays::compact($keys, ...$vars);
}

/**
 * @alias Arrays.extract()
 * @since 6.0
 */
function array_extract(array $array, int|string|array $keys, mixed &...$vars): int
{
    return Arrays::extract($array, $keys, ...$vars);
}

/**
 * @alias Arrays.export()
 * @since 6.0
 */
function array_export(array $array, mixed &...$vars): int
{
    return Arrays::export($array, ...$vars);
}

/**
 * @alias Arrays.keysExist()
 * @since 1.0, 6.0
 */
function array_keys_exist(array $array, array $keys): bool
{
    return Arrays::keysExist($array, $keys);
}

/**
 * @alias Arrays.valuesExist()
 * @since 1.0, 6.0
 */
function array_values_exist(array $array, array $values, bool $strict = true): bool
{
    return Arrays::valuesExist($array, $values, $strict);
}

/**
 * @alias Arrays.searchKey()
 * @since 5.0, 6.0
 */
function array_search_key(array $array, mixed $value, bool $strict = true, bool $last = false): int|string|null
{
    return Arrays::searchKey($array, $value, $strict, $last);
}

/**
 * @alias Arrays.searchKeys()
 * @since 4.0, 5.25, 6.0
 */
function array_search_keys(array $array, mixed $value, bool $strict = true, bool $reverse = false): array|null
{
    return Arrays::searchKeys($array, $value, $strict, $reverse);
}

/**
 * @alias Arrays.padKeys()
 * @since 4.0, 6.0
 */
function array_pad_keys(array $array, array $keys, mixed $value = null, bool $isset = false): array
{
    return Arrays::padKeys($array, $keys, $value, $isset);
}

/**
 * @alias Arrays.lowerKeys()
 * @since 6.0
 */
function array_lower_keys(array $array, bool $recursive = false): array
{
    return Arrays::lowerKeys($array, $recursive);
}

/**
 * @alias Arrays.upperKeys()
 * @since 6.0
 */
function array_upper_keys(array $array, bool $recursive = false): array
{
    return Arrays::upperKeys($array, $recursive);
}

/**
 * @alias Arrays.convertKeys()
 * @since 4.19, 6.0
 */
function array_convert_keys(array $array, string|int $case, string $exploder = null, string $imploder = null, bool $recursive = false): array
{
    return Arrays::convertKeys($array, $case, $exploder, $imploder, $recursive);
}

/**
 * @alias Arrays.default()
 * @since 4.0, 6.0
 */
function array_default(array $array, array $keys, mixed $default = null): array
{
    return Arrays::default($array, $keys, $default);
}

/**
 * @alias Arrays.options()
 * @since 5.44, 6.0
 */
function array_options(array|null $options, array|null $defaults, bool $recursive = true, bool $map = true): array
{
    return Arrays::options($options, $defaults, $recursive, $map);
}

/**
 * @alias Arrays.first()
 * @since 5.0, 6.0
 */
function array_first(array $array): mixed
{
    return Arrays::first($array);
}

/**
 * @alias Arrays.last()
 * @since 5.0, 6.0
 */
function array_last(array $array): mixed
{
    return Arrays::last($array);
}

/**
 * @alias Arrays.sort()
 * @since 6.0
 */
function array_sort(array $array, callable|int $func = null, int $flags = 0, bool $assoc = null): array
{
    return Arrays::sort($array, $func, $flags, $assoc);
}

/**
 * @alias Arrays.sortKey()
 * @since 6.0
 */
function array_sort_key(array $array, callable|int $func = null, int $flags = 0): array
{
    return Arrays::sortKey($array, $func, $flags);
}

/**
 * @alias Arrays.sortLocale()
 * @since 6.0
 */
function array_sort_locale(array $array, string $locale = null, bool $assoc = null): array
{
    return Arrays::sortLocale($array, $locale, $assoc);
}

/**
 * @alias Arrays.sortNatural()
 * @since 6.0
 */
function array_sort_natural(array $array, bool $icase = false): array
{
    return Arrays::sortNatural($array, $icase);
}

/**
 * @alias Arrays.filter(keepKeys=false)
 * @since 6.0
 */
function array_filter_list(array $array, callable|string|array $func = null, bool $recursive = false, bool $use_keys = false): array
{
    return Arrays::filter($array, $func, $recursive, $use_keys, false);
}

/**
 * @alias Arrays.filter(recursive=true)
 * @since 5.40, 6.0
 */
function array_filter_recursive(array $array, callable|string|array $func = null, bool $use_keys = false): array
{
    return Arrays::filter($array, $func, true, $use_keys);
}

/**
 * @alias Arrays.filterKeys()
 * @since 6.0
 */
function array_filter_keys(array $array, callable $func, bool $recursive = false): array
{
    return Arrays::filterKeys($array, $func, $recursive);
}

/**
 * @alias Arrays.map(keepKeys=true)
 * @since 7.0
 */
function array_map_list(callable $func, array $array, bool $recursive = false, bool $use_keys = false): array
{
    return Arrays::map($array, $func, $recursive, $use_keys, false);
}

/**
 * @alias Arrays.map(recursive=true)
 * @since 5.1, 6.0
 */
function array_map_recursive(callable $func, array $array, bool $use_keys = false): array
{
    return Arrays::map($array, $func, true, $use_keys);
}

/**
 * @alias Arrays.mapKeys()
 * @since 5.0, 6.0
 */
function array_map_keys(callable $func, array $array, bool $recursive = false): array
{
    return Arrays::mapKeys($array, $func, $recursive);
}

/**
 * @alias Arrays.reduceRight()
 * @since 6.0
 */
function array_reduce_right(array $array, mixed $carry, callable $func = null): mixed
{
    return Arrays::reduceRight($array, $carry, $func, $right);
}

/**
 * @alias Arrays.reduceKeys()
 * @since 6.0
 */
function array_reduce_keys(array $array, mixed $carry, callable $func = null, bool $right = false): mixed
{
    return Arrays::reduceKeys($array, $carry, $func, $right);
}

/**
 * @alias Arrays.apply()
 * @since 4.0, 6.0
 */
function array_apply(array $array, callable $func, bool $recursive = false, bool $list = false): array
{
    return Arrays::apply($array, $func, $recursive, $list);
}

/**
 * @alias Arrays.aggregate()
 * @since 4.14, 4.15, 6.0
 */
function array_aggregate(array $array, callable $func, array $carry = null): array
{
    return Arrays::aggregate($array, $func, $carry);
}

/**
 * @alias Arrays.isset()
 * @since 4.0, 6.0
 */
function array_isset(array $array, int|string ...$keys): bool
{
    return Arrays::isset($array, ...$keys);
}

/**
 * @alias Arrays.unset()
 * @since 4.0, 6.0
 */
function array_unset(array &$array, int|string ...$keys): array
{
    return Arrays::unset($array, ...$keys);
}

/**
 * @alias Arrays.contains()
 * @since 5.0, 6.0
 */
function array_contains(array $array, mixed ...$values): bool
{
    return Arrays::contains($array, ...$values);
}

/**
 * @alias Arrays.containsKey()
 * @since 5.3, 6.0
 */
function array_contains_key(array $array, int|string ...$keys): bool
{
    return Arrays::containsKey($array, ...$keys);
}

/**
 * @alias Arrays.delete()
 * @since 5.0, 6.0
 */
function array_delete(array &$array, mixed ...$values): array
{
    return Arrays::delete($array, ...$values);
}

/**
 * @alias Arrays.deleteKey()
 * @since 5.31, 6.0
 */
function array_delete_key(array &$array, int|string ...$keys): array
{
    return Arrays::deleteKey($array, ...$keys);
}

/**
 * @alias Arrays.append()
 * @since 4.0, 6.0
 */
function array_append(array &$array, mixed ...$values): array
{
    return Arrays::append($array, ...$values);
}

/**
 * @alias Arrays.prepend()
 * @since 4.0, 6.0
 */
function array_prepend(array &$array, mixed ...$values): array
{
    return Arrays::prepend($array, ...$values);
}

/**
 * @alias Arrays.list()
 * @since 6.0
 */
function array_list(array $array, int $length = null): array
{
    return Arrays::list($array, $length);
}

/**
 * @alias Arrays.entries()
 * @since 5.19, 6.0
 */
function array_entries(array $array): array
{
    return Arrays::entries($array);
}

/**
 * @alias Arrays.pushEntry()
 * @since 5.22, 6.0
 */
function array_push_entry(array &$array, array $entry): array
{
    return Arrays::pushEntry($array, $entry);
}

/**
 * @alias Arrays.popEntry()
 * @since 5.22, 6.0
 */
function array_pop_entry(array &$array, array $default = null): array|null
{
    return Arrays::popEntry($array, $default);
}

/**
 * @alias Arrays.unshiftEntry()
 * @since 5.22, 6.0
 */
function array_unshift_entry(array &$array, array $entry): array
{
    return Arrays::unshiftEntry($array, $entry);
}

/**
 * @alias Arrays.shiftEntry()
 * @since 5.22, 6.0
 */
function array_shift_entry(array &$array, array $default = null): array|null
{
    return Arrays::shiftEntry($array);
}

/**
 * @alias Arrays.pushKey()
 * @since 6.0
 */
function array_push_key(array &$array, int|string $key, mixed $value): array
{
    return Arrays::pushKey($array, $key, $value);
}

/**
 * @alias Arrays.popKey()
 * @since 6.0
 */
function array_pop_key(array &$array, int|string $key, mixed $default = null): mixed
{
    return Arrays::popKey($array, $key, $default);
}

/**
 * @alias Arrays.pushLeft()
 * @since 6.0
 */
function array_push_left(array &$array, mixed $value, mixed ...$values): array
{
    return Arrays::pushLeft($array, $value, ...$values);
}

/**
 * @alias Arrays.popLeft()
 * @since 6.0
 */
function array_pop_left(array &$array, mixed $default = null): mixed
{
    return Arrays::popLeft($array, $default);
}

/** Selections. */

/**
 * @alias Arrays.choose()
 * @since 6.0
 */
function array_choose(array $array, int|string|array $key, mixed $default = null): mixed
{
    return Arrays::choose($array, $key, $default);
}

/**
 * @alias Arrays.select()
 * @since 5.0, 6.0
 */
function array_select(array $array, int|string|array $key, mixed $default = null, bool $combine = false): mixed
{
    return Arrays::select($array, $key, $default, $combine);
}

/**
 * Pluck one or many items from given array, modifying array.
 *
 * @param  array            &$array
 * @param  int|string|array $key
 * @param  mixed|null       $default
 * @param  bool             $combine
 * @return mixed
 * @since  4.13, 6.0
 */
function array_pluck(array &$array, int|string|array $key, mixed $default = null, bool $combine = false): mixed
{
    return Arrays::pluck($array, $key, $default, $combine);
}

/** Additions. */

/**
 * Check a value if exists with/without strict comparison as default, filling found key as ref-arg.
 *
 * @param  mixed           $value
 * @param  array           $array
 * @param  bool            $strict
 * @param  int|string|null &$key
 * @return bool
 * @since  4.0, 6.0
 */
function array_value_exists(mixed $value, array $array, bool $strict = true, int|string &$key = null): bool
{
    $key = ($found = array_search($value, $array, $strict)) !== false ? $found : null;

    return ($key !== null);
}

/** Semantics. */

/**
 * Check whether given key is in given array.
 *
 * @param  array      $array
 * @param  int|string $key
 * @return bool
 */
function is_array_key(array $array, int|string $key): bool
{
    return array_key_exists($key, $array);
}

/**
 * Check whether given value is in given array (with strict search).
 *
 * @param  array $array
 * @param  mixed $value
 * @return bool
 */
function is_array_value(array $array, mixed $value): bool
{
    return array_value_exists($value, $array);
}
