<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Json.
 *
 * A static class which builds/parses JSON arrays/objects/strings safely.
 *
 * @package froq\util
 * @object  Json
 * @author  Kerem Güneş
 * @since   5.0
 */
class Json extends \StaticClass
{
    /** Types. */
    public final const ARRAY = 1, OBJECT = 2;

    /** Build flags. */
    public final const BUILD_FLAGS = JSON_UNESCAPED_UNICODE
                                   | JSON_UNESCAPED_SLASHES
                                   | JSON_PRESERVE_ZERO_FRACTION;

    /** Parse flags. */
    public final const PARSE_FLAGS = JSON_BIGINT_AS_STRING;

    /**
     * Build a JSON string.
     *
     * @param  mixed $data
     * @param  ?int  $type
     * @param  ?int  $flags
     * @return ?string
     */
    public static function build(mixed $data, ?int $type = 0, ?int $flags = 0): ?string
    {
        if ($type) {
            switch ($type) {
                case self::ARRAY:
                    $data = (array) $data;
                    $data = array_values($data); // Fix keys & use values.
                    break;
                case self::OBJECT:
                    $data = (object) $data;
                    break;
            }
        }

        // Add default flags.
        $flags |= self::BUILD_FLAGS;

        $out = json_encode($data, flags: $flags);

        return ($out !== false) ? $out : null;
    }

    /**
     * Build arrayified JSON string.
     *
     * @param  any  $data
     * @param  ?int $flags
     * @return ?string
     */
    public static function buildArray($data, ?int $flags = 0): ?string
    {
        return self::build($data, self::ARRAY, $flags);
    }

    /**
     * Build objectified JSON string.
     *
     * @param  any  $data
     * @param  ?int $flags
     * @return ?string
     */
    public static function buildObject($data, ?int $flags = 0): ?string
    {
        return self::build($data, self::OBJECT, $flags);
    }

    /**
     * Build prettified JSON string.
     *
     * @param  any  $data
     * @param  ?int $type
     * @param  ?int $flags
     * @return ?string
     */
    public static function buildPretty($data, ?int $type = 0, ?int $flags = 0): ?string
    {
        return self::build($data, $type, ($flags |= JSON_PRETTY_PRINT));
    }

    /**
     * Parse given JSON string.
     *
     * @param  ?string $json
     * @param  ?int    $type
     * @param  ?int    $flags
     * @return any
     */
    public static function parse(?string $json, ?int $type = 0, ?int $flags = 0)
    {
        $json = (string) $json;

        // Add default flags.
        $flags |= self::PARSE_FLAGS;

        if ($type) {
            switch ($type) {
                case self::ARRAY:
                    return (array) json_decode($json, true, flags: $flags);
                case self::OBJECT:
                    return (object) json_decode($json, false, flags: $flags);
            }
        }

        // Normal decode process.
        return json_decode($json, flags: $flags);
    }

    /**
     * Parse given JSON string as array.
     *
     * @param  ?string $json
     * @param  ?int    $flags
     * @return array
     */
    public static function parseArray(?string $json, ?int $flags = 0): array
    {
        return self::parse($json, self::ARRAY, $flags);
    }

    /**
     * Parse given JSON string as object.
     *
     * @param  ?string $json
     * @param  ?int    $flags
     * @return object
     */
    public static function parseObject(?string $json, ?int $flags = 0): object
    {
        return self::parse($json, self::OBJECT, $flags);
    }

    /**
     * Parse given JSON string as JsonObject.
     *
     * @param  ?string $json
     * @param  ?int    $flags
     * @return JsonObject
     * @since  6.0
     */
    public static function parseJsonObject(?string $json, ?int $flags = 0): JsonObject
    {
        return JsonObject::parse($json, $flags);
    }

    /**
     * Check whether given input is a JSON array.
     *
     * @param  ?string $input
     * @return bool
     */
    public static function isArray(?string $input): bool
    {
        return self::detectType($input) == self::ARRAY;
    }

    /**
     * Check whether given input is a JSON object.
     *
     * @param  ?string $input
     * @return bool
     */
    public static function isObject(?string $input): bool
    {
        return self::detectType($input) == self::OBJECT;
    }

    /**
     * Check whether given input is a JSON struct.
     *
     * @param  ?string $input
     * @return bool
     */
    public static function isStruct(?string $input): bool
    {
        return ($type = self::detectType($input))
            && ($type == self::ARRAY || $type == self::OBJECT);
    }

    /**
     * Detect given input type (array/object).
     *
     * @param  ?string $input
     * @return ?int
     * @since  6.0
     */
    public static function detectType(?string $input): ?int
    {

        $wrap = ($input && isset($input[0], $input[-1]))
              ? ($input[0] . $input[-1])
              : null;

        return match (true) {
            $wrap == '[]' => self::ARRAY,
            $wrap == '{}' => self::OBJECT,
            default       => null
        };
    }
}

