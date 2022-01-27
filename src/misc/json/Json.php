<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc\json;

/**
 * Json.
 *
 * Represents a static entity which builds/parses JSON strings safely.
 *
 * @package froq\util\misc\json
 * @object  froq\util\misc\json\Json
 * @author  Kerem Güneş
 * @since   5.0
 */
class Json extends \StaticClass
{
    /** Types. */
    public final const ARRAY = 1, OBJECT = 2;

    /** Build flags. */
    public final const FLAGS = JSON_UNESCAPED_UNICODE
                             | JSON_UNESCAPED_SLASHES
                             | JSON_PRESERVE_ZERO_FRACTION;

    /**
     * Build a JSON string.
     *
     * @param  any  $data
     * @param  ?int $type
     * @param  ?int $flags
     * @return ?string
     */
    public static function build($data, ?int $type = 0, ?int $flags = 0): ?string
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

        // Add defaults.
        $flags |= self::FLAGS;

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
        [$json, $flags] = [(string) $json, (int) $flags];

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
     * Check whether given input is a JSON struct.
     *
     * @param  ?string $in
     * @return bool
     */
    public static function isStruct(?string $in): bool
    {
        return isset($in[0], $in[-1]) && (
            ($in[0] . $in[-1]) === '[]' || ($in[0] . $in[-1]) === '{}'
        );
    }

    /**
     * Check whether given input is a JSON array.
     *
     * @param  ?string $in
     * @return bool
     */
    public static function isArray(?string $in): bool
    {
        return isset($in[0], $in[-1]) && (
            ($in[0] . $in[-1]) === '[]'
        );
    }

    /**
     * Check whether given input is a JSON object.
     *
     * @param  ?string $in
     * @return bool
     */
    public static function isObject(?string $in): bool
    {
        return isset($in[0], $in[-1]) && (
            ($in[0] . $in[-1]) === '{}'
        );
    }
}
