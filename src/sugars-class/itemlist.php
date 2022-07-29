<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\interface\{Arrayable, Jsonable};
use froq\collection\trait\{CountTrait, EmptyTrait};

/**
 * A simple item list class with a list data container & access stuff.
 *
 * @package global
 * @object  ItemList
 * @author  Kerem Güneş
 * @since   6.0
 */
class ItemList implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use CountTrait, EmptyTrait;

    /** @var array */
    private array $data = [];

    /**
     * Constructor.
     *
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        $data && $this->data = array_list([...$data]);
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * Get an item.
     *
     * @param  int $index
     * @return mixed
     */
    public function item(int $index): mixed
    {
        return $this->data[$index] ?? null;
    }

    /**
     * Get all items.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->data;
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
            $this->offsetUnset($this->index($item));
        }

        return $this;
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
        return array_search_key($this->data, $item, $strict, $last);
    }

    /**
     * Get first item.
     *
     * @return mixed
     */
    public function first(): mixed
    {
        return first($this->data);
    }

    /**
     * Get last item.
     *
     * @return mixed
     */
    public function last(): mixed
    {
        return last($this->data);
    }

    /**
     * Sort.
     *
     * @param  callable|null $func
     * @return self
     */
    public function sort(callable $func = null, int $flags = 0): self
    {
        $this->data = sorted($this->data, $func, $flags, assoc: false);

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
        $this->data = filter($this->data, $func, use_keys: $useKeys, keep_keys: false);

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
     * @return self
     */
    public function refine(array $items = null): self
    {
        $this->data = array_refine($this->data, $items);

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
        $this->data = array_dedupe($this->data, $strict);

        return $this;
    }

    /**
     * Select items.
     *
     * @param  int|array  $key
     * @param  mixed|null $default
     * @param  bool       $drop
     * @param  bool       $combine
     * @return mixed
     */
    public function select(int|array $key, mixed $default = null, bool $drop = false, bool $combine = false): mixed
    {
        return array_select($this->data, $key, $default, $drop, $combine);
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
     * @causes     KeyError
     */
    public function offsetSet(mixed $index, mixed $item): void
    {
        $this->indexCheck($index);

        // For calls like `items[] = item`.
        $index ??= $this->count();

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

            throw new KeyError('Invalid index %s for %s', [$indexRepr, $this::class]);
        }
    }
}
