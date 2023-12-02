<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\Stringable;

/**
 * A simple UUID (v4) class for working customized or time-prefixed UUIDs/GUIDs,
 * and pseudo-random identifiers from Base-62 Alphabet.
 *
 * @package global
 * @class   Uuid
 * @author  Kerem Güneş
 * @since   6.0
 */
class Uuid implements Stringable, \Stringable
{
    /** Nulls. */
    public final const NULL = '00000000-0000-0000-0000-000000000000',
                       NULL_HASH = '00000000000000000000000000000000';

    /** Given or generated value. */
    public readonly string $value;

    /**
     * Constructor.
     *
     * @param  string|Uuid|null $value
     * @param  bool          ...$options See generate().
     * @throws UuidError If options.strict is true and value is invalid.
     */
    public function __construct(string|Uuid $value = null, bool ...$options)
    {
        // When value given.
        if (func_num_args() && isset($options['strict'])) {
            if ($options['strict'] && !self::validate((string) $value)) {
                [$spec, $value] = ($value === null) ? ['%s', 'null']
                                                    : ['%q', $value];

                throw new UuidError('Invalid UUID value: ' . $spec, $value);
            }

            // Not used in generate().
            unset($options['strict']);
        }

        // Create if none given.
        $value ??= self::generate(...$options);

        $this->value = (string) $value;
    }

    /**
     * @magic
     */
    public function __toString(): string
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
     * Get Uuid value as dash-freed.
     *
     * @return string
     */
    public function toHashString(): string
    {
        return str_replace('-', '', $this->value);
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
     * Get Uuid value as 20/22-length short ID in Base-62.
     * Chances: 22-length: 88%, 21-length: 10%, 20-length: 02%.
     *
     * Note: Argument `$pad` can be used for fixed 22-length returns.
     *
     * @param  string|true|null $pad True for padding with "0".
     * @return string
     * @throws UuidError
     */
    public function toShortString(string|true $pad = null): string
    {
        $ret = convert_base($this->toHashString(), 16, 62);

        if ($pad !== null) {
            $pad = ($pad === true) ? '0' : $pad;

            if ($pad === '') {
                throw new UuidError('Argument $pad cannot be empty string');
            }

            $ret = str_pad($ret, 22, $pad);
        }

        return $ret;
    }

    /**
     * Get Unix time if UUID was created by `withTime()` or option `time: true`.
     *
     * @return int|null
     */
    public function getTime(): int|null
    {
        $time = null;

        // Extract usable part from value (8-byte hex).
        if (ctype_xdigit($sub = strcut($this->value, 8))) {
            $time = hexdec($sub);
        }

        // Validate extracted time.
        if ($time !== null && $time <= time()) {
            return $time;
        }

        return null;
    }

    /**
     * Get DateTime instance if UUID was created by `withTime()` or option `time: true`.
     *
     * @param  string $timezone
     * @return DateTime|null
     */
    public function getDateTime(string $timezone = 'UTC'): DateTime|null
    {
        $time = $this->getTime();

        if ($time !== null) {
            return (new DateTime)
                ->setTimestamp($time)
                ->setTimezone(new DateTimeZone($timezone));
        }

        return null;
    }

    /**
     * Check null value.
     *
     * @return bool
     */
    public function isNull(): bool
    {
        return hash_equals(self::NULL, $this->value);
    }

    /**
     * Check null-hash value.
     *
     * @return bool
     */
    public function isNullHash(): bool
    {
        return hash_equals(self::NULL_HASH, $this->value);
    }

    /**
     * Check whether given Uuid is equal to this value.
     *
     * @param  string|Uuid $uuid
     * @return bool
     */
    public function isEqual(string|Uuid $uuid): bool
    {
        return self::equals($this->value, (string) $uuid);
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
     * Generate a UUID (v4).
     *
     * @param  bool $time For Unix time prefix.
     * @param  bool $guid
     * @param  bool $hash
     * @param  bool $upper
     * @return string
     */
    public static function generate(bool $time = false, bool $guid = false, bool $hash = false, bool $upper = false): string
    {
        if (!$time) {
            $bins = random_bytes(16);
        } else {
            // Unix time prefix & 12-random bytes.
            // @tome: What about "2038 Problem" issue? Seems pack() will keep giving 4-byte bin
            // (so 8-byte hex) until date of "2105-12-31", but probably I'll never see that date.
            $bins = strrev(pack('L', time())) . random_bytes(12);
        }

        // Add signs: 4 (version) & 8, 9, A, B (variant), but GUID doesn't use them.
        if (!$guid) {
            $bins[6] = chr(ord($bins[6]) & 0x0F | 0x40); // Version.
            $bins[8] = chr(ord($bins[8]) & 0x3F | 0x80); // Variant.
        }

        $uuid = self::format(bin2hex($bins));

        $hash  && $uuid = str_replace('-', '', $uuid);
        $upper && $uuid = strtoupper($uuid);

        return $uuid;
    }

    /**
     * Generate a GUID.
     *
     * @param  bool $time
     * @param  bool $hash
     * @param  bool $upper
     * @return string
     */
    public static function generateGuid(bool $time = false, bool $hash = false, bool $upper = false): string
    {
        return self::generate($time, true, $hash, $upper);
    }

    /**
     * Generate a simple UID.
     *
     * @param  int $length
     * @param  int $base
     * @return string
     * @throws UuidError
     */
    public static function generateSuid(int $length, int $base = 62): string
    {
        if ($length < 1) {
            throw new UuidError('Invalid length: %s [min=1]', $length);
        } elseif ($base < 2 || $base > 62) {
            throw new UuidError('Invalid base: %s [min=2, max=62]', $base);
        }

        $max = $base - 1;
        $ret = '';

        while ($length--) {
            $ret .= BASE62_ALPHABET[random_int(0, $max)];
        }

        return $ret;
    }

    /**
     * Verify equal states of given Uuid inputs.
     *
     * @param  string $uuidKnown
     * @param  string $uuidUnknown
     * @return bool
     */
    public static function equals(string $uuidKnown, string $uuidUnknown): bool
    {
        return hash_equals($uuidKnown, $uuidUnknown);
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
     * Format given hash.
     *
     * @param  string $hash
     * @return string
     * @throws UuidError
     */
    public static function format(string $hash): string
    {
        if (strlen($hash) !== 32 || !ctype_xdigit($hash)) {
            throw new UuidError('Format for only 32-length UUIDs/GUIDs');
        }

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hash, 4));
    }
}
