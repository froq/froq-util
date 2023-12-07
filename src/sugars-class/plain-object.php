<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\Arrayable;

/**
 * A class for dynamic properties.
 *
 * @package global
 * @class   PlainObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class PlainObject extends stdClass implements Arrayable, IteratorAggregate
{
    /**
     * Constructor.
     *
     * @param mixed ...$properties Map of named arguments.
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

    /**
     * Get list of vars.
     *
     * @return array
     */
    public function getVars(): array
    {
        return array_values($this->toArray());
    }

    /**
     * Get list of var names.
     *
     * @return array
     */
    public function getVarNames(): array
    {
        return array_keys($this->toArray());
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return (array) $this;
    }

    /**
     * @inheritDoc IteratorAggregate
     */
    public function getIterator(): Traversable&Generator
    {
        foreach ($this->toArray() as $name => $value) {
            yield $name => $value;
        }
    }
}

/**
 * A class for dynamic properties with array utilities.
 *
 * @package global
 * @class   PlainArrayObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class PlainArrayObject extends PlainObject implements Countable, ArrayAccess
{
    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return count($this->toArray());
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $name): bool
    {
        return isset($this->$name);
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
