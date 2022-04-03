<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\interface\{Arrayable, Jsonable};
use froq\collection\trait\{CountTrait, EmptyTrait};

/**
 * A simple item class with key/value paire data container & access stuff.
 *
 * @package froq\util
 * @object  Item
 * @author  Kerem Güneş
 * @since   6.0
 */
class Item implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use CountTrait, EmptyTrait;

    /** @var array */
    protected array $data = [];

    /**
     * Constructor.
     *
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        $this->data = [...$data];
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /** @magic */
    public function __isset(int|string $key): bool
    {
        return $this->has($key);
    }

    /** @magic */
    public function __set(int|string $key, mixed $item): void
    {
        $this->set($key, $item);
    }

    /** @magic */
    public function __get(int|string $key): mixed
    {
        return $this->get($key);
    }

    /** @magic */
    public function __unset(int|string $key): void
    {
        $this->remove($key);
    }

    /**
     * Check an item.
     *
     * @param  string $key
     * @return bool
     */
    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Set an item.
     *
     * @param  string $key
     * @param  mixed  $item
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
     * @param  string     $key
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
     * @param  string $key
     * @return self
     */
    public function remove(int|string $key): self
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Get key of given item.
     *
     * @param  mixed $item
     * @param  bool  $strict
     * @param  bool  $last
     * @return int|null
     */
    public function key(mixed $item, bool $strict = true, bool $last = false): int|null
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
     * @param  callable|null $func
     * @return self
     */
    public function sort(callable $func = null, int $flags = 0): self
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
     * Refine data using given items or null, '', [] items as default.
     *
     * @param  array $items
     * @return self
     */
    public function refine(array $items = [null, '', []]): self
    {
        $this->data = array_clear($this->data, $items, keep_keys: true);

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
        $this->data = array_dedupe($this->data, $strict, list: false);

        return $this;
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
    public function offsetGet(mixed $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
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
    public function offsetUnset(mixed $key): void
    {
        $this->remove($key);
    }
}
