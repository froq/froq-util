<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A static class which builds/parses JSON arrays/objects/strings safely.
 *
 * @package global
 * @class   Json
 * @author  Kerem Güneş
 * @since   5.0
 */
class Json extends StaticClass
{
    /** Types. */
    public final const ARRAY = 1, OBJECT = 2;

    /** Build flags. */
    public final const BUILD_FLAGS = JSON_PRESERVE_ZERO_FRACTION
                                   | JSON_UNESCAPED_SLASHES
                                   | JSON_UNESCAPED_UNICODE;

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
     * @param  mixed $data
     * @param  ?int  $flags
     * @return ?string
     */
    public static function buildArray(mixed $data, ?int $flags = 0): ?string
    {
        return self::build($data, self::ARRAY, $flags);
    }

    /**
     * Build objectified JSON string.
     *
     * @param  mixed $data
     * @param  ?int  $flags
     * @return ?string
     */
    public static function buildObject(mixed $data, ?int $flags = 0): ?string
    {
        return self::build($data, self::OBJECT, $flags);
    }

    /**
     * Build prettified JSON string.
     *
     * @param  mixed $data
     * @param  ?int  $type
     * @param  ?int  $flags
     * @return ?string
     */
    public static function buildPretty(mixed $data, ?int $type = 0, ?int $flags = 0): ?string
    {
        return self::build($data, $type, ($flags |= JSON_PRETTY_PRINT));
    }

    /**
     * Parse given JSON string.
     *
     * @param  ?string $json
     * @param  ?int    $type
     * @param  ?int    $flags
     * @return mixed
     */
    public static function parse(?string $json, ?int $type = 0, ?int $flags = 0): mixed
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
        return self::detectType($input) === self::ARRAY;
    }

    /**
     * Check whether given input is a JSON object.
     *
     * @param  ?string $input
     * @return bool
     */
    public static function isObject(?string $input): bool
    {
        return self::detectType($input) === self::OBJECT;
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
            && ($type === self::ARRAY || $type === self::OBJECT);
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
            $wrap === '[]' => self::ARRAY,
            $wrap === '{}' => self::OBJECT,
            default        => null
        };
    }

    /**
     * Validate given input as JSON. @todo Use "json_validate()" function.
     *
     * @param  string|null     $input
     * @param  JsonError|null &$error
     * @return bool
     * @since  6.0
     */
    public static function validate(?string $input, JsonError &$error = null): bool
    {
        $error = $code = $message = null;

        // If '' or null input.
        if ($input === null) {
            $message = 'Empty/null input given';
        } else {
            json_decode($input);
            $message = json_error_message($code, clear: true);
        }

        // If $error was passed on call.
        if ($message && func_num_args() > 1) {
            $error = new JsonError($message, code: $code);
        }

        return ($message === null);
    }
}

use froq\common\interface\{Arrayable, Jsonable};

/**
 * A dynamic class which is mapped as JSON object including some utility methods.
 *
 * @package global
 * @class   JsonObject
 * @author  Kerem Güneş
 * @since   5.0
 */
class JsonObject extends PlainObject implements Arrayable, Jsonable, JsonSerializable, ArrayAccess
{
    /**
     * Array cache, for accelerating `get*()` methods.
     *
     * @var array
     */
    private static array $__ARRAY_CACHE = [];

    /**
     * Constructor
     *
     * @param array|object|null $data
     */
    public function __construct(array|object|null $data)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                // Convert objects to JsonObject.
                $value = $this->objectify($value);

                // Simply set as dynamic var (no private).
                try { $this->{$key} = $value; } catch (Error $e) {
                    trigger_error(format(
                        'Cannot change property %S::$%s [error: %S]',
                        $this::class, $key, $e->getMessage()
                    ));
                }
            }
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $id = spl_object_id($this);

        // Drop this object from cache.
        unset(self::$__ARRAY_CACHE[$id]);
    }

    /**
     * Get a value by given key (or path with "." notation).
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $data = $this->arrayify();

        return array_get($data, $key, $default);
    }

    /**
     * Get values by given keys (or paths with "." notation).
     *
     * @param  array<string> $keys
     * @param  array|null    $defaults
     * @return array
     */
    public function getAll(array $keys, array $defaults = null): array
    {
        $data = $this->arrayify();

        return array_get_all($data, $keys, $defaults);
    }

    /**
     * Get a value as int.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return int
     */
    public function getInt(string $key, mixed $default = null): int
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
    public function getFloat(string $key, mixed $default = null): float
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
    public function getString(string $key, mixed $default = null): string
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
    public function getBool(string $key, mixed $default = null): bool
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
            if ($deep && $value instanceof Arrayable) {
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
        return json_encode($this->toArray(true), $flags);
    }

    /**
     * @inheritDoc JsonSerializable
     */
    public function jsonSerialize(): array
    {
        return $this->toArray(true);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $key): bool
    {
        return property_exists($this, $key);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $key): mixed
    {
        return property_exists($this, $key) ? $this->$key : null;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $key, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $key): never
    {
        throw new UnimplementedError();
    }

    /**
     * Parse given JSON as static instance.
     *
     * @param  string|null $json
     * @param  int|null    $flags
     * @return static
     * @throws JsonError
     * @since  6.0
     */
    public static function parse(?string $json, ?int $flags = 0): static
    {
        $data = null;

        if ($json !== null) {
            $type = Json::detectType($json);
            if ($type !== Json::ARRAY && $type !== Json::OBJECT) {
                throw new JsonError('Given input must be a valid JSON struct');
            }

            $data = Json::parse($json, $type, $flags);
            if ($error = json_error_message()) {
                throw new JsonError($error);
            }
        }

        return new static($data);
    }

    /**
     * Get JsonObject data as array.
     */
    private function arrayify(): array
    {
        $id = spl_object_id($this);

        // This object is read-only, so caching seems ok.
        return self::$__ARRAY_CACHE[$id] ??= $this->toArray();
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
