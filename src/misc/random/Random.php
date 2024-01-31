<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

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
     * @param  int $bound
     * @return int
     * @throws ArgumentError
     */
    public function nextInt(int $bound = PHP_INT_MAX): int
    {
        if ($bound < 1) {
            throw new \ArgumentError('Invalid bound %s [min=1]', $bound);
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
     * @param  int $length
     * @param  int $base
     * @return string
     * @throws ArgumentError
     */
    public function nextChars(int $length, int $base = 62): string
    {
        if ($length < 1) {
            throw new \ArgumentError('Invalid length %s [min=1]', $length);
        } elseif ($base < 2 || $base > 62) {
            throw new \ArgumentError('Invalid base %s [min=2, max=62]', $base);
        }

        $chars = strcut(BASE62_ALPHABET, $base);
        $bound = strlen($chars) + 1;

        $ret = '';

        while ($length--) {
            $ret .= $chars[$this->nextInt($bound) - 1];
        }

        return $ret;
    }

    /**
     * Get next byte.
     *
     * @param  bool $hex
     * @return string
     */
    public function nextByte(bool $hex = false): string
    {
        return $this->nextBytes(1, $hex);
    }

    /**
     * Get next bytes.
     *
     * @param  int  $length
     * @param  bool $hex
     * @return string|array
     * @throws ArgumentError
     */
    public function nextBytes(int $length, bool $hex = false): string|array
    {
        if ($length < 1) {
            throw new \ArgumentError('Invalid length %s [min=1]', $length);
        }

        $ret = random_bytes($length);

        if ($hex) {
            $ret = bin2hex($ret);
        }

        return $ret;
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
        $rr = new \Random\Randomizer();

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
        $rr = new \Random\Randomizer();

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
    private function next(int $bits): int
    {
        $this->seed = (0xB + (int) ($this->seed * 0x5DEECE66D)) & ((1 << 48) - 1);

        return (int) ($this->seed >> (48 - $bits));
    }
}