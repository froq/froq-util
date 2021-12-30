<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\collection\{Collection, CollectionInterface};
use froq\collection\iterator\{ArrayIterator, ReverseArrayIterator};
use froq\collection\trait\{EachTrait, SortTrait, FilterTrait, MapTrait, ReduceTrait, FindTrait,
    MinMaxTrait, FirstLastTrait};
use froq\common\interface\{Arrayable, Jsonable, Listable, Collectable, Iteratable, IteratableReverse};
use froq\common\trait\{DataCountTrait, DataEmptyTrait, DataIteratorTrait, DataToArrayTrait, DataToJsonTrait};

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
class Map implements Iterator, ArrayAccess, Countable, Arrayable, Jsonable, Listable, Collectable,
    Iteratable, IteratableReverse
{
    /** Traits. */
    use EachTrait, SortTrait, FilterTrait, MapTrait, ReduceTrait, FindTrait, MinMaxTrait, FirstLastTrait,
        DataCountTrait, DataEmptyTrait, DataIteratorTrait, DataToArrayTrait, DataToJsonTrait;

    /** Data holder. */
    protected array $data = [];

    /**
     * Constructor.
     *
     * @param iterable|int|null $data
     * @param int|null          $size
     */
    public function __construct(iterable|int $data = null, int $size = null)
    {
        if ($data) {
            if (is_iterable($data)) {
                foreach ($data as $key => $value) {
                    $this->set($key, $value);
                }
            } elseif (is_int($data)) {
                $size = $data;
            }
        }

        // When size given, override.
        $size && ($this->data = array_fill(0, $size, null));
    }

    /** @magic __debugInfo() */
    public function __debugInfo(): array
    {
        return $this->data;
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
        $key = $this->prepareKey($key);

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
        $key = $this->prepareKey($key);

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
        $key = $this->prepareKey($key);

        if ($this->has($key)) {
            // Assign value ref.
            $value = $this->data[$key];

            unset($this->data[$key]);

            return true;
        }

        return false;
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
        $key = $this->prepareKey($key);

        if ($this->hasValue($value, $key)) {
            unset($this->data[$key]);

            return true;
        }

        return false;
    }

    /**
     * Replace a new value if old value exists.
     *
     * @param  mixed        $oldValue
     * @param  mixed        $newValue
     * @param  string|null &$key
     * @return bool
     */
    public function replace(mixed $oldValue, mixed $newValue, int &$key = null): bool
    {
        if ($this->removeValue($oldValue, $key)) {
            $this->set($key, $newValue);

            return true;
        }

        return false;
    }

    /** @aliasOf remove() */
    public function delete(int|string|object $key, mixed &$value = null): bool
    {
        return $this->remove($key, $value);
    }

    /**
     * Get keys.
     *
     * @return array.
     */
    public function keys(): array
    {
        return array_map(fn($k) => strval($k), array_keys($this->data));
    }

    /**
     * Get values.
     *
     * @return array.
     */
    public function values(): array
    {
        return array_values($this->data);
    }

    /**
     * Get entries.
     *
     * @return array.
     */
    public function entries(): array
    {
        return array_map(fn($e) => [strval($e[0]), $e[1]], array_entries($this->data));
    }

    /**
     * Clear map.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->empty();
    }

    /**
     * Get map size.
     *
     * @return int
     */
    public function size(): int
    {
        return $this->count();
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
            $func($value, strval($key), $this);
        }
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
            && ($key = $this->prepareKey($key)) !== null; // Just for string cast.
    }

    /**
     * Get map data copy as a new static instance.
     *
     * @return static
     */
    public function copy(): static
    {
        return new static($this->data);
    }

    /**
     * Copy map data to other map.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyTo(self $that): static
    {
        foreach ($this->data as $key => $value) {
            $that->set($key, $value);
        }

        return $that;
    }

    /**
     * Copy map data from other map data.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyFrom(self $that): static
    {
        foreach ($that->data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /** @inheritDoc froq\common\interface\Listable */
    public function toList(): array
    {
        return $this->values();
    }

    /** @inheritDoc froq\common\interface\Collectable */
    public function toCollection(): CollectionInterface
    {
        return new Collection($this->data);
    }

    /** @inheritDoc Iteratable */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->data);
    }

    /** @inheritDoc IteratableReverse */
    public function getReverseIterator(): iterable
    {
        return new ReverseArrayIterator($this->data);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetExists(mixed $key): bool
    {
        return $this->has($key);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetGet(mixed $key): mixed
    {
        return $this->get($key);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetUnset(mixed $key): void
    {
        $this->remove($key);
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
     * Static constructor.
     *
     * @param  mixed ...$items
     * @return static
     */
    public static function of(mixed ...$items): static
    {
        return new static($items);
    }

    /**
     * Prepare a key casting to string.
     *
     * @param  int|string|object $key
     * @return string
     * @throws KeyError
     */
    protected function prepareKey(int|string|object $key): string
    {
        // Dict has own prepareKey() method.
        if ($this instanceof Dict) {
            return $key;
        }

        if (is_string($key) && $key == '') {
            throw new KeyError('Empty key given');
        }

        return is_object($key) ? get_object_id($key) : strval($key);
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
class Set implements Iterator, ArrayAccess, Countable, Arrayable, Jsonable, Listable, Collectable,
    Iteratable, IteratableReverse
{
    /** Traits. */
    use EachTrait, SortTrait, FilterTrait, MapTrait, ReduceTrait, FindTrait, MinMaxTrait, FirstLastTrait,
        DataCountTrait, DataEmptyTrait, DataIteratorTrait, DataToArrayTrait, DataToJsonTrait;

    /** Data holder. */
    protected array $data = [];

    /**
     * Constructor.
     *
     * @param iterable|int|null $data
     * @param int|null          $size
     */
    public function __construct(iterable|int $data = null, int $size = null)
    {
        if ($data) {
            if (is_iterable($data)) {
                foreach ($data as $value) {
                    $this->add($value);
                }
            } elseif (is_int($data)) {
                $size = $data;
            }
        }

        // When size given, override.
        $size && ($this->data = array_fill(0, $size, null));
    }

    /** @magic __debugInfo() */
    public function __debugInfo(): array
    {
        return $this->data;
    }

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
        $this->checkIndex($index);

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
        $this->checkIndex($index);

        return $this->hasIndex($index) ? $this->data[$index] : $default;
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
        if ($this->has($value, $index)) {
            $count = $this->count();

            unset($this->data[$index]);

            // Re-index if no last index dropped.
            if ($index != $count - 1) {
                $this->resetIndexes();
            }

            return true;
        }

        return false;
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
        $this->checkIndex($index);

        if ($this->hasIndex($index)) {
            $count = $this->count();

            unset($this->data[$index]);

            // Re-index if no last index dropped.
            if ($index != $count - 1) {
                $this->resetIndexes();
            }

            return true;
        }

        return false;
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
        if ($this->remove($oldValue, $index)) {
            $this->set($index, $newValue);

            return true;
        }

        return false;
    }

    /** @aliasOf remove() */
    public function delete(mixed $value, int &$index = null): bool
    {
        return $this->remove($value, $index);
    }

    /**
     * Get keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Get values.
     *
     * @return array
     */
    public function values(): array
    {
        return array_values($this->data);
    }

    /**
     * Get entries.
     *
     * @return array
     */
    public function entries(): array
    {
        return array_entries($this->data);
    }

    /**
     * Clear set.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->empty();
    }

    /**
     * Get set size.
     *
     * @return int
     */
    public function size(): int
    {
        return $this->count();
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
     * Get set data copy as a new static instance.
     *
     * @return static
     */
    public function copy(): static
    {
        return new static($this->data);
    }

    /**
     * Copy map data to other map.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyTo(self $that): static
    {
        foreach ($this->data as $key => $value) {
            $that->set($key, $value);
        }

        return $that;
    }

    /**
     * Copy map data from other map data.
     *
     * @param  self (static) $that
     * @return static
     */
    public function copyFrom(self $that): static
    {
        foreach ($that->data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /** @inheritDoc froq\common\interface\Listable */
    public function toList(): array
    {
        return $this->values();
    }

    /** @inheritDoc froq\common\interface\Collectable */
    public function toCollection(): CollectionInterface
    {
        return new Collection($this->data);
    }

    /** @inheritDoc Iteratable */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->data);
    }

    /** @inheritDoc IteratableReverse */
    public function getReverseIterator(): iterable
    {
        return new ReverseArrayIterator($this->data);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetExists(mixed $index): bool
    {
        return $this->hasIndex($index);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetSet(mixed $index, mixed $value): void
    {
        // For calls like `items[] = item`.
        $index ??= $this->count();

        $this->set($index, $value);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetGet(mixed $index): mixed
    {
        return $this->get($index);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetUnset(mixed $index): void
    {
        $this->removeIndex($index);
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
     * Static constructor.
     *
     * @param  mixed ...$items
     * @return static
     */
    public static function of(mixed ...$items): static
    {
        return new static($items);
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
    private function checkIndex(mixed $index): void
    {
        if (!is_int($index) || $index < 0) {
            throw new KeyError('Index must be int & greater than -1');
        }
    }
}

/**
 * Dict.
 *
 * A Dict class just like Python's map but "a bit" extended. Also using Map structure,
 * it overrides some methods to accept null keys for flexibility.
 *
 * @package froq\util
 * @object  Dict
 * @author  Kerem Güneş
 * @since   5.31
 */
class Dict extends Map
{
    /**
     * Push an item by given value.
     *
     * @param  mixed $value
     * @return self
     */
    public function push(mixed $value): self
    {
        $this->offsetSet(null, $value);

        return $this;
    }

    /**
     * Push an item by given key/value, dropping old key if exists.
     *
     * @param  int|string|object $key
     * @param  mixed             $value
     * @return self
     */
    public function pushKey(int|string|object $key, mixed $value): self
    {
        array_push_entry($this->data, $this->prepareKey($key), $value);

        return $this;
    }

    /**
     * Pop last item.
     *
     * @return mixed|null
     */
    public function pop(): mixed
    {
        if ($entry = $this->popItem()) {
            return $entry[1];
        }
        return null;
    }

    /**
     * Pop an item by given key if exists.
     *
     * @param  int|string|object $key
     * @return mixed|null
     */
    public function popKey(int|string|object $key): mixed
    {
        if ($this->remove($key, $value)) {
            return $value;
        }
        return null;
    }

    /**
     * Pop last item as an entry.
     *
     * @return array|null
     */
    public function popItem(): array|null
    {
        return array_pop_entry($this->data);
    }

    /**
     * Static constructor with given keys (and value optionally).
     *
     * @param  array      $keys
     * @param  mixed|null $value
     * @return static
     */
    public static function fromKeys(array $keys, mixed $value = null): static
    {
        return new static(array_fill_keys($keys, $value));
    }

    /** @override */
    public function forEach(callable $func): void
    {
        foreach ($this->data as $key => $value) {
            $func($value, $key, $this);
        }
    }

    /** @override */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /** @override */
    public function entries(): array
    {
        return array_entries($this->data);
    }

    /** @override */
    public function hasValue(mixed $value, int|string &$key = null): bool
    {
        return array_value_exists($value, $this->data, key: $key);
    }

    /** @override */
    public function offsetSet(mixed $key, mixed $value): void
    {
        $key = $this->prepareKey($key);

        $this->set($key, $value);
    }

    /** @override */
    protected function prepareKey(int|string|object|null $key): string
    {
        if (is_string($key) && $key == '') {
            throw new KeyError('Empty key given');
        }

        // Get next key for "dict[] = .." calls.
        if (is_null($key)) {
            return (string) $this->count();
        }

        return is_object($key) ? get_object_id($key) : strval($key);
    }
}

/** Functions. */
function xmap(...$args): Map { return new Map(...$args); }
function xset(...$args): Set { return new Set(...$args); }
function xdict(...$args): Dict { return new Dict(...$args); }
