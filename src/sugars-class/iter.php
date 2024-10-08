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
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        // Since ArrayIterator methods are fast, such a getArrayCopy(), count() etc.,
        // we use it here to utilise (CachingIterator is slow, e.g. count()).
        $this->iter = new ArrayIterator(iterator_to_array($data));
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
     * @return mixed|null
     */
    public function getNext(bool $move = true): mixed
    {
        $value = null;

        if ($this->iter->valid()) {
            $value = $this->iter->current();

            // Move to next item.
            $move && $this->moveNext();
        }

        return $value;
    }

    /**
     * Get next item entry (key/value), moving position as default.
     *
     * @param  bool $move
     * @return array|null
     */
    public function getNextEntry(bool $move = true): array|null
    {
        $entry = null;

        if ($this->iter->valid()) {
            $entry = [$this->iter->key(), $this->iter->current()];

            // Move to next item.
            $move && $this->moveNext();
        }

        return $entry;
    }

    /**
     * Get next item pair as refs (key/value), moving position as default.
     *
     * @param  int|string|null $key
     * @param  mixed|null      $value
     * @return bool
     */
    public function getNextPair(int|string|null &$key, mixed &$value): bool
    {
        $key = $value = null;

        if ($entry = $this->getNextEntry()) {
            [$key, $value] = $entry;

            return true;
        }

        return false;
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
     * Get current key.
     *
     * @return int|string|null
     */
    public function key(): int|string|null
    {
        return $this->iter->key();
    }

    /**
     * Get current value.
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->iter->current();
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
        // No need for stuff below.
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
                $throw && throw new RangeError($e->getMessage());

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
     * Append items.
     *
     * @param  mixed ...$items
     * @return self
     */
    public function append(mixed ...$items): self
    {
        foreach ($items as $item) {
            $this->iter->append($item);
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
     * Reverse.
     *
     * @param  bool $keepKeys
     * @return self
     */
    public function reverse(bool $keepKeys = false): self
    {
        $this->iter = new ArrayIterator(
            reverse($this->toArray(), $keepKeys)
        );

        return $this;
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
     * @permissive
     */
    public function getIterator(): ArrayIterator|Traversable
    {
        return new ArrayIterator($this->iter->getArrayCopy());
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
     * Static constructor.
     *
     * @param  mixed ...$data Map of named arguments.
     * @return static
     */
    public static function of(mixed ...$data): static
    {
        return new static($data);
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

/**
 * Split iterator class.
 *
 * @package global
 * @class   SplitIter
 * @author  Kerem Güneş
 * @since   7.10
 */
class SplitIter extends Iter
{
    /** RegExp pattern. */
    public readonly string $pattern;

    /** RegExp compile error. */
    public readonly Error|null $error;

    /**
     * Constructor.
     *
     * @param string   $pattern
     * @param string   $string
     * @param int|null $limit
     * @param int|null $flags
     * @param bool  ...$options @see RegExp.split()
     */
    public function __construct(string $pattern, string $string, int $limit = null, int $flags = null, bool ...$options)
    {
        $this->pattern = $pattern;

        try {
            parent::__construct(
                (array) RegExp::fromPattern($pattern, throw: true)
                    ->split($string, $limit ?? -1, $flags ?? 0, null, $options)
            );
            $this->error = null;
        } catch (RegExpError $e) {
            parent::__construct();
            $this->error = new Error($e->getMessage(), $e->getCode());
        }
    }
}
