<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\interface\{Arrayable, Jsonable};
use froq\collection\trait\{SortTrait, FilterTrait, MapTrait, ReduceTrait,
    EachTrait, CountTrait, EmptyTrait, ToArrayTrait, ToJsonTrait};

/**
 *  Simple List.
 *
 * A class for playing with lists in OOP-way.
 *
 * @package froq\util
 * @object  SimpleList
 * @author  Kerem Güneş
 * @since   6.0
 */
class SimpleList implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use SortTrait, FilterTrait, MapTrait, ReduceTrait,
        EachTrait, CountTrait, EmptyTrait, ToArrayTrait, ToJsonTrait;

    /** @var array */
    protected array $data = [];

    /**
     * Constructor.
     *
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        foreach ($data as $value) {
            $this->add($value);
        }
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * Add an item.
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
     * Set an index with given value.
     *
     * @param  int   $index
     * @param  mixed $value
     * @return self
     */
    public function set(int $index, mixed $value): self
    {
        $this->indexCheck($index);

        // Maintain index if exceeds.
        if ($index > $count = $this->count()) {
            $index = $count;
        }

        $this->data[$index] = $value;

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
     * Remove an index if exists.
     *
     * @param  int $index
     * @return self
     */
    public function remove(int $index): self
    {
        $this->indexCheck($index);

        if ($this->hasIndex($index)) {
            $count = $this->count();

            unset($this->data[$index]);

            // Re-index if dropped index wasn't last index.
            if ($index != $count - 1) {
                $this->resetIndexes();
            }
        }

        return $this;
    }

    /**
     * Dedupe values applying unique check.
     *
     * @param  bool $strict
     * @param  bool $list
     * @return self
     */
    public function dedupe(bool $strict = true): self
    {
        $this->data = array_dedupe($this->data, $strict, list: true);

        return $this;
    }

    /**
     * Check whether given values exist.
     *
     * @param  mixed ...$values
     * @return bool
     */
    public function contains(mixed ...$values): bool
    {
        return array_contains($this->data, ...$values);
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
     * Fill tool.
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
     * Pad tool.
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
     * Slice tool.
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
     * Join tool.
     *
     * @param  string $glue
     * @return string
     */
    public function join(string $glue = ''): string
    {
        return join($glue, $this->data);
    }

    /**
     * Check whether an index value exists.
     *
     * @param  int $index
     * @return bool
     */
    public function has(int $index): bool
    {
        return isset($this->data[$index]);
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
     * Get index of given value, or return null.
     *
     * @param  mixed $value
     * @return int|null
     */
    public function indexOf(mixed $value): int|null
    {
        return array_search_key($this->data, $value);
    }

    /**
     * Get last index of given value, or return null.
     *
     * @param  mixed $value
     * @return int|null
     */
    public function lastIndexOf(mixed $value): int|null
    {
        return array_search_key($this->data, $value, last: true);
    }

    /**
     * Get first value.
     *
     * @return mixed
     */
    public function first(): mixed
    {
        return array_first($this->data);
    }

    /**
     * Get last value.
     *
     * @return mixed
     */
    public function last(): mixed
    {
        return array_last($this->data);
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
     * @inheritDoc IteratorAggregate
     */ #[\ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->data);
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
        $this->remove($index);
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
