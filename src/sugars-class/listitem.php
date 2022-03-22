<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A simple item list.
 *
 * @package froq\util
 * @object  Items
 * @author  Kerem Güneş
 * @since   6.0
 */
class ItemList implements Countable, IteratorAggregate, ArrayAccess
{
    /** @var array */
    protected array $items = [];

    /** @var bool */
    protected bool $locked;

    /**
     * Constructor.
     *
     * @param iterable $items
     * @param bool     $locked
     */
    public function __construct(iterable $items, bool $locked = false)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }

        $this->locked = $locked;
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return ['locked' => $this->locked] + $this->items;
    }

    /**
     * Get an item.
     *
     * @param  int $index
     * @return mixed
     */
    public function item(int $index): mixed
    {
        return $this->items[$index] ?? null;
    }

    /**
     * Get all items.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Get locked state.
     *
     * @return bool
     */
    public function locked(): bool
    {
        return $this->locked;
    }

    /**
     * Add an item.
     *
     * @param  mixed $item
     * @return self
     */
    public function add(mixed $item): self
    {
        return $this->offsetSet(null, $item);
    }

    /**
     * Get index of given item.
     *
     * @param  mixed $item
     * @param  bool  $strict
     * @param  bool  $last
     * @return int|null
     */
    public function index(mixed $item, bool $strict = true, bool $last = false): int|null
    {
        return array_search_key($this->items, $item, $strict, $last);
    }

    /**
     * Get first item.
     *
     * @return mixed
     */
    public function first(): mixed
    {
        return first($this->items);
    }

    /**
     * Get last item.
     *
     * @return mixed
     */
    public function last(): mixed
    {
        return last($this->items);
    }

    /**
     * Call given function for each item.
     *
     * @param  callable $func
     * @return self
     */
    public function each(callable $func): self
    {
        each($this->items, $func);

        return $this;
    }

    /**
     * Sort items.
     *
     * @param  callable|null $func
     * @return self
     */
    public function sort(callable $func = null, int $flags = 0): self
    {
        $this->items = sorted($this->items, $func, $flags, assoc: false);

        return $this;
    }

    /**
     * Filter items.
     *
     * @param  callable|null $func
     * @return self
     */
    public function filter(callable $func = null): self
    {
        $this->items = array_filter_list($this->items, $func);

        return $this;
    }

    /**
     * Map items.
     *
     * @param  callable $func
     * @return self
     */
    public function map(callable $func): self
    {
        $this->items = array_map($func, $this->items);

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
        return array_reduce($this->items, $func, $carry);
    }

    /**
     * Reverse items.
     *
     * @return self
     */
    public function reverse(): self
    {
        $this->items = array_reverse($this->items);

        return $this;
    }

    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @inheritDoc IteratorAggregate
     */ #[\ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $index): bool
    {
        $this->indexCheck($index);

        return array_key_exists($index, $this->items);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $index, mixed $default = null): mixed
    {
        $this->indexCheck($index);

        return array_get($this->items, $index, $default);
    }

    /**
     * @inheritDoc ArrayAccess
     */ #[\ReturnTypeWillChange]
    public function offsetSet(mixed $index, mixed $item): self
    {
        $this->locked && throw new ReadonlyError($this);

        $index ??= $this->count();
        $this->indexCheck($index);

        array_splice($this->items, $index, 1, $item);

        return $this;
    }

    /**
     * @inheritDoc ArrayAccess
     */ #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $index): self
    {
        $this->locked && throw new ReadonlyError($this);

        $this->indexCheck($index);

        array_splice($this->items, $index, 1);

        return $this;
    }

    /**
     * Check index validity.
     *
     * @param  mixed $index
     * @return void
     * @throws KeyError
     */
    private function indexCheck(mixed $index): void
    {
        if (!is_int($index) || $index < 0) {
            throw new KeyError('Index must be int & greater than -1');
        }
    }
}
