<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\storage;

use froq\common\interface\Arrayable;

/**
 * An extended `SplObjectStorage` class.
 *
 * @package froq\util\storage
 * @class   froq\util\storage\ObjectStorage
 * @author  Kerem Güneş
 * @since   6.0
 */
class ObjectStorage extends \SplObjectStorage implements Arrayable
{
    /**
     * Constructor.
     *
     * @param object ...$objects
     * @missing
     */
    public function __construct(object ...$objects)
    {
        foreach ($objects as $object) {
            $this->attach($object);
        }
    }

    /**
     * Store an object, optionally with info.
     *
     * @param  object     $object
     * @param  mixed|null $info
     * @return void
     */
    public function store(object $object, mixed $info = null): void
    {
        $this->attach($object, $info);
    }

    /**
     * Unstore an object.
     *
     * @param  object $object
     * @return void
     */
    public function unstore(object $object): void
    {
        $this->detach($object);
    }

    /**
     * Empty storage.
     *
     * @return void
     */
    public function empty(): void
    {
        $this->removeAll($this);

        // @cancel
        // foreach ($this->toArray() as $object) {
        //     $this->detach($object);
        // }
    }

    /**
     * Get an item by given index if stored.
     *
     * @note   O(n)
     * @param  int $index
     * @return object|null
     */
    public function get(int $index): object|null
    {
        foreach ($this->toArray() as $i => $object) {
            if ($i === $index) return $object;
        }
        return null;
    }

    /**
     * Get id.
     *
     * @param  object $object
     * @return int|string
     */
    public function getId(object $object): int|string
    {
        return get_object_id($object, with_name: false);
    }

    /**
     * Get id with name.
     *
     * @param  object $object
     * @return string
     */
    public function getNamedId(object $object): string
    {
        return get_object_id($object, with_name: true);
    }

    /**
     * Get hash with name.
     *
     * @param  object $object
     * @param  bool   $withRehash
     * @return string
     */
    public function getNamedHash(object $object, bool $withRehash = false): string
    {
        return get_object_hash($object, with_name: true, with_rehash: $withRehash);
    }

    /**
     * Get hash with serialize.
     *
     * @param  object $object
     * @param  bool   $withName
     * @return string
     */
    public function getSerializedHash(object $object, bool $withName = true): string
    {
        return get_object_hash($object, with_name: $withName, serialized: true);
    }

    /**
     * Permissive, no `UnexpectedValueException` throws.
     *
     * @override
     */
    public function offsetGet(mixed $object): mixed
    {
        if (parent::offsetExists($object)) {
            return parent::offsetGet($object);
        }
        return null;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }
}
