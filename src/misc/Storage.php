<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

/**
 * A class for storing static stuff that can used as stand-alone or
 * as a global storage.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\Storage
 * @author  Kerem Güneş
 * @since   6.0
 * @static
 */
final class Storage implements \Countable, \ArrayAccess
{
    /** @var string */
    private string $id;

    /** @var array */
    private static array $items = [];

    /**
     * Constructor.
     *
     * @param array|null $items
     */
    public function __construct(array $items = [])
    {
        $this->id = get_object_id($this);

        // Init sub array for dynamic usages.
        self::$items[$this->id] = $items;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // Clear sub array.
        unset(self::$items[$this->id]);
    }

    /**
     * Get id.
     *
     * @return array
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Get items.
     *
     * @return array
     */
    public static function items(): array
    {
        return self::$items;
    }

    /**
     * Store an item.
     *
     * @param  string|int $id
     * @param  mixed      $item
     * @return void
     */
    public static function store(string|int $id, mixed $item): void
    {
        self::$items[$id] = $item;
    }

    /**
     * Unstore an item.
     *
     * @param  string|int $id
     * @return mixed
     */
    public static function unstore(string|int $id): mixed
    {
        $item = self::$items[$id] ?? null;

        unset(self::$items[$id]);

        return $item;
    }

    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return count(self::$items[$this->id]);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $id): bool
    {
        return array_key_exists($id, self::$items[$this->id]);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $id): mixed
    {
        return self::$items[$this->id][$id] ?? null;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $id, mixed $item): void
    {
        self::$items[$this->id][$id] = $item;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $id): void
    {
        unset(self::$items[$this->id][$id]);
    }
}
