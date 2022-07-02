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
     * @param bool     ...$options See generate().
     */
    public function __construct(string $value = null, bool ...$options)
    {
        // Create if none given.
        $value ??= self::generate(...$options);

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
    public function toUpperString(): string
    {
        return strtoupper($this->value);
    }

    /**
     * Get Uuid value as dash-freed.
     *
     * @return string
     */
    public function toPlainString(): string
    {
        return str_replace('-', '', $this->value);
    }

    /**
     * Get Uuid value as hashed by length 16, 32, 40 or 64.
     *
     * @param  int  $length
     * @param  bool $format
     * @param  bool $upper
     * @return string
     */
    public function toHashString(int $length = 32, bool $format = false, bool $upper = false): string
    {
        return self::hash($this->value, $length, $format, $upper);
    }

    /**
     * Get Unix time if UUID was created by `withTime()` or option `timed: true`.
     *
     * @return int|null
     */
    public function getTime(): int|null
    {
        $time = null;

        if (ctype_xdigit($sub = substr($this->value, 0, 8))) {
            $time = hexdec($sub);
        }

        return ($time !== null && $time <= time()) ? $time : null;
    }

    /**
     * Format UTC time if UUID was created by `withTime()` or option `timed: true`.
     *
     * @param  string $format
     * @return string|null
     */
    public function formatTime(string $format = 'c'): string|null
    {
        $time = $this->getTime();

        return ($time !== null) ? gmdate($format, $time) : null;
    }

    /**
     * Check whether Uuid value is valid.
     *
     * @param  bool $strict
     * @return bool
     */
    public function isValid(bool $strict = true): bool
    {
        return self::validate($this->value, $strict);
    }

    /**
     * Create a Uuid instance with options.
     *
     * @param  bool ...$options See generate().
     * @return Uuid
     */
    public static function withOptions(bool ...$options): Uuid
    {
        return new Uuid(self::generate(...$options));
    }

    /**
     * Create a Uuid instance with Unix time prefix.
     *
     * @param  bool ...$options See generate().
     * @return Uuid
     */
    public static function withTime(bool ...$options): Uuid
    {
        return new Uuid(self::generate(true, ...$options));
    }

    /**
     * Generate a UUID.
     *
     * @param  bool $timed For Unix time prefix.
     * @param  bool $guid
     * @param  bool $upper
     * @param  bool $plain
     * @return string
     */
    public static function generate(bool $timed = false, bool $guid = false, bool $upper = false, bool $plain = false): string
    {
        if (!$timed) {
            // Full 16-random bytes.
            $bins = random_bytes(16);
        } else {
            // Unix time prefix & 12-random bytes.
            $bins = strrev(pack('L', time())) . random_bytes(12);
        }

        // Add signs: 4 (version) & 8, 9, A, B (variant), but GUID doesn't use them.
        if (!$guid) {
            $bins[6] = chr(ord($bins[6]) & 0x0F | 0x40); // Version.
            $bins[8] = chr(ord($bins[8]) & 0x3F | 0x80); // Variant.
        }

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bins), 4));

        $upper && $uuid = strtoupper($uuid);
        $plain && $uuid = str_replace('-', '', $uuid);

        return $uuid;
    }

    /**
     * Generate a UUID hash.
     *
     * @param  int  $length
     * @param  bool $format
     * @param  bool $upper
     * @return string
     * @causes UuidError
     */
    public static function generateHash(int $length = 32, bool $format = false, bool $upper = false): string
    {
        return self::hash(self::generate(), $length, $format, $upper);
    }

    /**
     * Validate a UUID/v4 or GUID.
     *
     * @param  string $uuid
     * @param  bool   $strict For version, variant & dashes.
     * @return bool
     */
    public static function validate(string $uuid, bool $strict = true): bool
    {
        if ($strict) {
            // With version, variant & dashes.
            return preg_test(
                '~^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[ab89][a-f0-9]{3}-[a-f0-9]{12}$~i',
                $uuid
            );
        }

        // With/without version, variant & dashes.
        return preg_test(
            '~^[a-f0-9]{8}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{12}$~i',
            $uuid
        );
    }

    /**
     * Validate a hash by length.
     *
     * @param  string $hash
     * @param  int    $length
     * @return bool
     */
    public static function validateHash(string $hash, int $length): bool
    {
        // With given length.
        return preg_test('~^[a-f0-9]{' . $length . '}$~i', $hash);
    }

    /**
     * Hash a UUID by given length.
     *
     * @param  string $uuid
     * @param  int    $length
     * @param  bool   $format
     * @param  bool   $upper
     * @return string
     * @throws UuidError
     */
    public static function hash(string $uuid, int $length, bool $format = false, bool $upper = false): string
    {
        static $algos = [16 => 'fnv1a64', 32 => 'md5', 40 => 'sha1', 64 => 'sha256'];

        if (!$algo = ($algos[$length] ?? null)) {
            throw new UuidError('Invalid length: %q [valids: 16,32,40,64]', $length);
        }

        $hash = hash($algo, $uuid);

        if ($format) {
            if (strlen($hash) != 32 || !ctype_xdigit($hash)) {
                throw new UuidError('Format option for only 32-length UUIDs/GUIDs');
            }

            $hash = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hash, 4));
        }

        $upper && $hash = strtoupper($hash);

        return $hash;
    }
}
