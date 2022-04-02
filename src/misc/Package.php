<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

use froq\common\trait\{DataAccessMagicTrait, DataAccessTrait};
use froq\collection\trait\CountTrait;

/**
 * A class for packaging stuff dynamically.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\Package
 * @author  Kerem Güneş
 * @since   6.0
 */
class Package implements \Countable, \ArrayAccess
{
    /** For using access magic & offset methods. */
    use DataAccessMagicTrait, DataAccessTrait;

    use CountTrait;

    /** @var array */
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
     * Unstore an item.
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
}
