<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Arrayable, Listable, Jsonable, Iteratable, IteratableReverse};
use froq\collection\trait\{SortTrait, FilterTrait, MapTrait, ReduceTrait, ApplyTrait, AggregateTrait,
    EachTrait, ForEachTrait, CountTrait, EmptyTrait, HasTrait, GetTrait, AccessTrait, AccessMagicTrait,
    FindTrait, FirstLastTrait, MinMaxTrait, CalcAvgTrait, CalcSumTrait, IteratorTrait,
    ToArrayTrait, ToListTrait, ToJsonTrait};
use froq\collection\iterator\{ArrayIterator, ReverseArrayIterator};

/**
 * A class for playing with arrays in OOP-way.
 *
 * @package global
 * @class   XArray
 * @author  Kerem Güneş
 * @since   6.0
 */
class XArray implements Arrayable, Listable, Jsonable, Iteratable, IteratableReverse, Countable, Iterator, ArrayAccess
{
    use SortTrait, FilterTrait, MapTrait, ReduceTrait, ApplyTrait, AggregateTrait,
        EachTrait, ForEachTrait, CountTrait, EmptyTrait, HasTrait, GetTrait, AccessTrait, AccessMagicTrait,
        FindTrait, FirstLastTrait, MinMaxTrait, CalcAvgTrait, CalcSumTrait, IteratorTrait,
        ToArrayTrait, ToListTrait, ToJsonTrait;

    /** Data holder. */
    protected array $data = [];

    /**
     * Constructor.
     *
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        $data && $this->data = iterator_to_array($data);
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    // Access.

    /**
     * Add items.
     *
     * @param  mixed ...$values
     * @return self
     */
    public function add(mixed ...$values): self
    {
        foreach ($values as $value) {
            $this->data[] = $value;
        }

        return $this;
    }

    /**
     * Drop items.
     *
     * @param  mixed ...$values
     * @return self
     */
    public function drop(mixed ...$values): self
    {
        foreach ($values as $value) {
            $keys = array_keys($this->data, $value, true);
            foreach ($keys as $key) {
                unset($this->data[$key]);
            }
        }

        return $this;
    }

