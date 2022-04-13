<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Objects;

/**
 * A class for playing with objects in OOP-way.
 *
 * @package froq\util
 * @object  XObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class XObject extends XClass
{
    /** @var object */
    public readonly object $object;

    /**
     * Constructor.
     *
     * @param object $object
     */
    public function __construct(object $object)
    {
        $this->object = $object;

        parent::__construct($object);
    }

    /**
     * Get vars.
     *
     * @return array
     * @override
     */
    public function getVars(): array
    {
        return get_object_vars($this->object);
    }

    /**
     * Get id.
     *
     * @param  bool $withName
     * @return string
     */
    public function getId(bool $withName = true): string
    {
        return Objects::getId($this->object, $withName);
    }

    /**
     * Get hash.
     *
     * @param  bool $withName
     * @param  bool $withRehash
     * @return string
     */
    public function getHash(bool $withName = true, bool $withRehash = false): string
    {
        return Objects::getHash($this->object, $withName, $withRehash);
    }

    /**
     * Get serialized hash.
     *
     * @return string
     */
    public function getSerializedHash(): string
    {
        return Objects::getSerializedHash($this->object);
    }

    /**
     * Instance-of checker.
     *
     * @param  object|string $object
     * @return bool
     */
    public function isInstanceOf(object|string $object): bool
    {
        return ($this->object instanceof $object);
    }

    /**
     * Equal-of checker.
     *
     * @param  object $object
     * @param  bool   $strict
     * @return bool
     */
    public function isEqualOf(object $object, bool $strict = true): bool
    {
        return ($strict ? $this->object === $object : $this->object == $object);
    }

    /**
     * Equal-hash-of checker.
     *
     * @param  object $object
     * @return bool
     */
    public function isEqualHashOf(object $object): bool
    {
        return Objects::getSerializedHash($this->object) == Objects::getSerializedHash($object);
    }
}
