<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Objects;

/**
 * X-Object.
 *
 * A class for playing with objects OOP-way.
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
     * Is instance of.
     *
     * @param  object|string $object
     * @return bool
     */
    public function isInstanceOf(object|string $object): bool
    {
        return ($this->object instanceof $object);
    }

    /**
     * Is equal to.
     *
     * @param  object $object
     * @param  bool   $strict
     * @return bool
     */
    public function isEqualTo(object $object, bool $strict = true): bool
    {
        return $strict ? ($this->object === $object) : ($this->object == $object);
    }
}