use froq\common\interface\{Arrayable, Jsonable};

/**
 * Json Object.
 *
 * A dynamic class which is mapped as JSON object including some utility methods.
 *
 * @package froq\util
 * @object  JsonObject
 * @author  Kerem Güneş
 * @since   5.0
 */
#[AllowDynamicProperties] // 8.2
class JsonObject implements Arrayable, Jsonable, JsonSerializable, ArrayAccess
{
    /**
     * Constructor
     *
     * @param  array|object|null $data
     */
    public function __construct(array|object|null $data)
    {
        if ($data && (is_array($data) || is_object($data))) {
            foreach ($data as $key => $value) {
                // Convert objects to JsonObject when available.
                $value = $this->objectify($value);

                // Simply set as dynamic var (no private).
                try { $this->{$key} = $value; } catch (Error) {
                    trigger_error(sprintf('Cannot set private property %s::$s', static::class, $key));
                }
            }
        }
    }

    /**
     * Get a value by given key (or path with "." notation).
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed|null
     */
    public final function get(string $key, mixed $default = null): mixed
    {
        $data = $this->arrayify();

        return array_fetch($data, $key, $default);
    }

    /**
     * Get values by given keys (or paths with "." notation).
     *
     * @param  array<string> $keys
     * @param  mixed|null    $default
     * @return mixed|null
     */
    public final function getAll(array $keys, mixed $default = null): mixed
    {
        $data = $this->arrayify();

        return array_fetch($data, $keys, $default);
    }

    /**
     * Get a value as int.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return int
     */
    public final function getInt(string $key, mixed $default = null): int
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Get a value as float.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return float
     */
    public final function getFloat(string $key, mixed $default = null): float
    {
        return (float) $this->get($key, $default);
    }

    /**
     * Get a value as string.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return string
     */
    public final function getString(string $key, mixed $default = null): string
    {
        return (string) $this->get($key, $default);
    }

    /**
     * Get a value as bool.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return bool
     */
    public final function getBool(string $key, mixed $default = null): bool
    {
        return (bool) $this->get($key, $default);
    }

    /** @inheritDoc froq\common\interface\Arrayable */
    public function toArray(bool $deep = true): array
    {
        $ret = [];

        foreach ($this as $key => $value) {
            if ($deep && $value instanceof self) {
                $value = $value->toArray(true);
            }
            $ret[$key] = $value;
        }

        return $ret;
    }

    /** @inheritDoc froq\common\interface\Jsonable */
    public function toJson(int $flags = 0): string
    {
        return (string) json_encode($this, $flags);
    }

    /** @inheritDoc JsonSerializable */
    public function jsonSerialize(): static
    {
        return $this;
    }

    /** @inheritDoc ArrayAccess */
    public function offsetExists(mixed $key): bool
    {
        return property_exists($this, $key);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetGet(mixed $key): mixed
    {
        return property_exists($this, $key) ? $this->$key : null;
    }

    /**
     * Block mutations.
     *
     * @throws JsonError
     * @inheritDoc ArrayAccess
     * @notImplemented
     */
    public function offsetSet(mixed $key, mixed $value): never
    {
        throw new JsonError('Not implemented');
    }

    /**
     * Block mutations.
     *
     * @throws JsonError
     * @inheritDoc ArrayAccess
     * @notImplemented
     */
    public function offsetUnset(mixed $key): never
    {
        throw new JsonError('Not implemented');
    }

    /**
     * Parse given JSON as static instance.
     *
     * @param  string|null $json
     * @param  int|null    $flags
     * @throws JsonError
     * @since  6.0
     */
    public static function parse(string|null $json, int|null $flags = 0): static
    {
        $data = null;

        if ($json !== null) {
            $type = Json::detectType($json);
            if ($type != Json::ARRAY && $type != Json::OBJECT) {
                throw new JsonError('Invalid JSON, it must be a valid JSON struct');
            }

            $data = Json::parse($json, $type, $flags);
            if ($error = json_error_message()) {
                throw new JsonError('Invalid JSON [error: '. strtolower($error) .']');
            }
        }

        return new static($data);
    }

    /**
     * Get JsonObject data as array.
     */
    private function arrayify(): array
    {
        return $this->toArray();
    }

    /**
     * Convert objects only to JsonObject, also map arrays as objects.
     */
    private function objectify(mixed $input): mixed
    {
        if (is_object($input)) {
            $input = new static($input);
        } elseif (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->objectify($value);
            }
        }

        return $input;
    }
}