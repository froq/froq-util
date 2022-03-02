<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

/**
 * Random.
 *
 * A RNG class that generates pseudorandom numbers. This class is highly
 * inspired by java.util.Random class using its some same implementations.
 * @see https://docs.oracle.com/javase/8/docs/api/java/util/Random.html
 *
 * @package froq\util\misc
 * @object  froq\util\misc\Random
 * @author  Kerem Güneş
 * @since   5.0
 */
final class Random
{
    /** @var int */
    private int $seed;

    /**
     * Constructor.
     *
     * @param int|null $seed
     */
    public function __construct(int $seed = null)
    {
        // Use milliseconds as default.
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
        if ($seed) {
            $this->seed = ($seed ^ 0x5DEECE66D) & ((1 << 48) - 1);
        }

        return $this->seed;
    }

    /**
     * Get next int.
     *
     * @param  int $bound
     * @return int
     */
    public function nextInt(int $bound = PHP_INT_MAX): int
    {
        if ($bound < 1) {
            throw new \ValueError("Min bound is 1, {$bound} given");
        }

        // i.e. bound is a power of 2.
        if (($bound & -$bound) == $bound) {
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
        return round($this->next(24) / (float) (1 << 24), $precision);
    }

    /**
     * Get next char.
     *
     * @return string
     */
    public function nextChar(): string
    {
        return $this->nextChars(1);
    }

    /**
     * Get next chars.
     *
     * @param  int $length
     * @return string
     */
    public function nextChars(int $length): string
    {
        $chars = '';

        while (strlen($chars) < $length) {
            $chars .= BASE62_ALPHABET[$this->nextInt(61)];
        }

        return $chars;
    }

    /**
     * Get next byte.
     *
     * @return string
     */
    public function nextByte(): string
    {
        return $this->nextBytes(1, true);
    }

    /**
     * Get next bytes.
     *
     * @param  int  $length
     * @param  bool $join
     * @return string|array
     */
    public function nextBytes(int $length, bool $join = true): string|array
    {
        $bytes = [];

        while (count($bytes) < $length) {
            $rands = unpack('C*', pack('L', $this->next(32)));
            $rands = array_map('chr', array_slice($rands, 1));
            $bytes = array_slice([...$bytes, ...$rands], 0, $length);
        }

        $join && $bytes = join($bytes);

        return $bytes;
    }

    /**
     * Get next number as int.
     *
     * @param  int $bits
     * @return int
     */
    private function next(int $bits): int
    {
        $this->seed = (0xB + (int) ($this->seed * 0x5DEECE66D)) & ((1 << 48) - 1);

        return (int) ($this->seed >> (48 - $bits));
    }
}
