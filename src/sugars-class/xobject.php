<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\Objects;

/**
 * A class for playing with objects in OOP-way.
 *
 * @package global
 * @class   XObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class XObject extends XClass
{
    /** Target object. */
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
     * Clone.
     *
     * @return object
     */
    public function clone(): object
    {
        return clone $this->object;
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
     * @param  bool $withName
     * @return string
     */
    public function getSerializedHash(bool $withName = true): string
    {
        return Objects::getSerializedHash($this->object, $withName);
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
        return Objects::getSerializedHash($this->object) === Objects::getSerializedHash($object);
    }

    /**
     * @override
     */
    public function getVars(): array
    {
        return Objects::getVars($this->object);
    }

    /**
     * @override
     */
    public function getVarNames(): array
    {
        return Objects::getVars($this->object, true);
    }

    /**
     * @override
     */
    public function getProperties(bool $namesOnly = false, bool $assoc = true): array
    {
        return get_class_properties($this->object, false, $namesOnly, $assoc);
    }

    /**
     * @override
     */
    public function reflect(bool $extended = false)
        : ReflectionObject|XReflectionObject|null
    {
        try {
            return !$extended ? new ReflectionObject($this->object)
                : new XReflectionObject($this->object);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * @override
     */
    public function reflectConstant(string $name, bool $extended = false)
        : ReflectionClassConstant|XReflectionClassConstant|null
    {
        try {
            return !$extended ? new ReflectionClassConstant($this->object, $name)
                : new XReflectionClassConstant($this->object, $name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * @override
     */
    public function reflectProperty(string $name, bool $extended = false)
        : ReflectionProperty|XReflectionProperty|null
    {
        try {
            return !$extended ? new ReflectionProperty($this->object, $name)
                : new XReflectionProperty($this->object, $name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * @override
     */
    public function reflectMethod(string $name, bool $extended = false)
        : ReflectionMethod|XReflectionMethod|null
    {
        try {
            return !$extended ? new ReflectionMethod($this->object, $name)
                : new XReflectionMethod($this->object, $name);
        } catch (ReflectionException) {
            return null;
        }
    }
}
