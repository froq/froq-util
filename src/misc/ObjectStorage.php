<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

use froq\common\interface\Arrayable;

/**
 * An extended SplObjectStorage class.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\ObjectStorage
 * @author  Kerem Güneş
 * @since   6.0
 */
class ObjectStorage extends \SplObjectStorage implements Arrayable
{
    /**
     * Store an object with its info.
     *
     * @param  object $object
     * @param  mixed  $info
     * @return void
     */
    public function store(object $object, mixed $info): void
    {
        parent::attach($object, $info);
    }

    /**
     * Unstore an object.
     *
     * @param  object $object
     * @return void
     */
    public function unstore(object $object): void
    {
        parent::detach($object);
    }

    /**
     * Get id.
     *
     * @param  object $object
     * @return string
     */
    public function getId(object $object): string
    {
        return get_object_id($object, false);
    }

    /**
     * Get id with name.
     *
     * @param  object $object
     * @return string
     */
    public function getNamedId(object $object): string
    {
        return get_object_id($object, true);
    }

    /**
     * Get hash with name.
     *
     * @param  object $object
     * @return string
     */
    public function getNamedHash(object $object): string
    {
        return get_class($object) .'#'. parent::getHash($object);
    }

    /**
     * Get hash with serialize.
     *
     * @param  object $object
     * @return string
     */
    public function getSerializedHash(object $object): string
    {
        return get_class($object) .'#'. get_object_hash($object, serialized: true);
    }

    /**
     * Permissive offset getter, not throws `UnexpectedValueException`.
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
