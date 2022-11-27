<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A class for dynamic properties.
 *
 * @package global
 * @object  PlainObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class PlainObject extends stdClass
{
    /**
     * Constructor.
     *
     * @param mixed ...$properties
     */
    public function __construct(mixed ...$properties)
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * For getting properties safely.
     *
     * @param  string $name
     * @return mixed
     */
    public function &__get(string $name): mixed
    {
        return $this->$name;
    }
}

/**
 * A class for dynamic properties with array-access utility.
 *
 * @package global
 * @object  PlainArrayObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class PlainArrayObject extends PlainObject implements ArrayAccess
{
    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $name): bool
    {
        return property_exists($this, $name);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $name, mixed $value): void
    {
        $this->$name = $value;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function &offsetGet(mixed $name): mixed
    {
        return $this->$name;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $name): void
    {
        unset($this->$name);
    }
}
