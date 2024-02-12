<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\{Numbers, Strings};

/**
 * Generate a random number.
 *
 * @param  int|float|null $min
 * @param  int|float|null $max
 * @param  int|null       $precision
 * @return int|float
 */
function random(int|float $min = null, int|float $max = null, int $precision = null): int|float
{
    return Numbers::random($min, $max, $precision);
}

/**
 * Generate a random float, optionally with precision.
 *
 * @param  float|null $min
 * @param  float|null $max
 * @param  int|null   $precision
 * @return float
 */
function random_float(float $min = null, float $max = null, int $precision = null): float
{
    return Numbers::randomFloat($min, $max, $precision);
}

/**
 * Generate a random string, optionally puncted.
 *
 * @param  int  $length
 * @param  bool $puncted
 * @return string
 */
function random_string(int $length, bool $puncted = false): string
{
    return Strings::random($length, $puncted);
}

/**
 * Generate a random range by given length.
 *
 * Note: This function is slow when `$length` is high and `$unique` is true.
 *
 * @param  int            $length
 * @param  int|float|null $min
 * @param  int|float|null $max
 * @param  int|null       $precision
 * @param  bool           $unique
 * @return array
 * @throws ArgumentError
 * @tofix  Optimise unique range performance (for large lengths).
 */
function random_range(int $length, int|float $min = null, int|float $max = null, int $precision = null, bool $unique = true): array
{
    if ($length < 0) {
        throw new ArgumentError('Negative length given');
    }

    $ret = [];

    // Unique stack.
    $uni = [];

    while ($length--) {
        $item = Numbers::random($min, $max, $precision);

        // Provide unique-ness.
        while ($unique && in_array($item, $ret, true) && !in_array($item, $uni, true)) {
            $item = $uni[] = Numbers::random($min, $max, $precision);
        }

        $ret[] = $item;
    }

    return $ret;
}

/**
 * Random int with optional min/max params.
 *
 * @param  int|null $min
 * @param  int|null $max
 * @return int
 * @throws ArgumentError
 */
function random_xint(int $min = null, int $max = null): int
{
    $min ??= 0;
    $max ??= PHP_INT_MAX;

    if ($min > $max) {
        throw new ArgumentError('Argument $min must be less than argument $max');
    }

    return random_int($min, $max);
}

/**
 * Random hexed-bytes with fixed length as given.
 *
 * @param  int $length
 * @return string
 * @throws ArgumentError
 */
function random_xbytes(int $length): string
{
    if ($length < 1) {
        throw new ArgumentError('Argument $length must be greater than 0');
    }

    if ($length === 1) {
        $ret = bin2hex(random_bytes($length));
    } else {
        $ret = bin2hex(random_bytes(intdiv($length, 2)));

        while ($length > strlen($ret)) {
            $ret .= random_xbytes(2);
        }
    }

    return strcut($ret, $length);
}
