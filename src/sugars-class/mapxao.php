<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

use froq\common\interface\{Arrayable, Objectable, Jsonable};
use froq\collection\trait\GetTrait;
use froq\util\Arrays;

/**
 * A class just like ArrayObject but "a bit" extended, basically designed
 * for Options & Attributes classes.
 *
 * @package global
 * @class   XArrayObject
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
        parent::__construct((array) $data, parent::ARRAY_AS_PROPS);
    }

    /**
     * @magic
     */
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
        return $this->offsetExists($key);
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
     * @return mixed|null
     */
    public function &get(string|int $key, mixed $default = null): mixed
    {
        // Prevent recursions in loops with calls like, because of ref (&):
        // foreach ($array as $i => $value) { $next = $arr[$i+1]; }
        // if (!$this->offsetExists($key)) {
        //     return $default;
        // }

        /** @thanks https://php.net/arrayobject#125849 */
        $iter = $this->getIterator();
        if ($iter->offsetExists($key)) {
            $value =& $iter[$key];
        } else {
            $value =& $default;
        }

        return $value;
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
     * Search a key for given value.
     *
     * @param  mixed  $value
     * @param  bool   $strict
     * @param  bool   $last
     * @return string|int|null
     */
    public function search(mixed $value, bool $strict = true, bool $last = false): string|int|null
    {
        return array_search_key($this->getArrayCopy(), $value, $strict, $last);
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
     * Keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * Values.
     *
     * @return array
     */
    public function values(): array
    {
        return array_values($this->getArrayCopy());
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
     * Empty.
     *
     * @return void
     */
    public function empty(): void
    {
        $this->setData([]);
    }

    /**
     * Sort.
     *
     * @param  int|null $func
     * @param  int      $flags
     * @return self
     */
    public function sort(callable|int $func = null, int $flags = 0): self
    {
        $this->setData(Arrays::sort(
            $this->toArray(false),
            $func, $flags, assoc: true
        ));

        return $this;
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

        if ($deep) foreach ($ret as $key => &$value) {
            if ($value instanceof Arrayable) {
                $value = $value->toArray(true);
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

        if ($deep) foreach ($ret as $key => &$value) {
            if ($value instanceof Objectable) {
                $value = $value->toObject(true);
            }
        }

        return (object) $ret;
    }

    /**
     * @inheritDoc froq\common\interface\Jsonable
     */
    public function toJson(int $flags = 0): string
    {
        return (string) json_encode($this->toArray(true), $flags);
    }

    /**
     * @override
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    /**
     * @override
     */
    public function &offsetGet(mixed $key): mixed
    {
        return $this->get($key);
    }
}
