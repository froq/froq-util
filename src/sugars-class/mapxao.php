<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\interface\{Arrayable, Objectable, Jsonable};
use froq\collection\trait\GetTrait;
use froq\util\Arrays;

/**
 * X-Array Object.
 *
 * A class just like ArrayObject but "a bit" extended, basically designed
 * for Options & Attributes classes.
 *
 * @package froq\util
 * @object  XArrayObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class XArrayObject extends ArrayObject implements Arrayable, Objectable, Jsonable
{
    use GetTrait;

    /**
     * Constructor
     *
     * @param array|self|null $data
     */
    public function __construct(array|self $data = null)
    {
        parent::__construct($data, parent::ARRAY_AS_PROPS);
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Check whether a key exists.
     *
     * @param  string|int $key
     * @return bool
     */
    public function has(string|int $key): bool
    {
        return parent::offsetExists($key);
    }

    /**
     * Set a key with given value.
     *
     * @param  string|int $key
     * @param  mixed      $value
     * @return self
     */
    public function set(string|int $key, mixed $value): self
    {
        parent::offsetSet($key, $value);

        return $this;
    }

    /**
     * Get a key value if key exists.
     *
     * @param  string|int $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function get(string|int $key, mixed $default = null): mixed
    {
        return parent::offsetExists($key) ? parent::offsetGet($key) : $default;
    }

    /**
     * Remove a key if exists.
     *
     * @param  string|int $key
     * @return self
     */
    public function remove(string|int $key): self
    {
        parent::offsetUnset($key);

        return $this;
    }

    /**
     * Set data.
     *
     * @param  array|self $data
     * @return self
     */
    public function setData(array|self $data): self
    {
        $this->exchangeArray((array) $data);

        return $this;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Update current data with given data.
     *
     * @param  array|self $data
     * @return self
     */
    public function updateData(array|self $data): self
    {
        $this->setData(array_replace_recursive($this->toArray(), (array) $data));

        return $this;
    }

    /**
     * Each.
     *
     * @param  callable $func
     * @return void
     */
    public function each(callable $func): void
    {
        foreach ($this as $key => $value) {
            $func($value, $key);
        }
    }

    /**
     * Filter.
     *
     * @param  callable|null $func
     * @param  bool          $recursive
     * @param  bool          $useKeys
     * @return self
     */
    public function filter(callable $func = null, bool $recursive = false, bool $useKeys = false): self
    {
        $this->setData(Arrays::filter(
            $this->toArray($recursive),
            $func, $recursive, $useKeys,
        ));

        return $this;
    }

    /**
     * Filter keys.
     *
     * @param  callable $func
     * @param  bool     $recursive
     * @return self
     */
    public function filterKeys(callable $func, bool $recursive = false): self
    {
        $this->setData(Arrays::filterKeys(
            $this->toArray($recursive),
            $func, $recursive
        ));

        return $this;
    }

    /**
     * Map.
     *
     * @param  callable|string $func
     * @param  bool            $recursive
     * @param  bool            $useKeys
     * @return self
     */
    public function map(callable|string $func, bool $recursive = false, bool $useKeys = false): self
    {
        $this->setData(Arrays::map(
            $this->toArray($recursive),
            $func, $recursive, $useKeys,
        ));

        return $this;
    }

    /**
     * Map keys.
     *
     * @param  callable|string $func
     * @param  bool            $recursive
     * @return self
     */
    public function mapKeys(callable|string $func, bool $recursive = false): self
    {
        $this->setData(Arrays::mapKeys(
            $this->toArray($recursive),
            $func, $recursive
        ));

        return $this;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(bool $deep = true): array
    {
        $ret = $this->getArrayCopy();

        if ($deep) {
            foreach ($ret as $key => &$value) {
                if ($value instanceof self) {
                    $value = $value->toArray(true);
                }
            }
        }

        return $ret;
    }

    /**
     * @inheritDoc froq\common\interface\Objectable
     */
    public function toObject(bool $deep = true): object
    {
        $ret = $this->getArrayCopy();

        if ($deep) {
            foreach ($ret as $key => &$value) {
                if ($value instanceof self) {
                    $value = $value->toObject(true);
                }
            }
        }

        return (object) $ret;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this->getArrayCopy(), $flags);
    }

    /**
     * @override
     */
    public function offsetGet(mixed $key): mixed
    {
        return $this->get($key);
    }
}
