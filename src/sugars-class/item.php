<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Arrayable, Jsonable};
use froq\collection\trait\{CountTrait, EmptyTrait};

/**
 * A simple item class with a key/value pair data container & access stuff.
 *
 * @package global
 * @class   Item
 * @author  Kerem Güneş
 * @since   6.0
 */
class Item implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use CountTrait, EmptyTrait;

    /** Data. */
    private array $data = [];

    /**
     * Constructor.
     *
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        $data && $this->data = [...$data];
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
    public function __get(int|string $key): mixed
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
    public function get(int|string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
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
     * Sort.
     *
     * @param  callable|int|null $func
     * @param  int               $flags
     * @return self
     */
    public function sort(callable|int $func = null, int $flags = 0): self
    {
        $this->data = sorted($this->data, $func, $flags, assoc: true);

        return $this;
    }

    /**
     * Call given function for each item.
     *
     * @param  callable $func
     * @return self
     */
    public function each(callable $func): self
    {
        each($this->data, $func);

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
        $this->data = filter($this->data, $func, use_keys: $useKeys);

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
        $this->data = map($this->data, $func, use_keys: $useKeys);

        return $this;
    }

    /**
     * Reduce.
     *
     * @param  mixed    $carry
     * @param  callable $func
     * @return mixed
     */
    public function reduce(mixed $carry, callable $func): mixed
    {
        return array_reduce($this->data, $func, $carry);
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
     * @param  bool|null  $list
     * @return self
     */
    public function refine(array $items = null, bool $list = null): self
    {
        $this->data = array_refine($this->data, $items, $list);

        return $this;
    }

    /**
     * Dedupe items applying unique check.
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
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc froq\common\interface\Jsonable
     */
    public function toJson(int $flags = 0): string
    {
        return (string) json_encode($this->data, $flags);
    }

    /**
     * @inheritDoc IteratorAggregate
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
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
    public function offsetGet(mixed $key, mixed $default = null): mixed
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
}
