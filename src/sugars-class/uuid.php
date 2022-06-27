<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A simple UUID class for working customized UUIDs.
 *
 * @package global
 * @class   Uuid
 * @author  Kerem Güneş
 * @since   6.0
 */
class Uuid implements Stringable
{
    /** Given or generated value. */
    public readonly string $value;

    /**
     * Constructor.
     *
     * @param string|null $value
     * @param bool     ...$options
     */
    public function __construct(string $value = null, bool ...$options)
    {
        // Create if none given.
        $value ??= uuid(...$options);

        $this->value = $value;
    }

    /**
     * @magic
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * Get Uuid value.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get Uuid value as upper-cased.
     *
     * @return string
     */
    public function toUpper(): string
    {
        return strtoupper($this->value);
    }

    /**
     * Get Uuid value as dash-freed.
     *
     * @return string
     */
    public function toUndash(): string
    {
        return str_replace('-', '', $this->value);
    }

    /**
     * Get Uuid value as hashed by length 32, 40, 64 or 16.
     *
     * @param  int $length
     * @return string|null
     */
    public function toHash(int $length = 32): string|null
    {
        return uuid_hash($length, format: $length == 32, uuid: $this->value);
    }

    /**
     * Create an Uuid instance with options.
     *
     * @param  bool ...$options
     * @return Uuid
     */
    public static function withOptions(bool ...$options): Uuid
    {
        return new Uuid(uuid(...$options));
    }

    /**
     * Create an Uuid instance with hashed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withHash(int $length = 32): Uuid
    {
        return new Uuid(uuid_hash($length, format: $length == 32));
    }

    /**
     * Create an Uuid instance with (UTC) date prefixed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withDate(bool $guid = false): Uuid
    {
        $bins = strrev(pack('L', gmdate('Ymd'))) . random_bytes(12);
        $guid || $bins = self::modify($bins);

        return new Uuid(uuid_format(bin2hex($bins)));
    }

    /**
     * Create an Uuid instance with (Unix) time prefixed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withTime(bool $guid = false): Uuid
    {
        $bins = strrev(pack('L', time())) . random_bytes(12);
        $guid || $bins = self::modify($bins);

        return new Uuid(uuid_format(bin2hex($bins)));
    }

    /**
     * Create an Uuid instance with HR-time prefixed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withHRTime(bool $guid = false): Uuid
    {
        $time = map(hrtime(), function ($t) {
            $t = pad((string) $t, 10, 0);
            return strrev(pack('L', $t));
        });

        $bins = join($time) . random_bytes(8);
        $guid || $bins = self::modify($bins);

        return new Uuid(uuid_format(bin2hex($bins)));
    }

    /**
     * Modify UUID bins adding signs: 4 (version) & 8, 9, A, B.
     *
     * @param  string $bins
     * @return string.
     */
    public static function modify(string $bins): string
    {
        $bins[6] = chr(ord($bins[6]) & 0x0F | 0x40);
        $bins[8] = chr(ord($bins[8]) & 0x3F | 0x80);

        return $bins;
    }

    /**
     * Decode UUID hash extracting its creation date/time.
     *
     * @param  string $uuid
     * @return int|null
     */
    public static function decode(string $uuid): int|null
    {
        $sub = substr($uuid, 0, 8);

        return ctype_xdigit($sub) ? hexdec($sub) : null;
    }
}
