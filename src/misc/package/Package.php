<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\package;

use froq\common\interface\Arrayable;
use froq\common\trait\{DataAccessMagicTrait, DataAccessTrait};
use Iter, Traversable;

/**
 * A class for packaging stuff dynamically.
 *
 * @package froq\util\package
 * @class   froq\util\package\Package
 * @author  Kerem Güneş
 * @since   6.0
 */
class Package implements Arrayable, \Countable, \ArrayAccess, \IteratorAggregate
{
    use DataAccessMagicTrait, DataAccessTrait;

    /** Package data. */
    private array $data = [];

    /**
     * Constructor.
     *
     * @param mixed ...$data
     */
    public function __construct(mixed ...$data)
    {
        $this->data = $data;
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
     * Get an item.
     *
     * @param  string|int $key
     * @return mixed
     */
    public function item(string|int $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Pack an item.
     *
     * @param  string|int $key
     * @param  mixed      $item
     * @return void
     */
    public function pack(string|int $key, mixed $item): void
    {
        $this->data[$key] = $item;
    }

    /**
     * Unpack an item.
     *
     * @param  string|int $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function unpack(string|int $key, mixed $default = null): mixed
    {
        $item = $this->data[$key] ?? $default;

        unset($this->data[$key]);

        return $item;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @inheritDoc IteratorAggregate
     * @permissive
     */
    public function getIterator(): Iter|Traversable
    {
        return new Iter($this->data);
    }
}
