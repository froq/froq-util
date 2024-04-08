<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\Stringable;
use froq\util\random\Random;

/**
 * A simple UUID (v4) class for working customized or time-prefixed UUIDs/GUIDs,
 * and pseudo-random identifiers generated in Base-62 Alphabet.
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
                [$spec, $value] = ($value === null) ? ['%s', 'null'] : ['%q', $value];

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
     * Get this Uuid value.
     *
     * @param  int|null $base
     * @return string
     */
    public function toString(int $base = null): string
    {
        return ($base === null) ? $this->value
             : convert_base($this->toHashString(), 16, $base);
    }

    /**
     * Get this Uuid value as dash-freed.
     *
     * @param  string|null $algo
     * @return string
     */
    public function toHashString(string $algo = null): string
    {
        return ($algo === null) ? str_remove($this->value, '-')
             : hash($algo, str_remove($this->value, ''));
    }

    /**
     * Get this Uuid value as 20/22-length short ID in Base-62.
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
        $ret = $this->toString(62);

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
        if (ctype_xdigit($cut = strcut($this->value, 8))) {
            $time = hexdec($cut);
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
     * @param  string|null $zone
     * @return DateTime|null
     */
    public function getDateTime(string $zone = null): DateTime|null
    {
        $time = $this->getTime();

        if ($time !== null) {
            $datetime = new DateTime('', new DateTimeZone('UTC'));
            $datetime->setTimestamp($time);

            if ($zone !== null) {
                $datetime->setTimezone(new DateTimeZone($zone));
            }

            return $datetime;
        }

        return null;
    }

    /**
     * Format time if UUID was created by `withTime()` or option `time: true`.
     *
     * @param  string      $format
     * @param  string|null $zone
     * @return string|null
     */
    public function formatTime(string $format, string $zone = null): string|null
    {
        return $this->getDateTime($zone)?->format($format);
    }

    /**
     * Check null value.
     *
     * @return bool
     */
    public function isNull(): bool
    {
        return self::equals(self::NULL, $this->value);
    }

    /**
     * Check null-hash value.
     *
     * @return bool
     */
    public function isNullHash(): bool
    {
        return self::equals(self::NULL_HASH, $this->value);
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
            // @tome: What about "2038 Problem" issue? Seems pack() will keep giving 4-byte bins
            // (so 8-byte hexes) until then (2105-12-31), but probably I'll never see that date.
            $bins = strrev(pack('L', time())) . random_bytes(12);
        }

        // Add signs: version(4) / variant(8,9,a,b), GUID doesn't use.
        if (!$guid) {
            $bins[6] = chr(ord($bins[6]) & 0x0F | 0x40); // Version.
            $bins[8] = chr(ord($bins[8]) & 0x3F | 0x80); // Variant.
        }

        $uuid = self::format(bin2hex($bins));

        $hash  && $uuid = str_remove($uuid, '-');
        $upper && $uuid = str_upper($uuid);

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
     * Generate a simple UID (using Base-62 alphabet).
     *
     * @param  int $length Random characters length.
     * @param  int $base
     * @return string
     * @throws UuidError
     */
    public static function generateSuid(int $length, int $base = 62): string
    {
        try {
            return (new Random)->nextChars($length, $base);
        } catch (Throwable $e) {
            throw new UuidError($e);
        }
    }

    /**
     * Generate a random hash (using random_bytes() internally).
     *
     * @param  int    $length Random bytes length.
     * @param  string $algo
     * @return string
     * @throws UuidError
     */
    public static function generateHash(int $length, string $algo = 'md5'): string
    {
        try {
            return (new Random)->nextBytes($length, false, $algo);
        } catch (Throwable $e) {
            throw new UuidError($e);
        }
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
        // Do a "timing-attack-safe" comparison.
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
