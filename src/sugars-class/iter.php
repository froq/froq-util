<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Arrayable, Listable, Jsonable};

/**
 * A simple iterator class with some utility methods.
 *
 * @package global
 * @class   Iter
 * @author  Kerem Güneş
 * @since   7.10
 */
class Iter implements Arrayable, Listable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    /** Internal iterator. */
    private ArrayIterator $iter;

    /**
     * Constructor.
     *
     * @param iterable $iter
     */
    public function __construct(iterable $iter)
    {
        // Since ArrayIterator methods are fast, such a getArrayCopy(), count() etc.,
        // we use it here to utilise for speed (CachingIterator is slow, e.g. count()).
        $this->iter = $iter = new ArrayIterator([...$iter]);
    }

    /**
     * Reset iteration or move pointer to a position if given.
     *
     * @param  int|null $offset
     * @param  bool     $throw
     * @return void
     * @throws RangeError If offset is invalid & throw is true.
     */
    public function reset(int $offset = null, bool $throw = true): void
    {
        // No need for stuff below, indeed..
        if (!$this->iter->count()) {
            return;
        }

        if ($offset === null) {
            $this->iter->rewind();
        } else {
            try {
                $this->iter->seek($offset);
            } catch (OutOfBoundsException $e) {
                // Just throw error, as default.
                $throw && throw new RangeError($e);

                [$min, $max] = $this->getMinMaxKeys();

                if ($offset <= $min) {
                    $this->iter->seek($min);
                } elseif ($offset >= $max) {
                    $this->iter->seek($max);
                }
            }
        }
    }

    /**
     * Check next item.
     *
     * @return bool
     */
    public function hasNext(): bool
    {
        return $this->iter->valid();
    }

    /**
     * Get next item, moving position as default.
     *
     * @param  bool $move
     * @return mixed
     */
    public function getNext(bool $move = true): mixed
    {
        $value = $this->iter->current();

        // Move to next item.
        $move && $this->moveNext();

        return $value;
    }

    /**
     * Get next item entry (key/value), moving position as default.
     *
     * @param  bool $move
     * @return array
     */
    public function getNextEntry(bool $move = true): array
    {
        $entry = [$this->iter->key(), $this->iter->current()];

        // Move to next item.
        $move && $this->moveNext();

        return $entry;
    }

    /**
     * Move to next item.
     *
     * Note: Method `getNext()` and `getNextEntry()` will do this move already,
     * but in case, this method can be used where manual next calls are needed.
     *
     * @return void
     */
    public function moveNext(): void
    {
        $this->iter->next();
    }

    /**
     * Append items.
     *
     * @param  mixed ...$values
     * @return self
     */
    public function append(mixed ...$values): self
    {
        foreach ($values as $value) {
            $this->iter->append($value);
        }

        return $this;
    }

    /**
     * Sort items.
     *
     * @param  callable|int|null $func
     * @param  int               $flags
     * @param  bool|null         $assoc Associative directive.
     * @param  bool              $key   Sort by key directive.
     * @return self
     */
    public function sort(callable|int $func = null, int $flags = 0, bool $assoc = null, bool $key = false): self
    {
        $this->iter = new ArrayIterator(
            sorted($this->toArray(), $func, $flags, $assoc, $key)
        );

        return $this;
    }

    /**
     * Filter items.
     *
     * @param  callable|null $func
     * @param  bool          $useKeys
     * @param  bool          $keepKeys
     * @return self
     */
    public function filter(callable $func = null, bool $useKeys = false, bool $keepKeys = true): self
    {
        $this->iter = new ArrayIterator(
            filter($this->toArray(), $func, false, $useKeys, $keepKeys)
        );

        return $this;
    }

    /**
     * Map items.
     *
     * @param  callable $func
     * @param  bool     $useKeys
     * @param  bool     $keepKeys
     * @return self
     */
    public function map(callable $func, bool $useKeys = false, bool $keepKeys = true): self
    {
        $this->iter = new ArrayIterator(
            map($this->toArray(), $func, false, $useKeys, $keepKeys)
        );

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
        return reduce($this->toArray(), $carry, $func, $right);
    }

    /**
     * Get keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->toArray());
    }

    /**
     * Get values.
     *
     * @return array
     */
    public function values(): array
    {
        return array_values($this->toArray());
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return $this->iter->getArrayCopy();
    }

    /**
     * @inheritDoc froq\common\interface\Listable
     */
    public function toList(): array
    {
        return array_list($this->toArray());
    }

    /**
     * @inheritDoc froq\common\interface\Jsonable
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this->toArray(), $flags);
    }

    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return $this->iter->count();
    }

    /**
     * @inheritDoc IteratorAggregate
     */
    public function getIterator(): Iterator
    {
        return clone $this->iter;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $key): bool
    {
        return $this->iter->offsetExists($key);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $key): mixed
    {
        return $this->iter->offsetGet($key);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->iter->offsetSet($key, $value);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $key): void
    {
        $this->iter->offsetUnset($key);
    }

    /**
     * Get min/max integer keys.
     *
     * Time/Space Complexity: O(n)/O(1) (https://afteracademy.com/blog/largest-element-in-an-array).
     */
    private function getMinMaxKeys(): array
    {
        $min = $max = 0;

        foreach ($this->keys() as $key) {
            if (is_int($key)) {
                if ($key < $min) $min = $key;
                if ($key > $max) $max = $key;
            }
        }

        return [$min, $max];
    }
}
