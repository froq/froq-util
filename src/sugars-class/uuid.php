<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A simple UUID/v4 class for working customized UUIDs.
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
     * Get date/time prefix if UUID was created by `withDate()`, `withTime()`
     * or with option `timed: true`.
     *
     * @return int|null
     */
    public function getPrefix(): int|null
    {
        return self::decodePrefix($this->value);
    }

    /**
     * Get date/time prefix if UUID was created by `withDate()`.
     *
     * @return string|null
     */
    public function getDate(): string|null
    {
        $date = $this->getPrefix();

        return ($date !== null && $date <= gmdate('Ymd')) ? (string) $date : null;
    }

    /**
     * Get time prefix if UUID was created by `withTime()` or with option `timed: true`.
     *
     * @return int|null
     */
    public function getTime(): int|null
    {
        $time = $this->getPrefix();

        return ($time !== null && $time <= time()) ? $time : null;
    }

    /**
     * Get (UTC) datetime if UUID was created by `withTime()` or with option `timed: true`.
     *
     * @param  string $format
     * @return string|null
     */
    public function getDateTime(string $format = 'c'): string|null
    {
        $time = $this->getPrefix();

        return ($time !== null && $time <= time()) ? gmdate($format, $time) : null;
    }

    /**
     * Create a Uuid instance with options.
     *
     * @param  bool ...$options
     * @return Uuid
     */
    public static function withOptions(bool ...$options): Uuid
    {
        return new Uuid(uuid(...$options));
    }

    /**
     * Create a Uuid instance with hashed value.
     *
     * @param  int $length
     * @return Uuid
     */
    public static function withHash(int $length = 32): Uuid
    {
        return new Uuid(uuid_hash($length, format: $length == 32));
    }

    /**
     * Create a Uuid instance with (UTC) date prefixed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withDate(bool $guid = false): Uuid
    {
        $bins = strrev(pack('L', gmdate('Ymd'))) . random_bytes(12);
        $guid || $bins = self::applyProps($bins);

        return new Uuid(uuid_format(bin2hex($bins)));
    }

    /**
     * Create a Uuid instance with (Unix) time prefixed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withTime(bool $guid = false): Uuid
    {
        $bins = strrev(pack('L', time())) . random_bytes(12);
        $guid || $bins = self::applyProps($bins);

        return new Uuid(uuid_format(bin2hex($bins)));
    }

    /**
     * Create a Uuid instance with HR-time prefixed value.
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
        $guid || $bins = self::applyProps($bins);

        return new Uuid(uuid_format(bin2hex($bins)));
    }

    /**
     * Generate a UUID/v4 or GUID.
     *
     * @param  string $uuid
     * @param  bool   $strict
     * @return bool
     */
    public static function generate(bool ...$options): string
    {
        return uuid(...$options);
    }

    /**
     * Validate a UUID/v4 or GUID.
     *
     * @param  string $uuid
     * @param  bool   $strict
     * @return bool
     */
    public static function validate(string $uuid, bool $strict = true): bool
    {
        // With version & dashes.
        if ($strict) {
            return preg_test(
                '~^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[ab89][a-f0-9]{3}-[a-f0-9]{12}$~',
                $uuid
            );
        }

        // With/without version & dash-free (uuid/guid).
        return preg_test(
            '~^[a-f0-9]{8}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{12}$~',
            $uuid
        );
    }

    /**
     * Apply version (4) and variant (8, 9, a or b) props to given UUID binary string.
     *
     * @param  string $bins
     * @return string
     */
    public static function applyProps(string $bins): string
    {
        $bins[6] = chr(ord($bins[6]) & 0x0F | 0x40); // Version.
        $bins[8] = chr(ord($bins[8]) & 0x3F | 0x80); // Variant.

        return $bins;
    }

    /**
     * Decode UUID hash extracting its creation date/time.
     *
     * @param  string $uuid
     * @return int|null
     */
    public static function decodePrefix(string $uuid): int|null
    {
        $sub = substr($uuid, 0, 8);

        return ctype_xdigit($sub) ? hexdec($sub) : null;
    }
}
