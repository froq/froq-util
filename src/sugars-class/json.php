<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Arrayable, Jsonable};

/**
 * A static class, builds/parses JSON arrays/objects/strings safely.
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
    public const BUILD_FLAGS = JSON_PRESERVE_ZERO_FRACTION
                             | JSON_UNESCAPED_SLASHES
                             | JSON_UNESCAPED_UNICODE;

    /** Parse flags. */
    public const PARSE_FLAGS = JSON_BIGINT_AS_STRING;

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
        $flags |= static::BUILD_FLAGS;

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
        $flags |= static::PARSE_FLAGS;

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
     * Verify given input if a valid JSON.
     *
     * @param  ?string        $input
     * @param  JsonError|null &$error
     * @return bool
     */
    public static function verify(?string $input, JsonError &$error = null): bool
    {
        return json_verify($input, $error);
    }

    /**
     * Prettify given input if a valid JSON.
     *
     * @param  ?string    $input
     * @param  string|int $indent
     * @return ?string
     */
    public static function prettify(?string $input, string|int $indent = 2): ?string
    {
        return json_prettify($input, $indent);
    }

    /**
     * Normalize given input.
     *
     * @param  ?string    $input
     * @param  string|int $indent
     * @return ?string
     */
    public static function normalize(?string $input, string|int $indent = null): ?string
    {
        return json_normalize($input, $indent);
    }
}

/**
 * A dynamic class, mapped as JSON object including some utility methods.
 *
 * @package global
 * @class   JsonObject
 * @author  Kerem Güneş
 * @since   5.0
 */
class JsonObject extends stdClass implements Arrayable, Jsonable, JsonSerializable, ArrayAccess
{
    /** For accelerating `get*()` methods. */
    private static array $__ARRAY_CACHE = [];

    /**
     * Constructor
     *
     * @param object|array|null $data
     */
    public function __construct(object|array|null $data)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $value = $this->objectify($value);

                // Simply set as dynamic var (no private).
                try { $this->$key = $value; } catch (Error $e) {
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
        $id = get_object_id($this);

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
        return json_encode($this->toArray(), $flags);
    }

    /**
     * @inheritDoc JsonSerializable
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
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
     * @throws     UnimplementedError
     */
    public function offsetSet(mixed $key, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @inheritDoc ArrayAccess
     * @throws     UnimplementedError
     */
    public function offsetUnset(mixed $key): never
    {
        throw new UnimplementedError();
    }

    /**
     * Parse given JSON as static instance.
     *
     * @param  ?string $json
     * @param  ?int    $flags
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
        $id = get_object_id($this);

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

/**
 * JSON prettifier class for indenting JSON strings.
 *
 * @package global
 * @class   JsonPrettifier
 * @author  Kerem Güneş
 * @since   7.0
 */
class JsonPrettifier
{
    /**
     * Prettify.
     *
     * @param  string|Jsonable|JsonSerializable $json
     * @param  string|int                       $indent
     * @param  string                           $newLine
     * @return string
     * @throws JsonError
     * @thanks https://github.com/ergebnis/json-printer
     */
    public static function prettify(string|Jsonable|JsonSerializable $json, string|int $indent = "  ", string $newLine = "\n"): string
    {
        if ($json instanceof Jsonable) {
            $json = $json->toJson();
        } elseif ($json instanceof JsonSerializable) {
            $json = json_encode($json);
        }

        // When indentation is unavailable.
        if (!$json || !strpbrk($json, '{[')) {
            return $json;
        }

        // When indent given as size.
        if (is_int($indent)) {
            if ($indent < 0) {
                throw new JsonError('Argument $indent cannot be negative');
            }

            $indent = str_repeat(' ', $indent);
        }

        if (!preg_test('~^( +|\t+)$~', $indent)) {
            throw new JsonError('Invalid indent: %q', $indent);
        }
        if (!preg_test('~^(\r\n|\r|\n)$~', $newLine)) {
            throw new JsonError('Invalid new-line: %q', $newLine);
        }

        // Indent options.
        $indentString    = $indent;
        $indentLevel     = 0;

        // Loop variables.
        $noEscape        = true;
        $stringLiteral   = '';
        $inStringLiteral = false;

        // Indent macro, makes auto-indent by level.
        $indent = function () use ($indentString, &$indentLevel): string {
            return str_repeat($indentString, $indentLevel);
        };

        // Formatted string.
        $buffer = '';

        for ($i = 0, $il = strlen($json); $i < $il; $i++) {
            $char = $json[$i];

            // Are we inside a quoted string literal?
            if ($noEscape && $char === '"') {
                $inStringLiteral = !$inStringLiteral;
            }

            // Collect characters if we are inside a quoted string literal.
            if ($inStringLiteral) {
                $stringLiteral .= $char;
                $noEscape = ($char === '\\') ? !$noEscape : true;
                continue;
            }

            // Process string literal if we are about to leave it.
            if ($stringLiteral !== '') {
                $buffer .= $stringLiteral . $char;
                $stringLiteral = '';
                continue;
            }

            // Ignore whitespace outside of string literal.
            if ($char === ' ') {
                continue;
            }

            // Ensure space after ":" character.
            if ($char === ':') {
                $buffer .= ': ';
                continue;
            }

            // Output a new line after "," character and and indent the next line.
            if ($char === ',') {
                $buffer .= $char . $newLine . $indent();
                continue;
            }

            // Output a new line after "{" and "[" and indent the next line.
            if ($char === '{' || $char === '[') {
                $indentLevel++;

                $buffer .= $char . $newLine . $indent();
                continue;
            }

            // Output a new line after "}" and "]" and indent the next line.
            if ($char === '}' || $char === ']') {
                $indentLevel--;

                $temp = rtrim($buffer);
                $last = $temp[-1];

                // Collapse empty {} and [].
                if ($last === '{' || $last === '[') {
                    $buffer = $temp . $char;
                    continue;
                }

                $buffer .= $newLine . $indent();
            }

            $buffer .= $char;
        }

        return $buffer;
    }
}
