<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\interface\{Arrayable, Listable, Jsonable, Collectable, Iteratable, IteratableReverse};
use froq\collection\trait\{SortTrait, FilterTrait, MapTrait, ReduceTrait, EachTrait, CountTrait, EmptyTrait,
    FindTrait, FirstLastTrait, MinMaxTrait, CalcAverageTrait, CalcProductTrait, CalcSumTrait,
    IteratorTrait, ToArrayTrait, ToListTrait, ToJsonTrait};
use froq\collection\iterator\{ArrayIterator, ReverseArrayIterator};
use froq\collection\{Collection, CollectionInterface};

/**
 * Map/Set trait.
 *
 * A trait used by Map/Set classes.
 *
 * @package froq\util
 * @object  MapSetTrait
 * @author  Kerem Güneş
 * @since   5.35
 * @@internal
 */
trait MapSetTrait
{
    /** Traits. */
    use SortTrait, FilterTrait, MapTrait, ReduceTrait, EachTrait, CountTrait, EmptyTrait,
        FindTrait, FirstLastTrait, MinMaxTrait, CalcAverageTrait, CalcProductTrait, CalcSumTrait,
        IteratorTrait, ToArrayTrait, ToListTrait, ToJsonTrait;

    /** Data holder. */
    protected array $data = [];

    /**
     * Constructor.
     *
     * @param iterable|null $data
     */
    public function __construct(iterable $data = null)
    {
        if ($data) {
            $map = $this instanceof Map;
            foreach ($data as $key => $value) {
                $map ? $this->set($key, $value) : $this->add($value);
            }
        }
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * Get data keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return ($this instanceof Set) ? array_keys($this->data)
             : array_map(fn($k) => strval($k), array_keys($this->data));
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
        return ($this instanceof Set) ? array_entries($this->data)
             : array_map(fn($e) => [strval($e[0]), $e[1]], array_entries($this->data));
    }

    /**
     * Unshift.
     *
     * @param  mixed $value
     * @return self
     */
    public function unshift(mixed $value): self
    {
        if ($this instanceof Set) {
            array_value_exists($value, $this->data)
                || array_unshift($this->data, $value);
        } else {
            array_unshift($this->data, $value);
        }

        return $this;
    }

    /**
     * Push.
     *
     * @param  mixed $value
     * @return self
     */
    public function push(mixed $value): self
    {
        if ($this instanceof Set) {
            array_value_exists($value, $this->data)
                || array_push($this->data, $value);
        } else {
            array_push($this->data, $value);
        }

        return $this;
    }

    /**
     * Shift.
     *
     * @return mixed
     */
    public function shift(): mixed
    {
        return array_shift($this->data);
    }

    /**
     * Pop.
     *
     * @return mixed
     */
    public function pop(): mixed
    {
        return array_pop($this->data);
    }

    /** @alias unshift() */
    public function pushLeft(...$args)
    {
        return $this->unshift(...$args);
    }

    /** @alias shift() */
    public function popLeft()
    {
        return $this->shift();
    }

    /**
     * Check whether data contains given value/values.
     *
     * @param  mixed    $value
     * @param  mixed ...$values
     * @return bool
     */
    public function contains(mixed $value, mixed ...$values): bool
    {
        return array_contains($this->data, $value, ...$values);
    }

    /**
     * Check whether data contains given key/keys.
     *
     * @param  int|string    $key
     * @param  int|string ...$keys
     * @return bool
     */
    public function containsKey(int|string $key, int|string ...$keys): bool
    {
        return array_contains_key($this->data, $key, ...$keys);
    }

    /**
     * Fill tool.
     *
     * @param  int        $length
     * @param  mixed|null $value
     * @return self
     * @since  6.0
     */
    public function fill(int $length, mixed $value = null): self
    {
        $this->data = array_fill(0, $length, $value);

        return $this;
    }

    /**
     * Pad tool.
     *
     * @param  int        $length
     * @param  mixed|null $value
     * @return self
     * @since  6.0
     */
    public function pad(int $length, mixed $value = null): self
    {
        $this->data = array_pad($this->data, $length, $value);

        return $this;
    }

    /**
     * Chunk tool.
     *
     * @param  int $length
     * @return self
     * @since  6.0
     */
    public function chunk(int $length): self
    {
        $this->data = array_chunk($this->data, $length, $this instanceof Map);

        return $this;
    }

    /**
     * Concat tool.
     *
     * @param  mixed    $value
     * @param  mixed ...$values
     * @return self
     * @since  6.0
     */
    public function concat(mixed $value, mixed ...$values): self
    {
        $this->data = array_concat($this->data, $value, ...$values);

        if ($this instanceof Set) {
            $this->data = array_dedupe($this->data);
        }

        return $this;
    }

