<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\util\Arrays;

/**
 * A simple class, holds a random array as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomArray
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomArray implements \Countable, \IteratorAggregate
{
    /** Data holder. */
    public readonly array $data;

    /**
     * Constructor.
     *
     * @param array $data
     * @param bool  $shuffle
     */
    public function __construct(array $data, bool $shuffle = false)
    {
        $this->data = $shuffle ? Arrays::shuffle($data) : $data;
    }

    /**
     * Get data length.
     *
     * @return int
     */
    public function length(): int
    {
        return count($this->data);
    }

    /**
     * Get data as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Pick one item / many items.
     *
     * @param  int  $limit
     * @param  bool $pack
     * @return mixed
     */
    public function pick(int $limit, bool $pack = false): mixed
    {
        // Issue: readonly ref.
        $data = $this->data;

        return Arrays::random($data, $limit, $pack);
    }

    /**
     * Pick one item.
     *
     * @param  bool $pack
     * @return mixed
     */
    public function pickOne(bool $pack = false): mixed
    {
        return $this->pick(1, $pack);
    }

    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return $this->length();
    }

    /**
     * @inheritDoc IteratorAggregate
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->toArray() as $i => $item) {
            yield $i => $item;
        }
    }

    /**
     * Static initializer.
     *
     * @param  array $data
     * @param  bool  $shuffle
     * @return static
     */
    public static function from(array $data, bool $shuffle = false): static
    {
        return new static($data, $shuffle);
    }
}
