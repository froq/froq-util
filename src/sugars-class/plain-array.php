<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Arrayable, Jsonable};
use froq\collection\trait\{CountTrait, KeysValuesTrait, FirstLastTrait, ToArrayTrait, ToJsonTrait};

/**
 * A class for dynamic arrays, optionally with read-only state.
 *
 * @package global
 * @class   PlainArray
 * @author  Kerem Güneş
 * @since   7.12
 */
class PlainArray implements Arrayable, Jsonable, Countable, IteratorAggregate, ArrayAccess
{
    use KeysValuesTrait, FirstLastTrait, CountTrait, ToArrayTrait, ToJsonTrait;

    /** Data holder. */
    private array $data = [];

    /** Read-only state. */
    private bool|null $readOnly;

    /**
     * Constructor.
     *
     * @param iterable  $data
     * @param bool|null $readOnly
     */
    public function __construct(iterable $data = [], bool $readOnly = null)
    {
        $this->data = [...$data];
        $this->readOnly = $readOnly;
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * Set / get read-only state.
     *
     * @param  bool|null $readOnly
     * @return bool|null
     */
    public function readOnly(bool $readOnly = null): bool|null
    {
        if (func_num_args()) {
            $this->readOnly = $readOnly;
        }

        return $this->readOnly;
    }

    /**
     * @inheritDoc IteratorAggregate
     * @permissive
     */
    public function getIterator(): Iter|Traversable
    {
        return new Iter($this->data);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function &offsetGet(mixed $key): mixed
    {
        return $this->data[$key];
    }

    /**
     * @inheritDoc ArrayAccess
     * @throws     ReadonlyError
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->readOnly && throw new ReadonlyError($this);

        $this->data[$key ?? $this->count()] = $value;
    }

    /**
     * @inheritDoc ArrayAccess
     * @throws     ReadonlyError
     */
    public function offsetUnset(mixed $key): void
    {
        $this->readOnly && throw new ReadonlyError($this);

        unset($this->data[$key]);
    }

    /**
     * Static constructor.
     *
     * @param  mixed ...$data Map of named arguments.
     * @return static
     */
    public static function of(mixed ...$data): static
    {
        return new static($data);
    }
}

/**
 * A class for dynamic arrays, with read-only state.
 *
 * @package global
 * @class   PlainReadonlyArray
 * @author  Kerem Güneş
 * @since   7.12
 */
class PlainReadonlyArray extends PlainArray
{
    /**
     * @override
     */
    public function __construct(iterable $data = [])
    {
        parent::__construct($data, true);
    }
}