    /**
     * Slice tool.
     *
     * @param  int      $start
     * @param  int|null $end
     * @return self
     * @since  6.0
     */
    public function slice(int $start, int $end = null): self
    {
        $this->data = array_slice($this->data, $start, $end, $this instanceof Map);

        return $this;
    }

    /**
     * Splice tool.
     *
     * @param  int         $start
     * @param  int|null    $end
     * @param  mixed|null  $replace
     * @param  mixed|null &$replaced
     * @return self
     * @since  6.0
     */
    public function splice(int $start, int $end = null, mixed $replace = null, mixed &$replaced = null): self
    {
        $replaced = array_splice($this->data, $start, $end, $replace);

        return $this;
    }

    /**
     * Split tool.
     *
     * @param  int  $length
     * @param  bool $keepKeys
     * @return self
     * @since  6.0
     */
    public function split(int $length, bool $keepKeys = false): self
    {
        $this->data = array_split($this->data, $length, $keepKeys);

        return $this;
    }

    /**
     * Join tool.
     *
     * @param  string $glue
     * @return string
     * @since  6.0
     */
    public function join(string $glue = ''): string
    {
        return join($glue, $this->data);
    }

    /**
     * Update self data by given data.
     *
     * @param  iterable $data
     * @param  bool     $merge
     * @return self
     * @since  6.0
     */
    public function update(iterable $data, bool $merge = true): self
    {
        foreach ($data as $key => $value) {
            // Handle current iterable fields to keep as original.
            if ($merge && is_iterable($value)) {
                if (is_iterable($current = $this->get($key))) {
                    $value = static::from($current)->update($value);
                    $value = is_array($current) ? $value->array() : $value;
                } unset($current);
            }

            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Clear map/set.
     *
     * @return self
     */
    public function clear(): self
    {
        return $this->empty();
    }

    /**
     * Get map/set size.
     *
     * @return int
     */
    public function size(): int
    {
        return $this->count();
    }

    /**
     * Get map/set data copy as a new static instance.
     *
     * @return static
     */
    public function copy(): static
    {
        return new static($this->data);
    }

    /**
     * Copy map/set data to other map/set.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyTo(self $that): static
    {
        return $that->update($this->data);
    }

    /**
     * Copy map/set data from other map/set data.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyFrom(self $that): static
    {
        return $this->update($that->data);
    }

    /**
     * @inheritDoc froq\common\interface\Collectable
     */
    public function toCollection(): CollectionInterface
    {
        return new Collection($this->data);
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
     * Static constructor from a string & split pattern.
     *
     * @param  string   $string
     * @param  string   $pattern
     * @param  int|null $limit
     * @param  int|null $flags
     * @return static
     * @since  6.0
     */
    public static function fromSplit(string $string, string $pattern, int $limit = null, int $flags = null): static
    {
        return new static(split($pattern, $string, $limit, $flags));
    }
}

/**
 * Map.
 *
 * A map class just like JavaScript's map but "a bit" extended.
 *
 * @package froq\util
 * @object  Map
 * @author  Kerem Güneş
 * @since   5.25
 */
class Map implements Arrayable, Listable, Jsonable, Collectable, Iteratable, IteratableReverse,
    Countable, Iterator, ArrayAccess
{
    use MapSetTrait;

    /** @magic */
    public function __set(int|string|object $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    /** @magic */
    public function __get(int|string|object $key): mixed
    {
        return $this->get($key);
    }

    /** @magic */
    public function __isset(int|string|object $key): bool
    {
        return $this->has($key);
    }

    /** @magic */
    public function __unset(int|string|object $key): void
    {
        $this->remove($key);
    }

    /**
     * Add a value.
     *
     * @param  mixed $value
     * @return self
     */
    public function add(mixed $value): self
    {
        $this->data[] = $value;

        return $this;
    }

    /**
     * Set a key with given value.
     *
     * @param  int|string|object $key
     * @param  mixed             $value
     * @return self
     */
    public function set(int|string|object $key, mixed $value): self
    {
        $this->keyCheck($key);

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Get a key value if key exists.
     *
     * @param  int|string|object $key
     * @param  mixed|null        $default
     * @return mixed
     */
    public function get(int|string|object $key, mixed $default = null): mixed
    {
        $this->keyCheck($key);

        return $this->data[$key] ?? $default;
    }

    /**
     * Remove a key if exists.
     *
     * @param  int|string|object  $key
     * @param  mixed|null        &$value
     * @return bool
     */
    public function remove(int|string|object $key, mixed &$value = null): bool
    {
        $this->keyCheck($key);

        // Clear ref.
        $value = null;

        if ($ok = $this->has($key)) {
            // Fill ref.
            $value = $this->data[$key];

            unset($this->data[$key]);
        }

        return $ok;
    }

    /**
     * Remove a value if exists.
     *
     * @param  mixed        $value
     * @param  string|null &$key
     * @return bool
     */
    public function removeValue(mixed $value, string &$key = null): bool
    {
        if ($ok = $this->hasValue($value, $key)) {
            unset($this->data[$key]);
        }

        return $ok;
    }

    /**
     * Replace a new value if old value exists.
     *
     * @param  mixed        $oldValue
     * @param  mixed        $newValue
     * @param  string|null &$key
     * @return bool
     */
    public function replace(mixed $oldValue, mixed $newValue, string &$key = null): bool
    {
        if ($ok = $this->hasValue($oldValue, $key)) {
            $this->data[$key] = $newValue;
        }

        return $ok;
    }

    /** @alias remove() */
    public function delete(int|string|object $key, mixed &$value = null): bool
    {
        return $this->remove($key, $value);
    }

    /**
     * Run a callback for each item.
     *
     * @param  callable $func
     * @return void
     */
    public function forEach(callable $func): void
    {
        foreach ($this->data as $key => $value) {
            $func($value, (string) $key, $this);
        }
    }

    /**
     * Get key of given value, or return null.
     *
     * @param  mixed $value
     * @return string|null
     * @since  6.0
     */
    public function keyOf(mixed $value): string|null
    {
        return ($key = array_search_key($this->data, $value)) !== null
             ? (string) $key : null;
    }

    /**
     * Get last key of given value, or return null.
     *
     * @param  mixed $value
     * @return string|null
     * @since  6.0
     */
    public function lastKeyOf(mixed $value): string|null
    {
        return ($key = array_search_key($this->data, $value, last: true)) !== null
             ? (string) $key : null;
    }

    /**
     * Check whether a key exists.
     *
     * @param  int|string|object $key
     * @return bool
     */
    public function has(int|string|object $key): bool
    {
        return array_key_exists($this->prepareKey($key), $this->data);
    }

    /**
     * Check whether a value exists.
     *
     * @param  mixed        $value
     * @param  string|null &$key
     * @return bool
     */
    public function hasValue(mixed $value, string &$key = null): bool
    {
        return array_value_exists($value, $this->data, key: $key)
            && ($key = (string) $key) !== null; // Just for string cast.
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $key): bool
    {
        return $this->has($key);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        // For calls like `items[] = item`.
        $key ??= $this->count();

        $this->set($key, $value);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $key): mixed
    {
        return $this->get($key);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $key): void
    {
        $this->remove($key);
    }

    /**
     * Prepare a key casting to string.
     *
     * @param  int|string|object $key
     * @return string
     */
    protected function prepareKey(int|string|object $key): string
    {
        return is_object($key) ? get_object_id($key) : (string) $key;
    }

    /**
     * Check key validity & prepare (as shortcut).
     *
     * @param  mixed &$key
     * @return void
     * @throws KeyError
     */
    protected function keyCheck(mixed &$key, bool $prepare = true): void
    {
        if (is_string($key) && $key == '') {
            throw new KeyError('Empty key given');
        }

        if ($prepare) {
            $key = $this->prepareKey($key);
        }
    }
}

/**
 * Set.
 *
 * A set class just like JavaScript's set but "a bit" extended.
 *
 * @package froq\util
 * @object  Set
 * @author  Kerem Güneş
 * @since   5.25
 */
class Set implements Arrayable, Listable, Jsonable, Collectable, Iteratable, IteratableReverse,
    Countable, Iterator, ArrayAccess
{
    use MapSetTrait;

    /**
     * Add a value.
     *
     * @param  mixed $value
     * @return self
     */
    public function add(mixed $value): self
    {
        if (!$this->has($value)) {
            $this->data[] = $value;
        }

        return $this;
    }

    /**
     * Set an index with given value.
     *
     * @param  int   $index
     * @param  mixed $value
     * @return self
     */
    public function set(int $index, mixed $value): self
    {
        $this->indexCheck($index);

        if (!$this->has($value)) {
            $count = $this->count();

            // Maintain index if exceeds.
            if ($index > $count) {
                $index = $count;
            }

            $this->data[$index] = $value;
        }

        return $this;
    }

    /**
     * Get an index value if index exists.
     *
     * @param  int        $index
     * @param  mixed|null $default
     * @return mixed
     */
    public function get(int $index, mixed $default = null): mixed
    {
        $this->indexCheck($index);

        return $this->data[$index] ?? $default;
    }

    /**
     * Remove a value if exists.
     *
     * @param  mixed     $value
     * @param  int|null &$index
     * @return bool
     */
    public function remove(mixed $value, int &$index = null): bool
    {
        if ($ok = $this->has($value, $index)) {
            $count = $this->count();

            unset($this->data[$index]);

            // Re-index if no last index dropped.
            if ($index != $count - 1) {
                $this->resetIndexes();
            }
        }

        return $ok;
    }

    /**
     * Remove an index if exists.
     *
     * @param  int         $index
     * @param  mixed|null &$value
     * @return bool
     */
    public function removeIndex(int $index, mixed &$value = null): bool
    {
        $this->indexCheck($index);

        // Clear ref.
        $value = null;

        if ($ok = $this->hasIndex($index)) {
            $count = $this->count();

            // Fill ref.
            $value = $this->data[$index];

            unset($this->data[$index]);

            // Re-index if no last index dropped.
            if ($index != $count - 1) {
                $this->resetIndexes();
            }
        }

        return $ok;
    }

    /**
     * Replace a new value if old value exists.
     *
     * @param  mixed     $oldValue
     * @param  mixed     $newValue
     * @param  int|null &$index
     * @return bool
     */
    public function replace(mixed $oldValue, mixed $newValue, int &$index = null): bool
    {
        if ($ok = $this->remove($oldValue, $index)) {
            $this->set($index, $newValue);
        }

        return $ok;
    }

    /** @alias remove() */
    public function delete(mixed $value, int &$index = null): bool
    {
        return $this->remove($value, $index);
    }

    /**
     * Run a callback for each item.
     *
     * @param  callable $func
     * @return void
     */
    public function forEach(callable $func): void
    {
        foreach ($this->data as $index => $value) {
            $func($value, $index, $this);
        }
    }

    /**
     * Get index of given value, or return null.
     *
     * @param  mixed $value
     * @return int|null
     * @since  6.0
     */
    public function indexOf(mixed $value): int|null
    {
        return array_search_key($this->data, $value);
    }

    /**
     * Check whether a value exists.
     *
     * @param  mixed     $value
     * @param  int|null &$index
     * @return bool
     */
    public function has(mixed $value, int &$index = null): bool
    {
        return array_value_exists($value, $this->data, key: $index);
    }

    /**
     * Check whether an index exists.
     *
     * @param  int $index
     * @return bool
     */
    public function hasIndex(int $index): bool
    {
        return array_key_exists($index, $this->data);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $index): bool
    {
        return $this->hasIndex($index);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $index, mixed $value): void
    {
        // For calls like `items[] = item`.
        $index ??= $this->count();

        $this->set($index, $value);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $index): mixed
    {
        return $this->get($index);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $index): void
    {
        $this->removeIndex($index);
    }

    /**
     * Reset indexes for modifications.
     *
     * @return void
     */
    protected function resetIndexes(): void
    {
        $this->data = $this->values();
    }

    /**
     * Kind of an event callback for after sort, filter, map actions.
     *
     * @return void
     */
    protected function onDataChange(): void
    {
        $this->resetIndexes();
    }

    /**
     * Check index validity.
     *
     * @param  mixed $index
     * @return void
     * @throws KeyError
     */
    protected function indexCheck(mixed $index): void
    {
        if (!is_int($index) || $index < 0) {
            throw new KeyError('Index must be int & greater than -1');
        }
    }
}

/**
 * Dict.
 *
 * A dictionary class just like Python's dict but "a bit" extended.
 *
 * @package froq\util
 * @object  Dict
 * @author  Kerem Güneş
 * @since   5.31
 */
class Dict extends Map
{
    /**
     * Push an item by given key/value, dropping old key if exists.
     *
     * @param  int|string|object $key
     * @param  mixed             $value
     * @return self
     */
    public function pushKey(int|string|object $key, mixed $value): self
    {
        $this->data = array_push_key($this->data, $key, $value);

        return $this;
    }

    /**
     * Pop an item by given key if exists.
     *
     * @param  int|string|object $key
     * @param  mixed|null        $default
     * @return mixed|null
     */
    public function popKey(int|string|object $key, mixed $default = null): mixed
    {
        $value = array_pop_key($this->data, $key, $default);

        return $value;
    }

    /**
     * Pop last item as an entry.
     *
     * @return array|null
     */
    public function popItem(): array|null
    {
        $item = array_pop_entry($this->data);
        $item && ($item[0] = (string) $item[0]);

        return $item;
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
}
