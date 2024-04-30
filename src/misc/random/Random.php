<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use Random\Randomizer;
use Uuid;

/**
 * An RNG class that generates pseudorandom numbers, chars & bytes. This class is
 * highly inspired by java.util.Random class and uses some same implementations.
 * @see https://docs.oracle.com/javase/8/docs/api/java/util/Random.html
 *
 * @package froq\util\random
 * @class   froq\util\random\Random
 * @author  Kerem Güneş
 * @since   5.0
 */
class Random
{
    /** Seed. */
    private int $seed;

    /**
     * Constructor.
     *
     * @param int|null $seed
     */
    public function __construct(int $seed = null)
    {
        $seed ??= ustime();

        $this->seed($seed);
    }

    /**
     * Set/get seed.
     *
     * @param  int|null $seed
     * @return int
     */
    public function seed(int $seed = null): int
    {
        if ($seed !== null) {
            $this->seed = ($seed ^ 0x5DEECE66D) & ((1 << 48) - 1);
        }

        return $this->seed;
    }

    /**
     * Get next int.
     *
     * @param  int $bound Value of getrandmax().
     * @return int
     * @throws froq\util\random\RandomException
     */
    public function nextInt(int $bound = INT_MAX_32): int
    {
        if ($bound < 1) {
            throw RandomException::forInvalidBound($bound);
        }

        // i.e. bound is a power of 2.
        if (($bound & -$bound) === $bound) {
            return (int) (($bound * $this->next(31)) >> 31);
        }

        do {
            $bits = $this->next(31);
            $next = $bits % $bound;
        } while (($bits - $next) + ($bound - 1) < 0);

        return (int) $next;
    }

    /**
     * Get next bigint.
     *
     * @return int
     */
    public function nextBigInt(): int
    {
        return abs($this->next(32) << 32) + $this->next(32);
    }

    /**
     * Get next float.
     *
     * @param  int $precision
     * @return float
     */
    public function nextFloat(int $precision = PRECISION): float
    {
        return round($this->next(24) / (1 << 24), $precision);
    }

    /**
     * Get next char.
     *
     * @param  int $base
     * @return string
     */
    public function nextChar(int $base = 62): string
    {
        return $this->nextChars(1, $base);
    }

    /**
     * Get next chars.
     *
     * @param  int         $length
     * @param  int         $base
     * @param  string|null $chars
     * @return string
     * @throws froq\util\random\RandomException
     */
    public function nextChars(int $length, int $base = 62, string $chars = null): string
    {
        if ($length < 1) {
            throw RandomException::forInvalidLength($length);
        } elseif ($base < 2 || $base > 62) {
            throw RandomException::forInvalidBase($base);
        }

        if ($chars !== null) {
            $chars = trim($chars) ?: throw RandomException::forEmptyChars();
        } else {
            $chars = strcut(BASE62_ALPHABET, $base);
        }

        $ret = '';
        $max = strlen($chars) - 1;

        while ($length--) {
            $ret .= $chars[random_int(0, $max)];
        }

        return $ret;
    }

    /**
     * Get next byte.
     *
     * @param  bool            $hex
     * @param  string|int|null $algo
     * @return string
     */
    public function nextByte(bool $hex = false, string|int $algo = null): string
    {
        return $this->nextBytes(1, $hex, $algo);
    }

    /**
     * Get next bytes.
     *
     * @param  int             $length
     * @param  bool            $hex
     * @param  string|int|null $algo
     * @return string|array
     * @throws froq\util\random\RandomException
     */
    public function nextBytes(int $length, bool $hex = false, string|int $algo = null): string|array
    {
        if ($length < 1) {
            throw new RandomException('Invalid length %s [min=1]', $length);
        }

        $ret = random_bytes($length);

        if ($hex) {
            $ret = bin2hex($ret);
        } elseif ($algo !== null) {
            $base = null;
            if (is_int($algo)) {
                [$base, $algo] = [$algo, 'md5'];
            }

            $ret = hash($algo, $ret);
            if ($base && $base <> 16) {
                $ret = convert_base($ret, 16, $base);
            }
        }

        return $ret;
    }

    /**
     * Get next UUID with Unix time prefix.
     *
     * @param  bool $hash
     * @param  bool $upper
     * @return Uuid
     */
    public function nextUuid(bool $hash = false, bool $upper = false): Uuid
    {
        return Uuid::withOptions(time: true, hash: $hash, upper: $upper);
    }

    /**
     * Get next GUID with Unix time prefix.
     *
     * @param  bool $hash
     * @param  bool $upper
     * @return Uuid
     */
    public function nextGuid(bool $hash = false, bool $upper = false): Uuid
    {
        return Uuid::withOptions(time: true, guid: true, hash: $hash, upper: $upper);
    }

    /**
     * Shuffle array.
     *
     * @param  array $array
     * @return array
     * @since  7.0
     */
    public static function shuffleArray(array $array): array
    {
        $rr = new Randomizer();

        return $rr->shuffleArray($array);
    }

    /**
     * Shuffle string (unicode-safe).
     *
     * @param  string      $string
     * @param  string|null $encoding
     * @return string
     * @since  7.0
     */
    public static function shuffleString(string $string, string $encoding = null): string
    {
        $rr = new Randomizer();

        return join($rr->shuffleArray(mb_str_split($string, 1, $encoding)));
    }

    /**
     * Get next number.
     *
     * Note about 48:
     * When $bits = 8, returns <= 255.
     * When $bits = 16, returns <= 65535.
     * When $bits = 32, returns <= 4294967295 (2147483647 * 2 + 1).
     */
    protected function next(int $bits): int
    {
        $this->seed = (0xB + (int) ($this->seed * 0x5DEECE66D)) & ((1 << 48) - 1);

        return (int) ($this->seed >> (48 - $bits));
    }
}
