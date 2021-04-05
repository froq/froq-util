<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

use froq\util\{UtilException, misc\Json};
use froq\common\interface\{Arrayable, Jsonable};
use JsonSerializable, ArrayAccess;

/**
 * Json Object.
 *
 * Represents a dynamic entity which is mapped as JSON object including some utility methods.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\JsonObject
 * @author  Kerem Güneş
 * @since   5.0
 */
class JsonObject implements Arrayable, Jsonable, JsonSerializable, ArrayAccess
{
    /**
     * Cache of array copy that indexed by object ID & filled on first get()/getAll() call.
     */
    private static array $__JSON_OBJECT_CACHE;

    /**
     * Constructor
     *
     * @param string|object $data
     */
    public function __construct(string|object $data)
    {
        if (is_string($data)) {
            $data = Json::parseObject($data);
        }

        if ($data != null) {
            foreach ($data as $key => $value) {
                if (is_object($value)) {
                    $value = new static($value);
                }
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Get a value by given key (or path with "." notation).
     *
     * @param  string|array $key
     * @param  any|null     $default
     * @return any|null
     */
    public final function get(string $key, $default = null)
    {
        // Should really use cache?
        $data = self::$__JSON_OBJECT_CACHE[$this->id()] ??= $this->toArray();

        return array_fetch($data, $key, $default);
    }

    /**
     * Get values by given keys (or paths with "." notation).
     *
     * @param  array<string> $keys
     * @param  any|null      $default
     * @return any|null
     */
    public final function getAll(array $key, $default = null)
    {
        // Should really use cache?
        $data = self::$__JSON_OBJECT_CACHE[$this->id()] ??= $this->toArray();

        return array_fetch($data, $key, $default);
    }

    /**
     * Get a value as int.
     *
     * @param  string   $key
     * @param  any|null $default
     * @return int
     */
    public final function getInt(string $key, $default = null): int
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Get a value as float.
     *
     * @param  string   $key
     * @param  any|null $default
     * @return float
     */
    public final function getFloat(string $key, $default = null): float
    {
        return (float) $this->get($key, $default);
    }

    /**
     * Get a value as string.
     *
     * @param  string   $key
     * @param  any|null $default
     * @return string
     */
    public final function getString(string $key, $default = null): string
    {
        return (string) $this->get($key, $default);
    }

    /**
     * Get a value as bool.
     *
     * @param  string   $key
     * @param  any|null $default
     * @return bool
     */
    public final function getBool(string $key, $default = null): bool
    {
        return (bool) $this->get($key, $default);
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(bool $deep = true): array
    {
        $ret = [];

        foreach ($this as $key => $value) {
            if ($deep && $value instanceof $this) {
                $value = $value->toArray(true);
            }
            $ret[$key] = $value;
        }

        return $ret;
    }

    /**
     * @inheritDoc froq\common\interface\Jsonable
     */
    public function toJson(int $flags = 0): string
    {
        return (string) json_encode($this, $flags);
    }

    /**
     * @inheritDoc JsonSerializable
     */
    public function jsonSerialize(): static
    {
        return $this;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists($key)
    {
        return isset($this->{$key});
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /** @notImplemented. */
    public function offsetSet($key, $value) {}
    public function offsetUnset($key) {}

    /** @internal */
    private function id(): string
    {
        return $this::class .'#'. spl_object_id($this);
    }
}
