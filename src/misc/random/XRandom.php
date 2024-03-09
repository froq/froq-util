<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\util\Numbers;
use Random\{Randomizer, Engine};

/**
 * Wrapper class for `Random\Randomizer` class with some extensions.
 *
 * @package froq\util\random
 * @class   froq\util\random\XRandom
 * @author  Kerem Güneş
 * @since   7.30
 */
class XRandom
{
    /**
     * Random instance for 8.2.
     * @todo Drop as of 8.3.
     */
    private Random $random;

    /** Randomizer instance. */
    private Randomizer $randomizer;

    /**
     * Constructor.
     *
     * @param Random\Engine|null $engine
     */
    public function __construct(Engine $engine = null)
    {
        if ($this->isOldPhp()) {
            $this->random = new Random();
        }

        $this->randomizer = new Randomizer($engine);
    }

    /**
     * Get random bytes.
     *
     * @param  int         $length
     * @param  bool        $hex
     * @param  string|null $algo
     * @return string
     */
    public function getBytes(int $length, bool $hex = false, string $algo = null): string
    {
        $ret = $this->randomizer->getBytes($length);

        if ($hex) {
            $ret = bin2hex($ret);
        } elseif ($algo !== null) {
            $ret = hash($algo, $ret);
        }

        return $ret;
    }

    /**
     * Get random bytes from (given string).
     *
     * @param  int    $length
     * @param  string $from
     * @return string
     */
    public function getBytesFrom(int $length, string $from): string
    {
        return $this->isOldPhp() ? $this->random->nextChars($length, chars: $from)
             : $this->randomizer->getBytesFromString($from, $length);
    }

    /**
     * @alias getBytesFrom()
     */
    public function getBytesFromString(int $length, string $string): string
    {
        return $this->getBytesFrom($length, $string);
    }

    /**
     * Get float.
     *
     * @param  float|null $min
     * @param  float|null $max
     * @param  int|null   $precision
     * @return float
     */
    public function getFloat(float $min = null, float $max = null, int $precision = null): float
    {
        return $this->isOldPhp() ? Numbers::randomFloat($min, $max, $precision)
             : round($this->randomizer->getFloat($min ??= 0.0, $max ?? $min + 1.0), $precision ?? PRECISION);
    }

    /**
     * Get int.
     *
     * @param  int|null $min
     * @param  int|null $max
     * @return int
     */
    public function getInt(int $min = null, int $max = null): int
    {
        return $this->randomizer->getInt($min ?? 0, $max ?? PHP_INT_MAX);
    }

    /**
     * Next float.
     *
     * @return float
     */
    public function nextFloat(): float
    {
        return $this->isOldPhp() ? $this->random->nextFloat() : $this->randomizer->nextFloat();
    }

    /**
     * Next int.
     *
     * @return int
     */
    public function nextInt(): int
    {
        return $this->randomizer->nextInt();
    }

    /**
     * Pick array keys.
     *
     * @param  int   $count
     * @param  array $array
     * @return array
     */
    public function pickArrayKeys(int $count, array $array): array
    {
        if (!$array) return [];

        return $this->randomizer->pickArrayKeys($array, $count);
    }

    /**
     * Pick array values.
     *
     * @param  int   $count
     * @param  array $array
     * @return array
     */
    public function pickArrayValues(int $count, array $array): array
    {
        if (!$array) return [];

        $keys = $this->randomizer->pickArrayKeys($array, $count);

        foreach ($keys as $key) {
            $ret[] = $array[$key];
        }

        return $ret;
    }

    /**
     * Shuffle array.
     *
     * @param  array $array
     * @return array
     */
    public function shuffleArray(array $array): array
    {
        return $this->randomizer->shuffleArray($array);
    }

    /**
     * Shuffle bytes.
     *
     * @param  string $bytes
     * @return string
     */
    public function shuffleBytes(string $bytes): string
    {
        return $this->randomizer->shuffleBytes($bytes);
    }

    /**
     * Shuffle string.
     *
     * @param  string      $string
     * @param  string|null $encoding
     * @return string
     */
    public function shuffleString(string $string, string $encoding = null): string
    {
        return join($this->shuffleArray(mb_str_split($string, 1, $encoding)));
    }

    /**
     * Refresh randomizer property.
     *
     * @return void
     */
    public function refresh(): void
    {
        $this->randomizer = new Randomizer();
    }

    /**
     * Get randomizer property.
     *
     * @return Random\Randomizer
     */
    public function randomizer(): Randomizer
    {
        return $this->randomizer;
    }

    /**
     * Tentative version checker.
     *
     * @todo Drop as of 8.3.
     */
    private function isOldPhp(): bool
    {
        return PHP_VERSION_ID < 80300;
    }
}
