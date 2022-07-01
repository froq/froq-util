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
     * @param mixed    ...$options See generate().
     */
    public function __construct(string $value = null, mixed ...$options)
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
     * @return string|null
     */
    public function toHashString(int $length = 32, bool $format = false, bool $upper = false): string|null
    {
        return self::hash($this->value, $length, $format, $upper);
    }

    /**
     * Get date/time prefix if UUID was created by `withDate()`, `withTime()` or
     * with option `with: 'date' or 'time'`.
     *
     * @return int|null
     */
    public function getPrefix(): int|null
    {
        return self::decode($this->value)['prefix'];
    }

    /**
     * Get version if UUID was created by randomly (v4).
     *
     * @return int|null
     */
    public function getVersion(): int|null
    {
        return self::decode($this->value, true)['version'];
    }

    /**
     * Get date prefix if UUID was created by `withDate()` or with option `with: 'date'`.
     *
     * @return string|null
     */
    public function getDate(): string|null
    {
        $date = $this->getPrefix();

        return ($date !== null && $date <= gmdate('Ymd')) ? (string) $date : null;
    }

    /**
     * Get time prefix if UUID was created by `withTime()` or with option `with: 'time'`.
     *
     * @return int|null
     */
    public function getTime(): int|null
    {
        $time = $this->getPrefix();

        return ($time !== null && $time <= time()) ? $time : null;
    }

    /**
     * Get (UTC) date/time if UUID was created by `withTime()` or with option `with: 'time'`.
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
     * @param  mixed ...$options See generate().
     * @return Uuid
     */
    public static function withOptions(mixed ...$options): Uuid
    {
        return new Uuid(self::generate(...$options));
    }

    /**
     * Create a Uuid instance with (UTC) date prefixed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withDate(bool $guid = false): Uuid
    {
        return new Uuid(self::generate('date', $guid));
    }

    /**
     * Create a Uuid instance with (Unix) time prefixed value.
     *
     * @param  bool $guid
     * @return Uuid
     */
    public static function withTime(bool $guid = false): Uuid
    {
        return new Uuid(self::generate('time', $guid));
    }

    /**
     * Generate a UUID by given options or defaults.
     *
     * @param  string $with Prefix option, 'time' or 'date' only.
     * @param  bool   $guid
     * @param  bool   $upper
     * @param  bool   $plain
     * @return string
     * @throws UuidError
     */
    public static function generate(string $with = '', bool $guid = false, bool $upper = false, bool $plain = false): string
    {
        $bins = match ($with) {
            // Full 16-random bytes.
            '' => random_bytes(16),

            // Time prefix & 12-random bytes.
            'time' => strrev(pack('L', time())) . random_bytes(12),

            // Date prefix & 12-random bytes.
            'date' => strrev(pack('L', gmdate('Ymd'))) . random_bytes(12),

            // Invalid "with" option.
            default => throw new UuidError('Invalid "with" option: %q [valids: time,date]', $with),
        };

        // Add signs: 4 (version) & 8, 9, A, B, but GUID doesn't use them.
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
     * Decode a UUID extracting its creation date/time & optionally version.
     *
     * @param  string $uuid
     * @param  bool   $withVersion
     * @return array
     */
    public static function decode(string $uuid, bool $withVersion = false): array
    {
        $prefix = $version = null;

        if (ctype_xdigit($sub = substr($uuid, 0, 8))) {
            $prefix = hexdec($sub);
        }
        if ($withVersion && self::validate($uuid, true)) {
            $version = 4;
        }

        return ['prefix' => $prefix, 'version' => $version];
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
