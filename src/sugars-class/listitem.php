<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\interface\{Arrayable, Jsonable};
use froq\collection\trait\{CountTrait, EmptyTrait};

/**
 * A simple item list.
 *
 * @package froq\util
 * @object  ItemList
 * @author  Kerem Güneş
 * @since   6.0
 */
class ItemList implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use CountTrait, EmptyTrait;

    /** @var array */
    private array $data = [];

    /** @var string|null */
    private string|null $type;

    /** @var bool */
    private bool $locked = false;


    /**
     * Constructor.
     *
     * @param iterable    $data
     * @param string|null $type
     * @param bool        $locked
     */
    public function __construct(iterable $data = [], string $type = null, bool $locked = false)
    {
        // Set type before.
        $this->type = $type;

        foreach ($data as $item) {
            $this->add($item);
        }

        // Set locked after.
        $this->locked = $locked;
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return ['type' => $this->type, 'locked' => $this->locked] + $this->data;
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
     * Get type info.
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
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
     * Filter.
     *
     * @param  callable|null $func
     * @return self
     */
    public function filter(callable $func = null): self
    {
        $this->data = array_filter_list($this->data, $func);

        return $this;
    }

    /**
     * Map.
     *
     * @param  callable $func
     * @return self
     */
    public function map(callable $func): self
    {
        $this->data = array_map($func, $this->data);

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
     */ #[\ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError
     */
    public final function offsetExists(mixed $index): bool
    {
        $this->indexCheck($index);

        return array_key_exists($index, $this->data);
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError
     */
    public final function offsetGet(mixed $index, mixed $default = null): mixed
    {
        $this->indexCheck($index);

        return array_get($this->data, $index, $default);
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError|TypeError
     * @throws     ReadonlyError
     */ #[\ReturnTypeWillChange]
    public final function offsetSet(mixed $index, mixed $item): self
    {
        $this->locked && throw new ReadonlyError($this);

        $this->indexCheck($index); $this->typeCheck($item);

        $index ??= $this->count();

        // Splice, because it resets indexes.
        array_splice($this->data, $index, 1, [$item]);

        return $this;
    }

    /**
     * @inheritDoc ArrayAccess
     * @causes     KeyError
     * @throws     ReadonlyError
     */ #[\ReturnTypeWillChange]
    public final function offsetUnset(mixed $index): self
    {
        $this->locked && throw new ReadonlyError($this);

        $this->indexCheck($index);

        // Splice, because it resets indexes.
        array_splice($this->data, $index, 1);

        return $this;
    }

    /**
     * Check index validity (if index is not null).
     *
     * @throws KeyError
     */
    private function indexCheck(mixed $index): void
    {
        if ($index !== null && (!is_int($index) || $index < 0)) {
            throw new KeyError('Index must be int, greater than -1');
        }
    }

    /**
     * Check type validity (if type is not empty).
     *
     * @throws TypeError
     */
    private function typeCheck(mixed $item): void
    {
        if ($this->type && !is_type_of($item, $this->type)) {
            throw new TypeError(sprintf('Item type must be %s, %s given',
                $this->type, get_type($item)));
        }
    }
}
