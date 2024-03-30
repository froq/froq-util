<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\storage;

use froq\common\interface\Arrayable;
use froq\common\trait\DataAccessMagicOffsetTrait;
use Iter, Traversable;

/**
 * A class for storing data dynamically or statically, and can be used
 * as a stand-alone or global storage.
 *
 * Note: For dynamic usage, access magic or offset methods must be used,
 * otherwise store/unstore methods can be used.
 *
 * @package froq\util\storage
 * @class   froq\util\storage\Storage
 * @author  Kerem Güneş
 * @since   6.0
 */
class Storage implements Arrayable, \Countable, \ArrayAccess, \IteratorAggregate
{
    use DataAccessMagicOffsetTrait;

    /** Instance ID. */
    protected string $id;

    /** Storage data. */
    protected static array $data = [];

    /**
     * Constructor.
     *
     * @param mixed ...$data
     */
    public function __construct(mixed ...$data)
    {
        $this->id = get_object_id($this);

        // Init subarray for dynamic usages.
        self::$data[$this->id] = $data;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // Clear subarray.
        unset(self::$data[$this->id]);
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
     * Get all items.
     *
     * @return array
     */
    public static function items(): array
    {
        return self::$data;
    }

    /**
     * Get an item.
     *
     * @param  string|int $key
     * @return mixed
     */
    public static function item(string|int $key): mixed
    {
        return self::$data[$key] ?? null;
    }

    /**
     * Store an item.
     *
     * @param  string|int $key
     * @param  mixed      $item
     * @return void
     */
    public static function store(string|int $key, mixed $item): void
    {
        self::$data[$key] = $item;
    }

    /**
     * Unstore an item.
     *
     * @param  string|int $key
     * @param  mixed|null $default
     * @return mixed
     */
    public static function unstore(string|int $key, mixed $default = null): mixed
    {
        $item = self::$data[$key] ?? $default;

        unset(self::$data[$key]);

        return $item;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return self::$data[$this->id];
    }

    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return count(self::$data[$this->id]);
    }

    /**
     * @inheritDoc IteratorAggregate
     * @permissive
     */
    public function getIterator(): Iter|Traversable
    {
        return new Iter(self::$data[$this->id]);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $key): bool
    {
        return isset(self::$data[$this->id][$key]);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function &offsetGet(mixed $key): mixed
    {
        return self::$data[$this->id][$key];
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $key, mixed $item): void
    {
        self::$data[$this->id][$key] = $item;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $key): void
    {
        unset(self::$data[$this->id][$key]);
    }
}
