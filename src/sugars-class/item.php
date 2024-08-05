<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Arrayable, Jsonable};
use froq\collection\trait\{CountTrait, EmptyTrait, EachTrait, KeysValuesTrait, FirstLastTrait,
    ToArrayTrait, ToJsonTrait};

/**
 * A simple item class with a key/value data container & access stuff.
 *
 * @package global
 * @class   Item
 * @author  Kerem Güneş
 * @since   6.0
 */
class Item implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use CountTrait, EmptyTrait, EachTrait, KeysValuesTrait, FirstLastTrait, ToArrayTrait, ToJsonTrait;

    /** Data map. */
    private array $data = [];

    /**
     * Constructor.
     *
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        $this->data = iterator_to_array($data);
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * @magic
     */
    public function __isset(int|string $key): bool
    {
        return $this->has($key);
    }

    /**
     * @magic
     */
    public function __set(int|string $key, mixed $item): void
    {
        $this->set($key, $item);
    }

    /**
     * @magic
     */
    public function &__get(int|string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * @magic
     */
    public function __unset(int|string $key): void
    {
        $this->remove($key);
    }

    /**
     * Check an item.
     *
     * @param  int|string $key
     * @return bool
     */
    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Set an item.
     *
     * @param  int|string $key
     * @param  mixed      $item
     * @return self
     */
    public function set(int|string $key, mixed $item): self
    {
        $this->data[$key] = $item;

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
     * Update items.
     *
     * @param  iterable $data
     * @return self
     */
    public function update(iterable $data): self
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Get key of given item if found.
     *
     * @param  mixed $item
     * @param  bool  $strict
     * @param  bool  $last
     * @return int|string|null
     */
    public function key(mixed $item, bool $strict = true, bool $last = false): int|string|null
    {
        return array_search_key($this->data, $item, $strict, $last);
    }

    /**
     * Get value of given key if found.
     *
     * @param  int|string $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function value(int|string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Sort.
     *
     * @param  callable|int|null $func
     * @param  int               $flags
     * @param  bool              $key
     * @return self
     */
    public function sort(callable|int $func = null, int $flags = 0, bool $key = false): self
    {
        $this->data = sorted($this->data, $func, $flags, true, $key);

        return $this;
    }

    /**
     * Filter.
     *
     * @param  callable|null $func
     * @param  bool          $useKeys
     * @return self
     */
    public function filter(callable $func = null, bool $useKeys = false): self
    {
        $this->data = filter($this->data, $func, false, $useKeys);

        return $this;
    }

    /**
     * Map.
     *
     * @param  callable $func
     * @param  bool     $useKeys
     * @return self
     */
    public function map(callable $func, bool $useKeys = false): self
    {
        $this->data = map($this->data, $func, false, $useKeys);

        return $this;
    }

    /**
     * Reduce.
     *
     * @param  mixed    $carry
     * @param  callable $func
     * @param  bool     $right
     * @return mixed
     */
    public function reduce(mixed $carry, callable $func, bool $right = false): mixed
    {
        return reduce($this->data, $carry, $func, $right);
    }

    /**
     * Aggregate.
     *
     * @param  callable   $func
     * @param  array|null $carry
     * @return array
     */
    public function aggregate(callable $func, array $carry = null): array
    {
        return aggregate($this->data, $func, $carry);
    }

    /**
     * Reverse.
     *
     * @return self
     */
    public function reverse(): self
    {
        $this->data = array_reverse($this->data);

        return $this;
    }

    /**
     * Refine filtering given or null, "" and [] items as default.
     *
     * @param  array|null $items
     * @return self
     */
    public function refine(array $items = null): self
    {
        $this->data = array_refine($this->data, $items, false);

        return $this;
    }

    /**
     * Dedupe items applying unique check.
     *
     * @param  bool $strict
     * @return self
     */
    public function dedupe(bool $strict = true): self
    {
        $this->data = array_dedupe($this->data, $strict, false);

        return $this;
    }

    /**
     * Select items.
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
     * @inheritDoc IteratorAggregate
     * @permissive
     */
    public function getIterator(): Iter|Traversable
    {
        return new Iter($this->data);
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
    public function offsetSet(mixed $key, mixed $item): void
    {
        $this->set($key, $item);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function &offsetGet(mixed $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $key): void
    {
        $this->remove($key);
    }

    /**
     * Static constructor.
     *
     * @param  mixed ...$data Map of named arguments.
     * @return static
     */
    public static function of(mixed ...$data): static
    {
        return new static($data);
    }
}

/**
 * A simple item list class with a list data container & access stuff.
 *
 * @package global
 * @class   ItemList
 * @author  Kerem Güneş
 * @since   6.0
 */
class ItemList implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use CountTrait, EmptyTrait, EachTrait, KeysValuesTrait, FirstLastTrait, ToArrayTrait, ToJsonTrait;

    /** Items list. */
    private array $data = [];

    /** Items type to check. */
    private string|null $type;

    /**
     * Constructor.
     *
     * @param iterable                  $data
     * @param string|array<string>|null $type
     */
    public function __construct(iterable $data = [], string|array $type = null)
    {
        $this->type = is_array($type) ? join('|', $type) : $type;

        $data && $this->add(...$data);
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * Get an item.
     *
     * @param  int|callable $offset
     * @return mixed
     */
    public function item(int|callable $offset): mixed
    {
        if (is_callable($offset)) {
            return array_find($this->data, $offset);
        }

        return $this->data[$offset] ?? null;
    }

    /**
     * Get all items.
     *
     * @param  array|callable|null $offsets
     * @return array
     */
    public function items(array|callable $offsets = null): array
    {
        if (is_array($offsets)) {
            return array_select($this->data, $offsets);
        }
        if (is_callable($offsets)) {
            return array_find_all($this->data, $offsets);
        }

        return $this->data;
    }

    /**
     * Check an item.
     *
     * @param  mixed $item
     * @return bool
     */
    public function has(mixed $item): bool
    {
        return $this->index($item) !== null;
    }

    /**
     * Add items.
     *
     * @param  mixed ...$items
     * @return self
     */
    public function add(mixed ...$items): self
    {
        foreach ($items as $item) {
            $this->offsetSet(null, $item);
        }

        return $this;
    }

    /**
     * Drop items.
     *
     * @param  mixed ...$items
     * @return self
     */
    public function drop(mixed ...$items): self
    {
        foreach ($items as $item) {
            // Clear all indexes with given value.
            while (null !== ($index = $this->index($item))) {
                $this->offsetUnset($index);
            }
        }

        return $this;
    }

    /**
     * Get index of given item if found.
     *
     * @param  mixed $item
     * @param  bool  $strict
     * @param  bool  $last
     * @return int|null
     */
    public function index(mixed $item, bool $strict = true, bool $last = false): int|null
    {
        return array_search_key($this->data, $item, $strict, $last);
    }

    /**
     * Get value of given index if found.
     *
     * @param  int        $index
     * @param  mixed|null $default
     * @return mixed
     */
    public function value(int $index, mixed $default = null): mixed
    {
        return $this->data[$index] ?? $default;
    }

    /**
     * Sort.
     *
     * @param  callable|int|null $func
     * @param  int               $flags
     * @param  bool              $key
     * @return self
     */
    public function sort(callable|int $func = null, int $flags = 0, bool $key = false): self
    {
        $this->data = sorted($this->data, $func, $flags, false, $key);

        return $this;
    }

    /**
     * Filter.
     *
     * @param  callable|null $func
     * @param  bool          $useKeys
     * @return self
     */
    public function filter(callable $func = null, bool $useKeys = false): self
    {
        $this->data = filter($this->data, $func, false, $useKeys, false);

        return $this;
    }

    /**
     * Map.
     *
     * @param  callable $func
     * @param  bool     $useKeys
     * @return self
     */
    public function map(callable $func, bool $useKeys = false): self
    {
        $this->data = map($this->data, $func, false, $useKeys);

        return $this;
    }

    /**
     * Reduce.
     *
     * @param  mixed    $carry
     * @param  callable $func
     * @param  bool     $right
     * @return mixed
     */
    public function reduce(mixed $carry, callable $func, bool $right = false): mixed
    {
        return reduce($this->data, $carry, $func, $right);
    }

    /**
     * Aggregate.
     *
     * @param  callable   $func
     * @param  array|null $carry
     * @return array
     */
    public function aggregate(callable $func, array $carry = null): array
    {
        return aggregate($this->data, $func, $carry);
    }

    /**
     * Reverse.
     *
     * @return self
     */
    public function reverse(): self
    {
        $this->data = array_reverse($this->data);

        return $this;
    }

    /**
     * Refine filtering given or null, "" and [] items as default.
     *
     * @param  array|null $items
     * @return self
     */
    public function refine(array $items = null): self
    {
        $this->data = array_refine($this->data, $items, true);

        return $this;
    }

    /**
     * Dedupe items applying unique check.
     *
     * @param  bool $strict
     * @return self
     */
    public function dedupe(bool $strict = true): self
    {
        $this->data = array_dedupe($this->data, $strict, true);

        return $this;
    }

    /**
     * Slice.
     *
     * @param  int      $start
     * @param  int|null $end
     * @return self
     */
    public function slice(int $start, int $end = null): self
    {
        $this->data = array_slice($this->data, $start, $end);

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
     * @param  int $length
     * @return self
     */
    public function split(int $length): self
    {
        $this->data = array_split($this->data, $length, $keepKeys);

        return $this;
    }

    /**
     * Select.
     *
     * @param  int|array  $key
     * @param  mixed|null $default
     * @param  bool       $combine
     * @return mixed
     */
    public function select(int|array $key, mixed $default = null, bool $combine = false): mixed
    {
        return array_select($this->data, $key, $default, $combine);
    }

    /**
     * @inheritDoc IteratorAggregate
     * @permissive
     */
    public function getIterator(): Iter|Traversable
    {
        return new Iter($this->data);
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError
     */
    public function offsetExists(mixed $index): bool
    {
        $this->indexCheck($index);

        return array_key_exists($index, $this->data);
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError
     */
    public function offsetGet(mixed $index, mixed $default = null): mixed
    {
        $this->indexCheck($index);

        return $this->data[$index] ?? $default;
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError|TypeError
     */
    public function offsetSet(mixed $index, mixed $item): void
    {
        $this->indexCheck($index);
        $this->typeCheck($item);

        // For calls like `items[] = item`.
        $index ??= count($this->data);

        // Splice, because it resets indexes.
        array_splice($this->data, $index, 1, [$item]);
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError
     */
    public function offsetUnset(mixed $index): void
    {
        $this->indexCheck($index);

        // In case..
        $index ??= PHP_INT_MAX;

        // Splice, because it resets indexes.
        array_splice($this->data, $index, 1);
    }

    /**
     * Static constructor.
     *
     * @param  mixed ...$data List of items.
     * @return static
     */
    public static function of(mixed ...$data): static
    {
        return new static($data);
    }

    /**
     * Check index validity (if index is not null).
     *
     * @throws KeyError
     */
    private function indexCheck(mixed $index): void
    {
        if ($index !== null && (!is_int($index) || $index < 0)) {
            $indexRepr = match ($type = get_type($index)) {
                'int'    => "int($index)",
                'float'  => "float($index)",
                'string' => "string('$index')",
                default  => $type,
            };

            throw new KeyError(sprintf(
                'Invalid index %s for %s',
                $indexRepr, get_class_name($this, escape: true)
            ));
        }
    }

    /**
     * Check type validity (if self type is not null).
     *
     * @throws TypeError
     */
    private function typeCheck(mixed $item): void
    {
        if ($this->type !== null && !is_type_of($item, $this->type)) {
            throw new TypeError(sprintf(
                'Invalid type %s for %s accepting only type of %s',
                get_type($item), get_class_name($this, escape: true), $this->type
            ));
        }
    }
}