    /**
     * Set an item.
     *
     * @param  int|string $key
     * @param  mixed      $value
     * @return self
     */
    public function set(int|string $key, mixed $value): self
    {
        $this->keyCheck($key);

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Get an item.
     *
     * @param  int|string $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function &get(int|string $key, mixed $default = null): mixed
    {
        $this->keyCheck($key);

        // Breaking reference in such calls:
        // $foo = $array->get('absent-field', new Object());
        // $value = &$this->data[$key] ?? $default;

        if (isset($this->data[$key])) {
            $value = &$this->data[$key];
        } else {
            $value = &$default;
        }

        return $value;
    }

    /**
     * Remove an item.
     *
     * @param  int|string $key
     * @return self
     */
    public function remove(int|string $key): self
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Repose an item replacing with an entry.
     *
     * @param  int|string $key
     * @param  array      $entry Expects key,value pairs.
     * @return self
     */
    public function repose(int|string $key, array $entry): self
    {
        unset($this->data[$key]);

        $this->data[$entry[0]] = $entry[1];

        return $this;
    }

    /**
     * Choose an item.
     *
     * @param  int|string|array $key
     * @param  mixed|null       $default
     * @return mixed
     */
    public function choose(int|string|array $key, mixed $default = null): mixed
    {
        return array_choose($this->data, $key, $default);
    }

    /**
     * Select an item or items.
     *
     * @param  int|string|array $key
     * @param  mixed|null       $default
     * @param  bool             $combine
     * @return mixed
     */
    public function select(int|string|array $key, mixed $default = null, bool $combine = false): mixed
    {
        return array_select($this->data, $key, $default, $combine);
    }

    /**
     * Pluck an item or items.
     *
     * @param  int|string|array $key
     * @param  mixed|null       $default
     * @param  bool             $combine
     * @return mixed
     */
    public function pluck(int|string|array $key, mixed $default = null, bool $combine = false): mixed
    {
        return array_pluck($this->data, $key, $default, $combine);
    }

    /**
     * Put (good for dotted path notations).
     *
     * @param  int|string $key
     * @param  mixed      $value
     * @return self
     */
    public function put(int|string $key, mixed $value): self
    {
        array_set($this->data, $key, $value);

        return $this;
    }

    /**
     * Pull (good for dotted path notations).
     *
     * @param  int|string|array $key
     * @param  mixed|null       $default
     * @param  bool             $drop
     * @return mixed
     */
    public function pull(int|string|array $key, mixed $default = null, bool $drop = false): mixed
    {
        return array_get($this->data, $key, $default, $drop);
    }

    // Basic.

    /**
     * Cut.
     *
     * @param  int  $length
     * @param  bool $keepKeys
     * @return self
     */
    public function cut(int $length, bool $keepKeys = false): self
    {
        $this->data = array_slice($this->data, 0, $length, $keepKeys);

        return $this;
    }

    /**
     * Chunk.
     *
     * @param  int  $length
     * @param  bool $keepKeys
     * @return self
     */
    public function chunk(int $length, bool $keepKeys = false): self
    {
        $this->data = array_chunk($this->data, $length, $keepKeys);

        return $this;
    }

    /**
     * Concat.
     *
     * @param  mixed    $item
     * @param  mixed ...$items
     * @return self
     */
    public function concat(mixed $item, mixed ...$items): self
    {
        $this->data = array_concat($this->data, $item, ...$items);

        return $this;
    }

    /**
     * Slice.
     *
     * @param  int      $start
     * @param  int|null $end
     * @param  bool     $keepKeys
     * @return self
     */
    public function slice(int $start, int $end = null, bool $keepKeys = false): self
    {
        $this->data = array_slice($this->data, $start, $end, $keepKeys);

        return $this;
    }

    /**
     * Splice.
     *
     * @param  int         $start
     * @param  int|null    $end
     * @param  mixed|null  $replace
     * @param  mixed|null &$replaced
     * @return self
     */
    public function splice(int $start, int $end = null, mixed $replace = null, mixed &$replaced = null): self
    {
        $replaced = array_splice($this->data, $start, $end, $replace);

        return $this;
    }

    /**
     * Split.
     *
     * @param  int  $length
     * @param  bool $keepKeys
     * @return self
     */
    public function split(int $length, bool $keepKeys = false): self
    {
        $this->data = array_split($this->data, $length, $keepKeys);

        return $this;
    }

    /**
     * Divide.
     *
     * @param  int|string $offset
     * @return self
     */
    public function divide(int|string $offset): self
    {
        $this->data = array_divide($this->data, $offset);

        return $this;
    }

    /**
     * Insert.
     *
     * @param  int|string $offset
     * @param  array      $entry
     * @return self
     */
    public function insert(int|string $offset, array $entry): self
    {
        $this->data = array_insert($this->data, $offset, $entry);

        return $this;
    }

    /**
     * Update self data by given data.
     *
     * @param  iterable $data
     * @param  bool     $merge
     * @return self
     */
    public function update(iterable $data, bool $merge = true): self
    {
        foreach ($data as $key => $value) {
            // Handle current iterable fields to keep as original.
            // Eg: New "c" will be appended to old "c" item (xarray/array),
            // instead of overwriting on old "c" item as "33".
            // $x = xarray([...$a, 'c' => xarray($c)]);
            // $x->update([...$b, 'c' => ['c' => 33]]);
            if ($merge && is_iterable($value) && is_iterable($current = $this->get($key))) {
                $value = static::from($current)->update($value, true);
                $value = is_array($current) ? $value->data : $value;
                unset($current);
            }

            $this->set($key, $value);
        }

        return $this;
    }

    // Sugars.

    /**
     * Union with given data applying unique check.
     *
     * @param  iterable ...$data
     * @return self
     */
    public function union(iterable ...$data): self
    {
        $this->data = array_union($this->data, ...$this->prepare($data));

        return $this;
    }

    /**
     * Dedupe values applying unique check.
     *
     * @param  bool      $strict
     * @param  bool|null $list
     * @return self
     */
    public function dedupe(bool $strict = true, bool $list = null): self
    {
        $this->data = array_dedupe($this->data, $strict, $list);

        return $this;
    }

    /**
     * Refine filtering given or null, "" and [] items as default.
     *
     * @param  array|null $values
     * @param  bool|null  $list
     * @return self
     */
    public function refine(array $values = null, bool $list = null): self
    {
        $this->data = array_refine($this->data, $values, $list);

        return $this;
    }

    /**
     * Group by given field.
     *
     * @param  int|string|callable $field
     * @return self
     */
    public function group(int|string|callable $field): self
    {
        $this->data = array_group($this->data, $field);

        return $this;
    }

    /**
     * Collect by given mapper & field.
     *
     * @param  int|string|callable      $mapper
     * @param  int|string|callable|null $field
     * @return self
     */
    public function collect(int|string|callable $mapper, int|string|callable $field = null): self
    {
        $this->data = array_collect($this->data, $mapper, $field);

        return $this;
    }

    /**
     * Take items by given limit, optionally with filter/map callbacks.
     *
     * @param  int           $limit
     * @param  callable|null $filter
     * @param  callable|null $map
     * @return self
     */
    public function take(int $limit, callable $filter = null, callable $map = null): self
    {
        $this->data = array_take($this->data, $limit, $filter, $map);

        return $this;
    }

    /**
     * Test, like JavaScript Array.some().
     *
     * @param  callable $func
     * @return bool
     */
    public function test(callable $func): bool
    {
        return array_test($this->data, $func);
    }

    /**
     * Test all, like JavaScript Array.every().
     *
     * @param  callable $func
     * @return bool
     */
    public function testAll(callable $func): bool
    {
        return array_test_all($this->data, $func);
    }

    /**
     * Swap and old key with new key if exists.
     *
     * @param  int|string $oldKey
     * @param  int|string $newKey
     * @return self
     */
    public function swap(int|string $oldKey, int|string $newKey): self
    {
        $this->data = array_swap($this->data, $oldKey, $newKey);

        return $this;
    }

    /**
     * Swap an old value with new value if exists.
     *
     * @param  mixed  $oldValue
     * @param  mixed  $newValue
     * @return self
     */
    public function swapValue(mixed $oldValue, mixed $newValue): self
    {
        $this->data = array_swap_value($this->data, $oldValue, $newValue);

        return $this;
    }

    /**
     * Random getter.
     *
     * @param  int    $limit
     * @param  bool   $pack
     * @param  bool   $drop
     * @return mixed
     */
    public function random(int $limit = 1, bool $pack = false, bool $drop = false): mixed
    {
        return array_random($this->data, $limit, $pack, $drop);
    }

    /**
     * Shuffle maker.
     *
     * @param  bool|null $assoc
     * @return self
     */
    public function shuffle(bool $assoc = null): self
    {
        $this->data = array_shuffle($this->data, $assoc);

        return $this;
    }

    /**
     * Include given keys only.
     *
     * @param  array $keys
     * @return self
     */
    public function include(array $keys): self
    {
        $this->data = array_include($this->data, $keys);

        return $this;
    }

    /**
     * Exclude given keys.
     *
     * @param  array $keys
     * @return self
     */
    public function exclude(array $keys): self
    {
        $this->data = array_exclude($this->data, $keys);

        return $this;
    }

    /**
     * Flat maker.
     *
     * @param  bool $keepKeys
     * @param  bool $fixKeys
     * @param  bool $multi
     * @return self
     */
    public function flat(bool $keepKeys = false, bool $fixKeys = false, bool $multi = true): self
    {
        $this->data = array_flat($this->data, $keepKeys, $fixKeys, $multi);

        return $this;
    }

    /**
     * Compact given keys with given vars.
     *
     * @param  array    $keys
     * @param  mixed ...$vars
     * @return self
     */
    public function compact(int|string|array $keys, mixed ...$vars): self
    {
        $this->data = array_compact($keys, ...$vars);

        return $this;
    }

    /**
     * Extract given keys to given vars (with refs).
     *
     * @param  array     $keys
     * @param  mixed &...$vars
     * @return int
     */
    public function extract(int|string|array $keys, mixed &...$vars): int
    {
        return array_extract($this->data, $keys, ...$vars);
    }

    /**
     * Export keys to vars given as list or named argument.
     *
     * @param  mixed &...$vars
     * @return int
     */
    public function export(mixed &...$vars): int
    {
        return array_export($this->data, ...$vars);
    }

    /**
     * Ensure given keys with/without given default.
     *
     * @param  array      $keys
     * @param  mixed|null $default
     * @return self
     */
    public function default(array $keys, mixed $default = null): self
    {
        $this->data = array_default($this->data, $keys, $default);

        return $this;
    }

    /**
     * Convert key cases to lower.
     *
     * @param  bool $recursive
     * @return self
     */
    public function lowerKeys(bool $recursive = false): self
    {
        $this->data = array_lower_keys($this->data, $recursive);

        return $this;
    }

    /**
     * Convert key cases to upper.
     *
     * @param  bool $recursive
     * @return self
     */
    public function upperKeys(bool $recursive = false): self
    {
        $this->data = array_upper_keys($this->data, $recursive);

        return $this;
    }

    /**
     * Convert key cases to given case.
     *
     * @param  int         $case
     * @param  string|null $exploder
     * @param  string|null $imploder
     * @param  bool        $recursive
     * @return self
     */
    public function convertKeys(int $case, string $exploder = null, string $imploder = null, bool $recursive = false): self
    {
        $this->data = array_convert_keys($this->data, $case, $exploder, $imploder, $recursive);

        return $this;
    }

    /**
     * Check whether all given keys were set.
     *
     * @param  int|string ...$keys
     * @return bool
     */
    public function isset(int|string ...$keys): bool
    {
        return array_isset($this->data, ...$keys);
    }

    /**
     * Delete all given keys.
     *
     * @param  int|string ...$keys
     * @return bool
     */
    public function unset(int|string ...$keys): array
    {
        return array_unset($this->data, ...$keys);
    }

    /**
     * Check whether array contains any of given values.
     *
     * @param  mixed ...$values
     * @return bool
     */
    public function contains(mixed ...$values): bool
    {
        return array_contains($this->data, ...$values);
    }

    /**
     * Check whether array contains any of given keys.
     *
     * @param  int|string ...$keys
     * @return bool
     */
    public function containsKey(int|string ...$keys): bool
    {
        return array_contains_key($this->data, ...$keys);
    }

    /**
     * Delete given values.
     *
     * @param  mixed ...$values
     * @return self
     */
    public function delete(mixed ...$values): self
    {
        array_delete($this->data, ...$values);

        return $this;
    }

    /**
     * Delete given keys.
     *
     * @param  int|string ...$keys
     * @return self
     */
    public function deleteKey(int|string ...$keys): self
    {
        array_delete_key($this->data, ...$keys);

        return $this;
    }

    /**
     * Append given values.
     *
     * @param  mixed ...$values
     * @return self
     */
    public function append(mixed ...$values): self
    {
        array_append($this->data, ...$values);

        return $this;
    }

    /**
     * Prepend given values.
     *
     * @param  mixed ...$values
     * @return self
     */
    public function prepend(mixed ...$values): self
    {
        array_prepend($this->data, ...$values);

        return $this;
    }

    /**
     * Push an item.
     *
     * @param  mixed $value
     * @return self
     */
    public function push(mixed $value): self
    {
        array_push($this->data, $value);

        return $this;
    }

    /**
     * Pop an item.
     *
     * @param  mixed|null $default
     * @return mixed
     */
    public function pop(mixed $default = null): mixed
    {
        return array_pop($this->data) ?? $default;
    }

    /**
     * Push an item with a key.
     *
     * @param  int|string $key
     * @param  mixed      $value
     * @return self
     */
    public function pushKey(int|string $key, mixed $value): self
    {
        array_push_key($this->data, $key, $value);

        return $this;
    }

    /**
     * Pop an item with a key.
     *
     * @param  int|string $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function popKey(int|string $key, mixed $default = null): mixed
    {
        return array_pop_key($this->data, $key, $default);
    }

    /**
     * Push an item to left.
     *
     * @param  mixed $value
     * @return self
     */
    public function pushLeft(mixed $value): self
    {
        array_push_left($this->data, $value);

        return $this;
    }

    /**
     * Pop an item from left.
     *
     * @param  mixed|null $default
     * @return mixed
     */
    public function popLeft(mixed $default = null): mixed
    {
        return array_pop_left($this->data, $default);
    }

    /**
     * Unshift an item.
     *
     * @param  mixed $value
     * @return self
     */
    public function unshift(mixed $value): self
    {
        array_unshift($this->data, $value);

        return $this;
    }

    /**
     * Shift an item.
     *
     * @param  mixed|null $default
     * @return mixed
     */
    public function shift(mixed $default = null): mixed
    {
        return array_shift($this->data) ?? $default;
    }

    /**
     * Wrap.
     *
     * @param  mixed      $left
     * @param  mixed|null $right
     * @return self
     */
    public function wrap(mixed $left, mixed $right = null): self
    {
        $this->prepend($left)->append($right ?? $left);

        return $this;
    }

    /**
     * Unwrap.
     *
     * @param  mixed      $left
     * @param  mixed|null $right
     * @return self
     */
    public function unwrap(mixed $left, mixed $right = null): self
    {
        $right ??= $left;
        $count = $this->count();

        while ($count--) {
            if ($this->first() === $left) {
                $this->shift();
                $count--; // Reduce.
            }
            if ($this->last() === $right) {
                $this->pop();
                $count--; // Reduce.
            }
        }

        return $this;
    }

    /**
     * Use keys.
     *
     * @return self
     */
    public function useKeys(): self
    {
        $this->data = array_keys($this->data);

        return $this;
    }

    /**
     * Use values.
     *
     * @return self
     */
    public function useValues(): self
    {
        $this->data = array_values($this->data);

        return $this;
    }

    // Internal & addition.

    /**
     * Flip.
     *
     * @return self
     */
    public function flip(): self
    {
        $this->data = array_flip($this->data);

        return $this;
    }

    /**
     * Flip keys.
     *
     * @return self
     */
    public function flipKeys(): self
    {
        $this->data = array_flip(array_keys($this->data));

        return $this;
    }

    /**
     * Pad.
     *
     * @param  int        $length
     * @param  mixed|null $value
     * @return self
     */
    public function pad(int $length, mixed $value = null): self
    {
        $this->data = array_pad($this->data, $length, $value);

        return $this;
    }

    /**
     * Pad keys.
     *
     * @param  array      $keys
     * @param  mixed|null $value
     * @param  bool       $isset
     * @return self
     */
    public function padKeys(array $keys, mixed $value = null, bool $isset = false): self
    {
        $this->data = array_pad_keys($this->data, $keys, $value, $isset);

        return $this;
    }

    /**
     * Fill.
     *
     * @param  int        $length
     * @param  mixed|null $value
     * @return self
     */
    public function fill(int $length, mixed $value = null): self
    {
        $this->data = array_fill(0, $length, $value);

        return $this;
    }

    /**
     * Fill keys.
     *
     * @param  array      $keys
     * @param  mixed|null $value
     * @return self
     */
    public function fillKeys(array $keys, mixed $value = null): self
    {
        $this->data = array_fill_keys($keys, $value);

        return $this;
    }

    /**
     * Column.
     *
     * @param  int|string|null columnKey
     * @param  int|string|null indexKey
     * @return self
     */
    public function column(int|string|null $columnKey, int|string|null $indexKey = null): self
    {
        $this->data = array_column($this->data, $columnKey, $indexKey);

        return $this;
    }

    /**
     * Combine.
     *
     * @param  array      $keys
     * @param  array|null $values
     * @return self
     */
    public function combine(array $keys, array $values = null): self
    {
        $this->data = array_combine($keys, $values ?? $this->values());

        return $this;
    }

    /**
     * Compose.
     *
     * @param  array      $keys
     * @param  array|null $values
     * @param  mixed|null $default
     * @return self
     */
    public function compose(array $keys, array $values = null, mixed $default = null): self
    {
        $this->data = array_compose($keys, $values ?? $this->values(), $default);

        return $this;
    }

    /**
     * Like (mutual values).
     *
     * @param  iterable $data
     * @param  bool     $strict
     * @return self
     */
    public function like(iterable $data, bool $strict = true): self
    {
        $data = $this->prepare($data);

        $this->data = array_filter(
            $this->data,
            fn($value): bool => in_array($value, $data, $strict)
        );

        return $this;
    }

    /**
     * Unlike (non-mutual values).
     *
     * @param  iterable $data
     * @param  bool     $strict
     * @return self
     */
    public function unlike(iterable $data, bool $strict = true): self
    {
        $data = $this->prepare($data);

        $this->data = array_filter(
            $this->data,
            fn($value): bool => !in_array($value, $data, $strict)
        );

        return $this;
    }

    /**
     * Diff.
     *
     * @param  iterable      $data
     * @param  callable|null $func
     * @param  bool          $assoc
     * @return self
     */
    public function diff(iterable $data, callable $func = null, bool $assoc = false): self
    {
        $data = $this->prepare($data);

        if ($func) {
            $this->data = $assoc ? array_udiff_assoc($this->data, $data)
                                 : array_udiff($this->data, $data);
        } else {
            $this->data = $assoc ? array_diff_assoc($this->data, $data)
                                 : array_diff($this->data, $data);
        }

        return $this;
    }

    /**
     * Diff-key.
     *
     * @param  iterable      $data
     * @param  callable|null $func
     * @return self
     */
    public function diffKey(iterable $data, callable $func = null): self
    {
        $data = $this->prepare($data);

        if ($func) {
            $this->data = array_diff_ukey($this->data, $data);
        } else {
            $this->data = array_diff_key($this->data, $data);
        }

        return $this;
    }

    /**
     * Intersect.
     *
     * @param  iterable      $data
     * @param  callable|null $func
     * @param  bool          $assoc
     * @return self
     */
    public function intersect(iterable $data, callable $func = null, bool $assoc = false): self
    {
        $data = $this->prepare($data);

        if ($func) {
            $this->data = $assoc ? array_uintersect_assoc($this->data, $data)
                                 : array_uintersect($this->data, $data);
        } else {
            $this->data = $assoc ? array_intersect_assoc($this->data, $data)
                                 : array_intersect($this->data, $data);
        }

        return $this;
    }

    /**
     * Intersect-key.
     *
     * @param  iterable $data
     * @return self
     */
    public function intersectKey(iterable $data): self
    {
        $data = $this->prepare($data);

        if ($func) {
           $this->data = array_intersect_ukey($this->data, $data);
        } else {
           $this->data = array_intersect_key($this->data, $data);
        }

        return $this;
    }

    /**
     * Merge.
     *
     * @param  iterable ...$data
     * @return self
     */
    public function merge(iterable ...$data): self
    {
        $this->data = array_merge($this->data, ...$this->prepare($data));

        return $this;
    }

    /**
     * Merge recursive.
     *
     * @param  iterable ...$data
     * @return self
     */
    public function mergeRecursive(iterable ...$data): self
    {
        $this->data = array_merge_recursive($this->data, ...$this->prepare($data));

        return $this;
    }

    /**
     * Replace.
     *
     * @param  iterable ...$data
     * @return self
     */
    public function replace(iterable ...$data): self
    {
        $this->data = array_replace($this->data, ...$this->prepare($data));

        return $this;
    }

    /**
     * Replace recursive.
     *
     * @param  iterable ...$data
     * @return self
     */
    public function replaceRecursive(iterable ...$data): self
    {
        $this->data = array_replace_recursive($this->data, ...$this->prepare($data));

        return $this;
    }

    /**
     * Walk.
     *
     * @param  callable   $func
     * @param  mixed|null $funcArg
     * @return self
     */
    public function walk(callable $func, mixed $funcArg = null): self
    {
        array_walk($this->data, $func, $funcArg);

        return $this;
    }

    /**
     * Walk recursive.
     *
     * @param  callable   $func
     * @param  mixed|null $funcArg
     * @return self
     */
    public function walkRecursive(callable $func, mixed $funcArg = null): self
    {
        array_walk_recursive($this->data, $func, $funcArg);

        return $this;
    }

    /**
     * Search.
     *
     * @param  mixed $value
     * @param  bool  $strict
     * @param  bool  $last
     * @return int|string|null
     */
    public function search(mixed $value, bool $strict = true, bool $last = false): int|string|null
    {
        return array_search_key($this->data, $value, $strict, $last);
    }

    /**
     * Search keys.
     *
     * @param  mixed $value
     * @param  bool  $strict
     * @param  bool  $reverse
     * @return array|null
     */
    public function searchKeys(mixed $value, bool $strict = true, bool $reverse = false): array|null
    {
        return array_search_keys($this->data, $value, $strict, $reverse);
    }

    /**
     * Reverse.
     *
     * @param  bool $keepKeys
     * @return self
     */
    public function reverse(bool $keepKeys = false): self
    {
        $this->data = array_reverse($this->data, $keepKeys);

        return $this;
    }

    /**
     * Unique.
     *
     * @param int $flags
     * @retur self
     */
    public function unique(int $flags = SORT_STRING): self
    {
        $this->data = array_unique($this->data, $flags);

        return $this;
    }

    // Misc.

    /**
     * List data check.
     *
     * @return bool
     */
    public function isList(): bool
    {
        return is_list_array($this->data);
    }

    /**
     * Assoc data check.
     *
     * @return bool
     */
    public function isAssoc(): bool
    {
        return is_assoc_array($this->data);
    }

    /**
     * Get data keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Get data values.
     *
     * @return array
     */
    public function values(): array
    {
        return array_values($this->data);
    }

    /**
     * Get data entries.
     *
     * @return array
     */
    public function entries(): array
    {
        return array_entries($this->data);
    }

    /**
     * Get key of given value, or return null.
     *
     * @param  mixed $value
     * @return int|string|null
     */
    public function keyOf(mixed $value): int|string|null
    {
        return array_search_key($this->data, $value);
    }

    /**
     * Get last key of given value, or return null.
     *
     * @param  mixed $value
     * @return int|string|null
     */
    public function lastKeyOf(mixed $value): int|string|null
    {
        return array_search_key($this->data, $value, last: true);
    }

    /**
     * Get an item without ref, or return default.
     *
     * @param  int|string $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function valueOf(int|string $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
    }

    /**
     * Get item.
     *
     * @param  int|string $key
     * @return mixed
     */
    public function item(int|string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * Get items.
     *
     * @param  array<int|string>|null $keys
     * @return array
     */
    public function items(array $keys = null): array
    {
        if ($keys === null) {
            return $this->data;
        }

        $items = [];

        foreach ($keys as $key) {
            $items[$key] = $this->get($key);
        }

        return $items;
    }

    /**
     * Get items, optionally with keys.
     *
     * @param  array<int|string>|null $keys
     * @param  callable|null          $map
     * @param  bool                   $combine
     * @return array
     */
    public function all(array $keys = null, callable $map = null, bool $combine = false): array
    {
        $items = $this->items($keys);

        if ($map !== null) {
            $items = array_map($map, $items);
        }

        return $combine ? $items : array_values($items);
    }

    /**
     * Format.
     *
     * @param  string $format
     * @return string
     */
    public function format(string $format): string
    {
        return format($format, ...$this->data);
    }

    /**
     * Join.
     *
     * @param  string $glue
     * @return string
     */
    public function join(string $glue = ''): string
    {
        return join($glue, $this->data);
    }

    /**
     * X-join.
     *
     * @param  string $glue
     * @return XString
     */
    public function xjoin(string $glue = ''): XString
    {
        return new XString($this->join($glue));
    }

    // Copy & inherit.

    /**
     * Get a copy instance.
     *
     * @return static
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Get data list copy as a new static instance.
     *
     * @return static
     */
    public function copyList(): static
    {
        return new static($this->toList());
    }

    /**
     * Copy this data to that data.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyTo(self $that): static
    {
        return $that->update($this->data);
    }

    /**
     * Copy this data from that data.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyFrom(self $that): static
    {
        return $this->update($that->data);
    }

    /**
     * @inheritDoc Iteratable
     */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @inheritDoc IteratableReverse
     */
    public function getReverseIterator(): iterable
    {
        return new ReverseArrayIterator($this->data);
    }

    /**
     * Convert to Map.
     *
     * @return Map
     */
    public function toMap(): Map
    {
        return new Map($this);
    }

    /**
     * Convert to Set.
     *
     * @return Set
     */
    public function toSet(): Set
    {
        return new Set($this);
    }

    // Static.

    /**
     * Static constructor.
     *
     * @param  mixed ...$data
     * @return static
     */
    public static function of(mixed ...$data): static
    {
        return new static($data);
    }

    /**
     * Static constructor.
     *
     * @param  iterable $data
     * @return static
     */
    public static function from(iterable $data): static
    {
        return new static($data);
    }

    /**
     * Static constructor from given keys (and value optionally).
     *
     * @param  array      $keys
     * @param  mixed|null $value
     * @return static
     */
    public static function fromKeys(array $keys, mixed $value = null): static
    {
        return new static(array_fill_keys($keys, $value));
    }

    /**
     * Static constructor from a string & split pattern.
     *
     * @param  string   $pattern
     * @param  string   $string
     * @param  int|null $limit
     * @param  int|null $flags
     * @return static
     */
    public static function fromSplit(string $pattern, string $string, int $limit = null, int $flags = null): static
    {
        return new static(split($pattern, $string, $limit, $flags));
    }

    /**
     * Static constructor from a glob.
     *
     * @param  string   $pattern
     * @param  int|null $flags
     * @return static
     */
    public static function fromGlob(string $pattern, int $flags = null): static
    {
        return new static(glob($pattern, $flags ?? 0) ?: []);
    }

    /**
     * Static constructor from a range.
     *
     * @param  int|float|string $min
     * @param  int|float|string $max
     * @param  int|float        $step
     * @return static
     */
    public static function fromRange(int|float|string $min, int|float|string $max, int|float $step = 1): static
    {
        return new static(range($min, $max, $step));
    }

    /**
     * Static constructor from a random range.
     *
     * @param  int            $length
     * @param  int|float|null $min
     * @param  int|float|null $max
     * @param  int|null       $precision
     * @return static
     */
    public static function fromRandomRange(int $length, int|float $min = null, int|float $max = null, int $precision = null): static
    {
        return new static(random_range($length, $min, $max, $precision));
    }

    /**
     * Check key validity & prepare (as shortcut).
     *
     * @param  mixed $key
     * @return void
     * @throws KeyError
     */
    protected function keyCheck(mixed $key): void
    {
        if ($key === '') {
            throw new KeyError('Empty key given');
        }
    }

    /**
     * Prepare given data for some methods.
     */
    private function prepare(iterable $data): array
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }

        foreach ($data as $i => $dat) {
            if ($dat instanceof self) {
                $data[$i] = $dat->data;
            }
            // Variadic capture.
            elseif ($dat instanceof Traversable) {
                $data[$i] = iterator_to_array($dat);
            }
        }

        return $data;
    }
}

/**
 * XArray initializer, accepts a single iterable or multiple arguments as iterable.
 *
 * Examples:
 *
 * ```
 * $x = xarray(id: 1, name: 'Foo');
 * $x = xarray(['id' => 1, 'name' => 'Foo']);
 * $x = xarray([1, 'Foo']);
 * $x = xarray(1, 'Foo');
 * ```
 *
 * @param  mixed ...$data
 * @return XArray
 */
function xarray(mixed ...$data): XArray
{
    if (is_list($data) && count($data) === 1) {
        $data = is_iterable($data[0]) ? $data[0] : [$data[0]];
    }
    return XArray::from($data);
}

/**
 * XArray initializer via split.
 *
 * @param  string   $pattern
 * @param  string   $string
 * @param  int|null $limit
 * @param  int|null $flags
 * @return XArray
 */
function xsplit(string $pattern, string $string, int $limit = null, int $flags = null): XArray
{
    return XArray::fromSplit($pattern, $string, $limit, $flags);
}

/**
 * XArray initializer via glob.
 *
 * @param  string   $pattern
 * @param  int|null $flags
 * @return XArray
 */
function xglob(string $pattern, int $flags = null): XArray
{
    return XArray::fromGlob($pattern, $flags);
}
